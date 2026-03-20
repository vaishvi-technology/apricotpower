<?php

namespace App\AuthorizeNet;

use App\AuthorizeNet\Services\TransactionService;
use App\AuthorizeNet\Services\CIMService;
use App\AuthorizeNet\DTOs\TransactionResponseDTO;
use App\AuthorizeNet\Managers\AuthorizeNetManager;
use App\Models\PaymentMethod;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Base\DataTransferObjects\PaymentRefund;
use Lunar\Models\Contracts\Transaction as TransactionContract;
use Lunar\PaymentTypes\AbstractPayment;
use Illuminate\Support\Facades\DB;

class AuthorizeNetPaymentType extends AbstractPayment
{
    public function __construct(
        protected TransactionService $transactionService,
        protected CIMService $cimService
    ) {}

    public function authorize(): ?PaymentAuthorize
    {
        $this->order = $this->order ?: ($this->cart->draftOrder ?: $this->cart->completedOrder);

        if ($this->order && $this->order->placed_at) {
            return new PaymentAuthorize(success: false, message: 'Order already placed');
        }

        if (!$this->order) {
            $this->order = $this->cart->createOrder();
        }

        try {
            return DB::transaction(function () {
                if (isset($this->data['payment_method_id'])) {
                    $response = $this->processWithSavedCard();
                } else {
                    $response = $this->processWithNonce();
                }

                if ($response->isSuccessful()) {
                    $this->order->update([
                        'status' => 'payment-received',
                        'placed_at' => now(),
                    ]);

                    return new PaymentAuthorize(
                        success: true,
                        message: $response->getMessage(),
                        orderId: $this->order->id,
                        paymentType: 'authorizenet'
                    );
                }

                $this->order->update([
                    'status' => 'payment-failed',
                ]);

                // Get error code and use user-friendly message
                $errorCode = !empty($response->errors) ? (int) ($response->errors[0]['code'] ?? 0) : 0;
                $friendlyMessage = AuthorizeNetManager::getUserFriendlyMessage($errorCode, $response->getMessage());

                return new PaymentAuthorize(
                    success: false,
                    message: $friendlyMessage,
                    orderId: $this->order->id,
                    paymentType: 'authorizenet'
                );
            });
        } catch (\Exception $e) {
            return new PaymentAuthorize(
                success: false,
                message: $e->getMessage(),
                orderId: $this->order?->id,
                paymentType: 'authorizenet'
            );
        }
    }

    protected function processWithSavedCard(): TransactionResponseDTO
    {
        $paymentMethod = PaymentMethod::findOrFail($this->data['payment_method_id']);

        // Convert from cents to dollars
        $amount = (float) $this->order->total->value / 100;

        return $this->transactionService->chargeWithSavedCard(
            $paymentMethod,
            $amount,
            $this->order->id
        );
    }

    protected function processWithNonce(): TransactionResponseDTO
    {
        // Check if billing_info was passed directly (from PaymentPage)
        if (!empty($this->data['billing_info'])) {
            $billingInfo = $this->data['billing_info'];
        } else {
            // Fall back to cart billing address
            $billingAddress = $this->cart->billingAddress;

            $billingInfo = [
                'first_name' => $billingAddress->first_name ?? '',
                'last_name' => $billingAddress->last_name ?? '',
                'address' => $billingAddress->line_one ?? '',
                'city' => $billingAddress->city ?? '',
                'state' => $billingAddress->state ?? '',
                'postal_code' => $billingAddress->postcode ?? '',
                'country' => $billingAddress->country?->iso2 ?? 'US',
                'email' => $billingAddress->contact_email ?? '',
                'phone' => $billingAddress->contact_phone ?? '',
            ];
        }

        $savedCard = null;

        // Save card if requested
        if (!empty($this->data['save_card']) && $this->cart->customer) {
            $savedCard = $this->cimService->addPaymentMethod(
                $this->cart->customer,
                $this->data['opaque_data_descriptor'],
                $this->data['opaque_data_value'],
                $billingInfo
            );
        }

        // Convert from cents to dollars
        $amount = (float) $this->order->total->value / 100;

        return $this->transactionService->chargeWithNonce(
            $this->data['opaque_data_descriptor'],
            $this->data['opaque_data_value'],
            $amount,
            $billingInfo,
            $this->order->id,
            $savedCard
        );
    }

    public function capture(TransactionContract $transaction, $amount = 0): PaymentCapture
    {
        // Implement if using auth_only policy
        return new PaymentCapture(success: true);
    }

    public function refund(TransactionContract $transaction, int $amount, $notes = null): PaymentRefund
    {
        // Find our transaction record
        $localTransaction = \App\Models\Transaction::where('order_id', $transaction->order_id)
            ->where('provider', 'authorize_net')
            ->where('type', 'charge')
            ->where('status', 'completed')
            ->firstOrFail();

        $response = $this->transactionService->refund(
            $localTransaction,
            $amount / 100,  // Convert from cents to dollars
            $notes
        );

        return new PaymentRefund(
            success: $response->isSuccessful()
        );
    }
}
