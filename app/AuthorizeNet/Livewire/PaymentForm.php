<?php

namespace App\AuthorizeNet\Livewire;

use App\AuthorizeNet\Services\CIMService;
use App\Models\PaymentMethod;
use Livewire\Component;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;

class PaymentForm extends Component
{
    public Cart $cart;
    public string $returnUrl = '';

    public ?int $selectedPaymentMethodId = null;
    public bool $useNewCard = true;
    public bool $saveCard = false;

    public ?string $opaqueDataDescriptor = null;
    public ?string $opaqueDataValue = null;

    public ?string $errorMessage = null;
    public bool $processing = false;

    protected $listeners = [
        'acceptJsResponse' => 'handleAcceptJsResponse',
        'acceptJsError' => 'handleAcceptJsError',
    ];

    public function mount(): void
    {
        $savedCards = $this->getSavedCards();

        if ($savedCards->isNotEmpty()) {
            $defaultCard = $savedCards->firstWhere('is_default', true) ?? $savedCards->first();
            $this->selectedPaymentMethodId = $defaultCard->id;
            $this->useNewCard = false;
        }
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
            ? config('lunar.authorizenet.accept_js.production_url')
            : config('lunar.authorizenet.accept_js.sandbox_url');
    }

    public function getSavedCards()
    {
        if (!$this->cart->customer) {
            return collect();
        }

        return PaymentMethod::where('customer_id', $this->cart->customer->id)
            ->where('provider', 'authorize_net')
            ->where('is_active', true)
            ->get();
    }

    public function selectPaymentMethod(int $id): void
    {
        $this->selectedPaymentMethodId = $id;
        $this->useNewCard = false;
        $this->errorMessage = null;
    }

    public function useNewCardForm(): void
    {
        $this->selectedPaymentMethodId = null;
        $this->useNewCard = true;
        $this->errorMessage = null;
    }

    public function handleAcceptJsResponse(array $response): void
    {
        $this->opaqueDataDescriptor = $response['opaqueDataDescriptor'];
        $this->opaqueDataValue = $response['opaqueDataValue'];
        $this->processPayment();
    }

    public function handleAcceptJsError(string $error): void
    {
        $this->errorMessage = $error;
        $this->processing = false;
    }

    public function processPayment(): void
    {
        $this->processing = true;
        $this->errorMessage = null;

        try {
            $data = [];

            if ($this->selectedPaymentMethodId && !$this->useNewCard) {
                $data['payment_method_id'] = $this->selectedPaymentMethodId;
            } else {
                $data['opaque_data_descriptor'] = $this->opaqueDataDescriptor;
                $data['opaque_data_value'] = $this->opaqueDataValue;
                $data['save_card'] = $this->saveCard;
            }

            $payment = Payments::driver('authorizenet')
                ->cart($this->cart)
                ->withData($data)
                ->authorize();

            if ($payment->success) {
                $this->redirect(route('checkout.success'));
            } else {
                $this->errorMessage = $payment->message ?? 'Payment failed. Please try again.';
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->processing = false;
    }

    public function render()
    {
        return view('livewire.authorizenet.payment-form', [
            'savedCards' => $this->getSavedCards(),
        ]);
    }
}
