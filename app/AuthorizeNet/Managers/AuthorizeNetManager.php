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
        $resultCode = $response->getMessages()->getResultCode();
        $message = $response->getMessages()->getMessage()[0] ?? null;
        $transactionResponse = $response->getTransactionResponse();

        $logData = [
            'result_code' => $resultCode,
            'message_code' => $message?->getCode(),
            'message_text' => $message?->getText(),
        ];

        if ($transactionResponse) {
            $logData['response_code'] = $transactionResponse->getResponseCode();
            $logData['auth_code'] = $transactionResponse->getAuthCode();
            $logData['trans_id'] = $transactionResponse->getTransId();
            $logData['avs_result'] = $transactionResponse->getAvsResultCode();
            $logData['cvv_result'] = $transactionResponse->getCvvResultCode();

            // Get transaction errors
            if ($transactionResponse->getErrors()) {
                $errors = [];
                foreach ($transactionResponse->getErrors() as $error) {
                    $errors[] = [
                        'code' => $error->getErrorCode(),
                        'text' => $error->getErrorText(),
                    ];
                }
                $logData['errors'] = $errors;
            }

            // Get transaction messages
            if ($transactionResponse->getMessages()) {
                $messages = [];
                foreach ($transactionResponse->getMessages() as $msg) {
                    $messages[] = [
                        'code' => $msg->getCode(),
                        'description' => $msg->getDescription(),
                    ];
                }
                $logData['trans_messages'] = $messages;
            }
        }

        // Log at appropriate level based on result
        $isSuccess = $resultCode === 'Ok' &&
            $transactionResponse &&
            (int) $transactionResponse->getResponseCode() === 1;

        if ($isSuccess) {
            Log::info("Authorize.net {$method}", $logData);
        } else {
            Log::error("Authorize.net {$method} FAILED", $logData);
        }
    }

    /**
     * Map Authorize.net error codes to user-friendly messages.
     */
    public static function getUserFriendlyMessage(int $errorCode, ?string $defaultMessage = null): string
    {
        $messages = [
            2 => 'Your card was declined. Please check your card details or try a different card.',
            3 => 'There was an error processing your payment. Please try again.',
            4 => 'Your card was declined. Please contact your bank or try a different card.',
            5 => 'Invalid transaction amount. Please try again.',
            6 => 'Invalid credit card number. Please check your card number and try again.',
            7 => 'Credit card has expired. Please use a different card.',
            8 => 'Credit card has expired. Please use a different card.',
            11 => 'This appears to be a duplicate transaction. Please wait a moment before trying again.',
            13 => 'Invalid merchant login. Please contact support.',
            27 => 'Transaction could not be verified. Please try again.',
            35 => 'Payment could not be processed at this time. Please try a different card or try again later.',
            37 => 'Invalid credit card number. Please check your card number and try again.',
            44 => 'Your card security code (CVV) is incorrect. Please check and try again.',
            45 => 'This transaction has been declined. Please try a different card.',
            65 => 'Your card was declined. Please contact your bank or try a different card.',
            127 => 'Your billing address does not match the card. Please verify your address.',
            252 => 'Your request cannot be processed at this time. Please try again later.',
        ];

        return $messages[$errorCode] ?? $defaultMessage ?? 'An error occurred during payment processing. Please try again.';
    }
}
