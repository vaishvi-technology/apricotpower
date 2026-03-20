<?php

namespace App\Livewire;

use App\Models\Promo;
use App\Services\PromoService;
use App\Shipping\Exceptions\ShipStationException;
use App\Shipping\ShipStationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Facades\Payments;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;

class CheckoutPage extends Component
{
    /**
     * The Cart instance.
     */
    public ?Cart $cart;

    /**
     * The shipping address instance.
     */
    public ?CartAddress $shipping = null;

    /**
     * The billing address instance.
     */
    public ?CartAddress $billing = null;

    /**
     * The current checkout step.
     */
    public int $currentStep = 1;

    /**
     * Whether the shipping address is the billing address too.
     */
    public bool $shippingIsBilling = true;

    /**
     * The chosen shipping option.
     */
    public $chosenShipping = null;

    /**
     * The checkout steps.
     */
    public array $steps = [
        'shipping_address' => 1,
        'shipping_option' => 2,
        'billing_address' => 3,
        'payment' => 4,
    ];

    /**
     * The payment type we want to use.
     */
    public string $paymentType = 'cash-in-hand';

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'selectedShippingOption' => 'refreshCart',
    ];

    public $payment_intent = null;

    public $payment_intent_client_secret = null;

    protected $queryString = [
        'payment_intent',
        'payment_intent_client_secret',
    ];

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return array_merge(
            $this->getAddressValidation('shipping'),
            $this->getAddressValidation('billing'),
            [
                'shippingIsBilling' => 'boolean',
                'chosenShipping' => 'required',
            ]
        );
    }

    public function mount(): void
    {
        if (! $this->cart = CartSession::current()) {
            $this->redirect('/');

            return;
        }

        if ($this->payment_intent) {
            $payment = Payments::driver($this->paymentType)->cart($this->cart)->withData([
                'payment_intent_client_secret' => $this->payment_intent_client_secret,
                'payment_intent' => $this->payment_intent,
            ])->authorize();

            if ($payment->success) {
                redirect()->route('checkout-success.view');

                return;
            }
        }

        // Do we have a shipping address?
        $this->shipping = $this->cart->shippingAddress ?: new CartAddress;

        $this->billing = $this->cart->billingAddress ?: new CartAddress;

        $this->determineCheckoutStep();
    }

    public function hydrate(): void
    {
        $this->cart = CartSession::current();
    }

    /**
     * Trigger an event to refresh addresses.
     */
    public function triggerAddressRefresh(): void
    {
        $this->dispatch('refreshAddress');
    }

    /**
     * Determines what checkout step we should be at.
     */
    public function determineCheckoutStep(): void
    {
        $shippingAddress = $this->cart->shippingAddress;
        $billingAddress = $this->cart->billingAddress;

        if ($shippingAddress) {
            if ($shippingAddress->id) {
                $this->currentStep = $this->steps['shipping_address'] + 1;
            }

            // Do we have a selected option?
            if ($this->shippingOption) {
                $this->chosenShipping = $this->shippingOption->getIdentifier();
                $this->currentStep = $this->steps['shipping_option'] + 1;
            } else {
                $this->currentStep = $this->steps['shipping_option'];
                $this->chosenShipping = $this->shippingOptions->first()?->getIdentifier();

                return;
            }
        }

        if ($billingAddress) {
            $this->currentStep = $this->steps['billing_address'] + 1;
        }
    }

    /**
     * Refresh the cart instance.
     */
    public function refreshCart(): void
    {
        $this->cart = CartSession::current();
    }

    /**
     * Return the shipping option.
     */
    public function getShippingOptionProperty()
    {
        $shippingAddress = $this->cart->shippingAddress;

        if (! $shippingAddress) {
            return;
        }

        if ($option = $shippingAddress->shipping_option) {
            return ShippingManifest::getOptions($this->cart)->first(function ($opt) use ($option) {
                return $opt->getIdentifier() == $option;
            });
        }

        return null;
    }

    /**
     * Save the address for a given type.
     */
    public function saveAddress(string $type): void
    {
        $validatedData = $this->validate(
            $this->getAddressValidation($type)
        );

        $address = $this->{$type};

        if ($type == 'billing') {
            $this->cart->setBillingAddress($address);
            $this->billing = $this->cart->billingAddress;
        }

        if ($type == 'shipping') {
            $this->cart->setShippingAddress($address);
            $this->shipping = $this->cart->shippingAddress;

            if ($this->shippingIsBilling) {
                // Do we already have a billing address?
                if ($billing = $this->cart->billingAddress) {
                    $billing->fill($validatedData['shipping']);
                    $this->cart->setBillingAddress($billing);
                } else {
                    $address = $address->only(
                        $address->getFillable()
                    );
                    $this->cart->setBillingAddress($address);
                }

                $this->billing = $this->cart->billingAddress;
            }
        }

        $this->determineCheckoutStep();
    }

    /**
     * Save the selected shipping option.
     */
    public function saveShippingOption(): void
    {
        $option = $this->shippingOptions->first(fn ($option) => $option->getIdentifier() == $this->chosenShipping);

        CartSession::setShippingOption($option);

        // Store ShipStation carrier/service codes in cart meta for later order creation
        if ($option && $option->meta) {
            $meta = $this->cart->meta ?? [];
            $meta['shipstation_carrier_code'] = $option->meta['carrier_code'] ?? null;
            $meta['shipstation_service_code'] = $option->meta['service_code'] ?? null;
            $this->cart->update(['meta' => $meta]);
        }

        $this->refreshCart();

        $this->determineCheckoutStep();
    }

    public function checkout()
    {
        $payment = Payments::cart($this->cart)->withData([
            'payment_intent_client_secret' => $this->payment_intent_client_secret,
            'payment_intent' => $this->payment_intent,
        ])->authorize();

        if ($payment->success) {
            // Record promo usage if a promo was applied
            $this->recordPromoUsage();

            // Submit order to ShipStation
            $this->submitOrderToShipStation();

            redirect()->route('checkout-success.view');

            return;
        }

        // Record promo usage if a promo was applied
        $this->recordPromoUsage();

        // Submit order to ShipStation even if payment status is pending
        $this->submitOrderToShipStation();

        return redirect()->route('checkout-success.view');
    }

    /**
     * Submit the order to ShipStation after successful checkout.
     */
    protected function submitOrderToShipStation(): void
    {
        if (config('shipping.provider') !== 'shipstation') {
            return;
        }

        try {
            // Get the latest order from the cart
            $order = $this->cart->orders()->latest()->first();

            if (! $order) {
                Log::warning('No order found to submit to ShipStation', [
                    'cart_id' => $this->cart->id,
                ]);

                return;
            }

            // Transfer ShipStation meta from cart to order
            $cartMeta = $this->cart->meta ?? [];
            $orderMeta = $order->meta ?? [];
            $orderMeta['shipstation_carrier_code'] = $cartMeta['shipstation_carrier_code'] ?? null;
            $orderMeta['shipstation_service_code'] = $cartMeta['shipstation_service_code'] ?? null;
            $order->update(['meta' => $orderMeta]);

            // Create order in ShipStation
            $shipStation = app(ShipStationService::class);
            $ssOrderId = $shipStation->createOrder($order);

            // Update order with ShipStation order ID
            $order->update([
                'shipstation_order_id' => $ssOrderId,
                'shipstation_carrier_code' => $orderMeta['shipstation_carrier_code'],
                'shipstation_service_code' => $orderMeta['shipstation_service_code'],
                'shipping_status' => 'processing',
            ]);

            Log::info('Order submitted to ShipStation', [
                'order_id' => $order->id,
                'shipstation_order_id' => $ssOrderId,
            ]);
        } catch (ShipStationException $e) {
            // Log the error but don't fail the checkout
            Log::error('Failed to submit order to ShipStation', [
                'error' => $e->getMessage(),
                'cart_id' => $this->cart->id,
            ]);
        }
    }

    /**
     * Record promo usage when order is placed.
     */
    protected function recordPromoUsage(): void
    {
        if (!$this->cart || !$this->cart->promo_id) {
            return;
        }

        $promo = Promo::find($this->cart->promo_id);
        if (!$promo) {
            return;
        }

        $customerId = $this->cart->customer_id;
        $customerEmail = null;

        if ($customerId) {
            $customer = \App\Models\Customer::find($customerId);
            $customerEmail = $customer?->email;
        }

        // Get order ID from the cart's latest order
        $orderId = $this->cart->orders()->latest()->value('id');

        $promoService = app(PromoService::class);
        $promoService->recordUsage(
            $promo,
            $customerId,
            $customerEmail,
            $orderId,
            (float) $this->cart->promo_discount,
            (bool) $this->cart->promo_free_shipping,
        );
    }

    /**
     * Return the available countries.
     */
    public function getCountriesProperty(): Collection
    {
        return Country::whereIn('iso3', ['GBR', 'USA'])->get();
    }

    /**
     * Return available shipping options.
     */
    public function getShippingOptionsProperty(): Collection
    {
        return ShippingManifest::getOptions(
            $this->cart
        );
    }

    /**
     * Get the applied promo for the cart.
     */
    public function getAppliedPromoProperty(): ?Promo
    {
        if (!$this->cart || !$this->cart->promo_id) {
            return null;
        }

        return Promo::find($this->cart->promo_id);
    }

    /**
     * Get the promo discount amount.
     */
    public function getPromoDiscountProperty(): float
    {
        return $this->cart?->promo_discount ?? 0;
    }

    /**
     * Get whether promo grants free shipping.
     */
    public function getPromoFreeShippingProperty(): bool
    {
        return (bool) ($this->cart?->promo_free_shipping ?? false);
    }

    /**
     * Return the address validation rules for a given type.
     */
    protected function getAddressValidation(string $type): array
    {
        return [
            "{$type}.first_name" => 'required',
            "{$type}.last_name" => 'required',
            "{$type}.line_one" => 'required',
            "{$type}.country_id" => 'required',
            "{$type}.city" => 'required',
            "{$type}.postcode" => 'required',
            "{$type}.company_name" => 'nullable',
            "{$type}.line_two" => 'nullable',
            "{$type}.line_three" => 'nullable',
            "{$type}.state" => 'nullable',
            "{$type}.delivery_instructions" => 'nullable',
            "{$type}.contact_email" => 'required|email',
            "{$type}.contact_phone" => 'nullable',
        ];
    }

    public function render(): View
    {
        return view('livewire.checkout-page')
            ->layout('layouts.checkout');
    }
}
