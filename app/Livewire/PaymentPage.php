<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Facades\Payments;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;

class PaymentPage extends Component
{
    // Billing Information
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $postalCode = '';
    public string $country = 'US';

    // Card Information (received from Accept.js)
    public ?string $opaqueDataDescriptor = null;
    public ?string $opaqueDataValue = null;

    // UI State
    public ?string $errorMessage = null;
    public ?string $successMessage = null;
    public bool $processing = false;

    protected $listeners = [
        'acceptJsResponse' => 'handleAcceptJsResponse',
        'acceptJsError' => 'handleAcceptJsError',
    ];

    /**
     * Get the current cart with calculated totals.
     */
    protected function getCart(): ?Cart
    {
        $cart = CartSession::current();

        if ($cart) {
            $cart->calculate();
        }

        return $cart;
    }

    protected function rules(): array
    {
        return [
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postalCode' => 'required|string|max:20',
            'country' => 'required|string|size:2',
        ];
    }

    protected function messages(): array
    {
        return [
            'firstName.required' => 'First name is required.',
            'lastName.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'address.required' => 'Street address is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'postalCode.required' => 'Postal code is required.',
        ];
    }

    public function mount(): void
    {
        $cart = $this->getCart();

        if (!$cart || $cart->lines->isEmpty()) {
            $this->redirect(route('cart.view'));
            return;
        }

        // Set default shipping address on cart
        $this->setDefaultShipping();

        // Pre-fill from logged-in customer if available
        if ($cart->customer) {
            $customer = $cart->customer;
            $this->firstName = $customer->first_name ?? '';
            $this->lastName = $customer->last_name ?? '';
            $this->email = $customer->email ?? '';
            $this->phone = $customer->phone ?? '';
        }
    }

    /**
     * Set default shipping address and option on the cart.
     */
    protected function setDefaultShipping(): void
    {
        $cart = CartSession::current();

        if (!$cart) {
            return;
        }

        // Default shipping address (US-based)
        $defaultShipping = [
            'first_name' => 'Default',
            'last_name' => 'Shipping',
            'line_one' => '123 Default Street',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postcode' => '90001',
            'country_id' => $this->getCountryId('US'),
            'contact_email' => 'default@example.com',
        ];

        // Set shipping address on cart if not already set
        if (!$cart->shippingAddress) {
            $cart->setShippingAddress($defaultShipping);
        }

        // Set shipping option if not already set
        $cart = $cart->refresh();
        if ($cart->shippingAddress && !$cart->shippingAddress->shipping_option) {
            $cart->calculate();
            $shippingOptions = ShippingManifest::getOptions($cart);

            if ($shippingOptions->isNotEmpty()) {
                $firstOption = $shippingOptions->first();
                $cart->shippingAddress->update([
                    'shipping_option' => $firstOption->getIdentifier(),
                ]);
                $cart->shippingAddress->shippingOption = $firstOption;
            }
        }
    }

    /**
     * Get country ID from ISO code.
     */
    protected function getCountryId(string $iso): ?int
    {
        return \Lunar\Models\Country::where('iso2', $iso)
            ->orWhere('iso3', $iso === 'US' ? 'USA' : $iso)
            ->first()?->id;
    }

    public function getClientKeyProperty(): string
    {
        return config('lunar.authorizenet.public_client_key', '');
    }

    public function getApiLoginIdProperty(): string
    {
        return config('lunar.authorizenet.api_login_id', '');
    }

    public function getAcceptJsUrlProperty(): string
    {
        return config('lunar.authorizenet.environment') === 'production'
            ? config('lunar.authorizenet.accept_js.production_url', 'https://js.authorize.net/v1/Accept.js')
            : config('lunar.authorizenet.accept_js.sandbox_url', 'https://jstest.authorize.net/v1/Accept.js');
    }

    public function handleAcceptJsResponse(array $response): void
    {
        $this->opaqueDataDescriptor = $response['opaqueDataDescriptor'] ?? null;
        $this->opaqueDataValue = $response['opaqueDataValue'] ?? null;

        if ($this->opaqueDataDescriptor && $this->opaqueDataValue) {
            $this->processPayment();
        } else {
            $this->errorMessage = 'Failed to tokenize card. Please try again.';
            $this->processing = false;
        }
    }

    public function handleAcceptJsError(string $error): void
    {
        $this->errorMessage = $error;
        $this->processing = false;
    }

    public function validateBilling(): bool
    {
        $this->errorMessage = null;

        try {
            $this->validate();
            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorMessage = collect($e->errors())->flatten()->first();
            return false;
        }
    }

    public function processPayment(): void
    {
        $this->processing = true;
        $this->errorMessage = null;

        try {
            // Validate billing info
            $this->validate();

            // Get fresh cart
            $cart = CartSession::current();

            if (!$cart) {
                $this->errorMessage = 'Cart not found. Please try again.';
                $this->processing = false;
                return;
            }

            // Set billing address on cart
            $billingAddress = [
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'line_one' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'postcode' => $this->postalCode,
                'country_id' => $this->getCountryId($this->country),
                'contact_email' => $this->email,
                'contact_phone' => $this->phone,
            ];

            $cart->setBillingAddress($billingAddress);

            // Also update shipping with real billing info
            $cart->setShippingAddress($billingAddress);

            // Recalculate cart and set shipping option
            $cart = CartSession::current();
            $cart->calculate();

            // Set shipping option from available options
            $shippingOptions = ShippingManifest::getOptions($cart);
            if ($shippingOptions->isNotEmpty()) {
                $shippingOption = $shippingOptions->first();
                $cart->shippingAddress->update([
                    'shipping_option' => $shippingOption->getIdentifier(),
                ]);
                $cart->shippingAddress->shippingOption = $shippingOption;
            }

            // Recalculate with shipping
            $cart->calculate();

            // Prepare payment data
            $paymentData = [
                'opaque_data_descriptor' => $this->opaqueDataDescriptor,
                'opaque_data_value' => $this->opaqueDataValue,
                'billing_info' => [
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'address' => $this->address,
                    'city' => $this->city,
                    'state' => $this->state,
                    'postal_code' => $this->postalCode,
                    'country' => $this->country,
                    'email' => $this->email,
                    'phone' => $this->phone,
                ],
            ];

            // Process payment through Authorize.net
            $payment = Payments::driver('authorizenet')
                ->cart($cart)
                ->withData($paymentData)
                ->authorize();

            if ($payment->success) {
                // Clear cart and redirect to success
                CartSession::forget();
                $this->redirect(route('checkout-success.view'));
                return;
            }

            $this->errorMessage = $payment->message ?? 'Payment failed. Please check your card details and try again.';

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorMessage = collect($e->errors())->flatten()->first();
        } catch (\Exception $e) {
            $this->errorMessage = 'An error occurred: ' . $e->getMessage();
        }

        $this->processing = false;
    }

    public function render(): View
    {
        return view('livewire.payment-page', [
            'cart' => $this->getCart(),
        ])->layout('layouts.checkout');
    }
}
