<?php

namespace App\AuthorizeNet\Managers;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\AuthorizeNet\DTOs\TransactionResponseDTO;
use App\AuthorizeNet\Exceptions\AuthorizeNetException;
use Illuminate\Support\Facades\Log;

class AuthorizeNetManager
{
    protected AnetAPI\MerchantAuthenticationType $merchantAuth;
    protected string $endpoint;

    public function __construct()
    {
        $this->initializeMerchantAuth();
        $this->setEndpoint();
    }

    protected function initializeMerchantAuth(): void
    {
        $this->merchantAuth = new AnetAPI\MerchantAuthenticationType();
        $this->merchantAuth->setName(config('lunar.authorizenet.api_login_id'));
        $this->merchantAuth->setTransactionKey(config('lunar.authorizenet.transaction_key'));
    }

    protected function setEndpoint(): void
    {
        $this->endpoint = config('lunar.authorizenet.environment') === 'production'
            ? \net\authorize\api\constants\ANetEnvironment::PRODUCTION
            : \net\authorize\api\constants\ANetEnvironment::SANDBOX;
    }

    public function getMerchantAuth(): AnetAPI\MerchantAuthenticationType
    {
        return $this->merchantAuth;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    // ========== CUSTOMER PROFILE (CIM) METHODS ==========

    public function createCustomerProfile(string $merchantCustomerId, string $email, ?string $description = null): AnetAPI\CreateCustomerProfileResponse
    {
        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setMerchantCustomerId($merchantCustomerId);
        $customerProfile->setEmail($email);
        if ($description) {
            $customerProfile->setDescription($description);
        }

        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setProfile($customerProfile);

        $controller = new AnetController\CreateCustomerProfileController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        $this->logResponse('createCustomerProfile', $response);

        return $response;
    }

    public function getCustomerProfile(string $profileId): ?AnetAPI\GetCustomerProfileResponse
    {
        $request = new AnetAPI\GetCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setCustomerProfileId($profileId);

        $controller = new AnetController\GetCustomerProfileController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        return $response;
    }

    public function deleteCustomerProfile(string $profileId): bool
    {
        $request = new AnetAPI\DeleteCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setCustomerProfileId($profileId);

        $controller = new AnetController\DeleteCustomerProfileController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        return $response->getMessages()->getResultCode() === 'Ok';
    }

    // ========== PAYMENT PROFILE METHODS ==========

    public function createPaymentProfile(
        string $customerProfileId,
        string $opaqueDataDescriptor,
        string $opaqueDataValue,
        array $billingInfo
    ): AnetAPI\CreateCustomerPaymentProfileResponse {
        // Create opaque data from Accept.js token
        $opaqueData = new AnetAPI\OpaqueDataType();
        $opaqueData->setDataDescriptor($opaqueDataDescriptor);
        $opaqueData->setDataValue($opaqueDataValue);

        $paymentType = new AnetAPI\PaymentType();
        $paymentType->setOpaqueData($opaqueData);

        // Billing address
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($billingInfo['first_name'] ?? '');
        $billTo->setLastName($billingInfo['last_name'] ?? '');
        $billTo->setAddress($billingInfo['address'] ?? '');
        $billTo->setCity($billingInfo['city'] ?? '');
        $billTo->setState($billingInfo['state'] ?? '');
        $billTo->setZip($billingInfo['postal_code'] ?? '');
        $billTo->setCountry($billingInfo['country'] ?? 'USA');
        if (!empty($billingInfo['phone'])) {
            $billTo->setPhoneNumber($billingInfo['phone']);
        }

        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile->setPayment($paymentType);
        $paymentProfile->setBillTo($billTo);

        $request = new AnetAPI\CreateCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setCustomerProfileId($customerProfileId);
        $request->setPaymentProfile($paymentProfile);
        $request->setValidationMode('testMode');

        $controller = new AnetController\CreateCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        $this->logResponse('createPaymentProfile', $response);

        return $response;
    }

    public function deletePaymentProfile(string $customerProfileId, string $paymentProfileId): bool
    {
        $request = new AnetAPI\DeleteCustomerPaymentProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setCustomerProfileId($customerProfileId);
        $request->setCustomerPaymentProfileId($paymentProfileId);

        $controller = new AnetController\DeleteCustomerPaymentProfileController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        return $response->getMessages()->getResultCode() === 'Ok';
    }

    // ========== TRANSACTION METHODS ==========

    public function chargeCustomerProfile(
        string $customerProfileId,
        string $paymentProfileId,
        float $amount,
        ?string $orderId = null
    ): AnetAPI\CreateTransactionResponse {
        $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
        $profileToCharge->setCustomerProfileId($customerProfileId);

        $paymentProfile = new AnetAPI\PaymentProfile();
        $paymentProfile->setPaymentProfileId($paymentProfileId);
        $profileToCharge->setPaymentProfile($paymentProfile);

        $transactionType = config('lunar.authorizenet.transaction_policy') === 'auth_only'
            ? 'authOnlyTransaction'
            : 'authCaptureTransaction';

        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType($transactionType);
        $transactionRequest->setAmount($amount);
        $transactionRequest->setProfile($profileToCharge);

        if ($orderId) {
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($orderId);
            $transactionRequest->setOrder($order);
        }

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setTransactionRequest($transactionRequest);

        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        $this->logResponse('chargeCustomerProfile', $response);

        return $response;
    }

    public function chargeWithNonce(
        string $opaqueDataDescriptor,
        string $opaqueDataValue,
        float $amount,
        array $billingInfo,
        ?string $orderId = null
    ): AnetAPI\CreateTransactionResponse {
        $opaqueData = new AnetAPI\OpaqueDataType();
        $opaqueData->setDataDescriptor($opaqueDataDescriptor);
        $opaqueData->setDataValue($opaqueDataValue);

        $paymentType = new AnetAPI\PaymentType();
        $paymentType->setOpaqueData($opaqueData);

        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($billingInfo['first_name'] ?? '');
        $billTo->setLastName($billingInfo['last_name'] ?? '');
        $billTo->setAddress($billingInfo['address'] ?? '');
        $billTo->setCity($billingInfo['city'] ?? '');
        $billTo->setState($billingInfo['state'] ?? '');
        $billTo->setZip($billingInfo['postal_code'] ?? '');
        $billTo->setCountry($billingInfo['country'] ?? 'USA');
        if (!empty($billingInfo['phone'])) {
            $billTo->setPhoneNumber($billingInfo['phone']);
        }

        // Add customer data for email receipt
        $customerData = null;
        if (!empty($billingInfo['email'])) {
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setEmail($billingInfo['email']);
        }

        $transactionType = config('lunar.authorizenet.transaction_policy') === 'auth_only'
            ? 'authOnlyTransaction'
            : 'authCaptureTransaction';

        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType($transactionType);
        $transactionRequest->setAmount($amount);
        $transactionRequest->setPayment($paymentType);
        $transactionRequest->setBillTo($billTo);

        // Add customer email for receipt
        if ($customerData) {
            $transactionRequest->setCustomer($customerData);
        }

        if ($orderId) {
            $order = new AnetAPI\OrderType();
            $order->setInvoiceNumber($orderId);
            $transactionRequest->setOrder($order);
        }

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setTransactionRequest($transactionRequest);

        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        $this->logResponse('chargeWithNonce', $response);

        return $response;
    }

    public function refundTransaction(string $transactionId, float $amount, string $lastFour): AnetAPI\CreateTransactionResponse
    {
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($lastFour);
        $creditCard->setExpirationDate('XXXX');

        $paymentType = new AnetAPI\PaymentType();
        $paymentType->setCreditCard($creditCard);

        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType('refundTransaction');
        $transactionRequest->setAmount($amount);
        $transactionRequest->setPayment($paymentType);
        $transactionRequest->setRefTransId($transactionId);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setTransactionRequest($transactionRequest);

        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        $this->logResponse('refundTransaction', $response);

        return $response;
    }

    public function voidTransaction(string $transactionId): AnetAPI\CreateTransactionResponse
    {
        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType('voidTransaction');
        $transactionRequest->setRefTransId($transactionId);

        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($this->merchantAuth);
        $request->setTransactionRequest($transactionRequest);

        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse($this->endpoint);

        $this->logResponse('voidTransaction', $response);

        return $response;
    }

    protected function logResponse(string $method, $response): void
    {
        if (config('lunar.authorizenet.logging.enabled')) {
            $resultCode = $response->getMessages()->getResultCode();
            $message = $response->getMessages()->getMessage()[0] ?? null;

            Log::channel(config('lunar.authorizenet.logging.channel'))->info("Authorize.net {$method}", [
                'result_code' => $resultCode,
                'message_code' => $message?->getCode(),
                'message_text' => $message?->getText(),
            ]);
        }
    }
}
