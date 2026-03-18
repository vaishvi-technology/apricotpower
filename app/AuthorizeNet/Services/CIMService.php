<?php

namespace App\AuthorizeNet\Services;

use App\AuthorizeNet\Managers\AuthorizeNetManager;
use App\AuthorizeNet\Models\AuthorizeNetProfile;
use App\AuthorizeNet\Exceptions\AuthorizeNetException;
use App\Models\Customer;
use App\Models\PaymentMethod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CIMService
{
    public function __construct(
        protected AuthorizeNetManager $manager
    ) {}

    /**
     * Get or create CIM profile for customer
     */
    public function getOrCreateProfile(Customer $customer): AuthorizeNetProfile
    {
        $profile = AuthorizeNetProfile::where('customer_id', $customer->id)->first();

        if ($profile) {
            return $profile;
        }

        return $this->createProfile($customer);
    }

    /**
     * Create new CIM profile
     */
    public function createProfile(Customer $customer): AuthorizeNetProfile
    {
        $merchantCustomerId = 'CUST_' . $customer->id . '_' . time();

        $response = $this->manager->createCustomerProfile(
            $merchantCustomerId,
            $customer->email ?? $customer->attribute_data['email'] ?? '',
            "{$customer->first_name} {$customer->last_name}"
        );

        if ($response->getMessages()->getResultCode() !== 'Ok') {
            $errorMessage = $response->getMessages()->getMessage()[0]->getText();

            // Check if profile already exists (duplicate error)
            if (strpos($errorMessage, 'duplicate') !== false) {
                // Try to extract existing profile ID from error message
                preg_match('/(\d+)/', $errorMessage, $matches);
                if (!empty($matches[1])) {
                    return AuthorizeNetProfile::create([
                        'customer_id' => $customer->id,
                        'profile_id' => $matches[1],
                        'merchant_customer_id' => $merchantCustomerId,
                        'email' => $customer->email ?? $customer->attribute_data['email'] ?? '',
                        'description' => "{$customer->first_name} {$customer->last_name}",
                    ]);
                }
            }

            throw new AuthorizeNetException("Failed to create profile: {$errorMessage}");
        }

        return AuthorizeNetProfile::create([
            'customer_id' => $customer->id,
            'profile_id' => $response->getCustomerProfileId(),
            'merchant_customer_id' => $merchantCustomerId,
            'email' => $customer->email ?? $customer->attribute_data['email'] ?? '',
            'description' => "{$customer->first_name} {$customer->last_name}",
        ]);
    }

    /**
     * Add payment method from Accept.js token
     */
    public function addPaymentMethod(
        Customer $customer,
        string $opaqueDataDescriptor,
        string $opaqueDataValue,
        array $billingInfo
    ): PaymentMethod {
        $profile = $this->getOrCreateProfile($customer);

        $response = $this->manager->createPaymentProfile(
            $profile->profile_id,
            $opaqueDataDescriptor,
            $opaqueDataValue,
            $billingInfo
        );

        if ($response->getMessages()->getResultCode() !== 'Ok') {
            $errorMessage = $response->getMessages()->getMessage()[0]->getText();
            throw new AuthorizeNetException("Failed to add payment method: {$errorMessage}");
        }

        // Extract card info from validation response
        $validationResponse = $response->getValidationDirectResponse();
        $cardInfo = $this->parseValidationResponse($validationResponse);

        return PaymentMethod::create([
            'customer_id' => $customer->id,
            'type' => PaymentMethod::TYPE_CARD,
            'provider' => 'authorize_net',
            'provider_payment_method_id' => $response->getCustomerPaymentProfileId(),
            'last_four' => $cardInfo['last_four'],
            'brand' => $cardInfo['card_type'],
            'exp_month' => $billingInfo['exp_month'] ?? null,
            'exp_year' => $billingInfo['exp_year'] ?? null,
            'billing_name' => trim(($billingInfo['first_name'] ?? '') . ' ' . ($billingInfo['last_name'] ?? '')),
            'billing_address_line_1' => $billingInfo['address'] ?? null,
            'billing_city' => $billingInfo['city'] ?? null,
            'billing_state' => $billingInfo['state'] ?? null,
            'billing_postal_code' => $billingInfo['postal_code'] ?? null,
            'billing_country' => $billingInfo['country'] ?? 'USA',
            'is_default' => PaymentMethod::where('customer_id', $customer->id)->count() === 0,
            'is_active' => true,
        ]);
    }

    /**
     * Delete payment method from CIM
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod): bool
    {
        if ($paymentMethod->provider !== 'authorize_net') {
            return false;
        }

        $profile = AuthorizeNetProfile::where('customer_id', $paymentMethod->customer_id)->first();

        if (!$profile) {
            $paymentMethod->delete();
            return true;
        }

        $deleted = $this->manager->deletePaymentProfile(
            $profile->profile_id,
            $paymentMethod->provider_payment_method_id
        );

        if ($deleted) {
            $paymentMethod->delete();
        }

        return $deleted;
    }

    /**
     * Get all payment methods for customer
     */
    public function getPaymentMethods(Customer $customer): Collection
    {
        return PaymentMethod::where('customer_id', $customer->id)
            ->where('provider', 'authorize_net')
            ->where('is_active', true)
            ->get();
    }

    /**
     * Parse the validation response to extract card details
     */
    protected function parseValidationResponse(?string $response): array
    {
        if (!$response) {
            return ['last_four' => '****', 'card_type' => 'Unknown'];
        }

        $parts = explode(',', $response);

        // The account number is typically at index 50 and card type at 51
        // But this can vary, so we'll try to find the last 4 digits pattern
        $lastFour = '****';
        $cardType = 'Unknown';

        if (isset($parts[50]) && preg_match('/\d{4}$/', $parts[50], $matches)) {
            $lastFour = $matches[0];
        }

        if (isset($parts[51])) {
            $cardType = trim($parts[51]);
        }

        return [
            'last_four' => $lastFour,
            'card_type' => $cardType,
        ];
    }
}
