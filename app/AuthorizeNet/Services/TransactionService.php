<?php

namespace App\AuthorizeNet\Services;

use App\AuthorizeNet\Managers\AuthorizeNetManager;
use App\AuthorizeNet\DTOs\TransactionResponseDTO;
use App\AuthorizeNet\Models\AuthorizeNetProfile;
use App\AuthorizeNet\Exceptions\AuthorizeNetException;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function __construct(
        protected AuthorizeNetManager $manager,
        protected CIMService $cimService
    ) {}

    /**
     * Charge using saved payment method (CIM profile)
     */
    public function chargeWithSavedCard(
        PaymentMethod $paymentMethod,
        float $amount,
        int|string $orderId
    ): TransactionResponseDTO {
        $profile = AuthorizeNetProfile::where('customer_id', $paymentMethod->customer_id)->firstOrFail();

        $response = $this->manager->chargeCustomerProfile(
            $profile->profile_id,
            $paymentMethod->provider_payment_method_id,
            $amount,
            (string) $orderId
        );

        $dto = TransactionResponseDTO::fromApiResponse($response);

        $this->createTransactionRecord($dto, $orderId, $paymentMethod, 'charge', $amount);

        return $dto;
    }

    /**
     * Charge using Accept.js nonce (one-time or save card)
     */
    public function chargeWithNonce(
        string $opaqueDataDescriptor,
        string $opaqueDataValue,
        float $amount,
        array $billingInfo,
        int|string $orderId,
        ?PaymentMethod $savedCard = null
    ): TransactionResponseDTO {
        $response = $this->manager->chargeWithNonce(
            $opaqueDataDescriptor,
            $opaqueDataValue,
            $amount,
            $billingInfo,
            (string) $orderId
        );

        $dto = TransactionResponseDTO::fromApiResponse($response);

        $this->createTransactionRecord($dto, $orderId, $savedCard, 'charge', $amount);

        return $dto;
    }

    /**
     * Refund a transaction
     */
    public function refund(Transaction $transaction, float $amount, ?string $notes = null): TransactionResponseDTO
    {
        if (!$transaction->provider_transaction_id || !$transaction->last_four) {
            throw new AuthorizeNetException('Missing transaction ID or card information for refund');
        }

        $response = $this->manager->refundTransaction(
            $transaction->provider_transaction_id,
            $amount,
            $transaction->last_four
        );

        $dto = TransactionResponseDTO::fromApiResponse($response);

        // Create refund transaction record
        Transaction::create([
            'order_id' => $transaction->order_id,
            'payment_method_id' => $transaction->payment_method_id,
            'type' => Transaction::TYPE_REFUND,
            'status' => $dto->isSuccessful() ? Transaction::STATUS_COMPLETED : Transaction::STATUS_FAILED,
            'amount' => $amount,
            'currency' => $transaction->currency,
            'provider' => 'authorize_net',
            'provider_transaction_id' => $dto->transactionId,
            'provider_response_code' => (string) $dto->responseCode,
            'provider_response_message' => $dto->responseMessage,
            'last_four' => $transaction->last_four,
            'card_brand' => $transaction->card_brand,
            'notes' => $notes,
        ]);

        return $dto;
    }

    /**
     * Void a transaction
     */
    public function void(Transaction $transaction): TransactionResponseDTO
    {
        $response = $this->manager->voidTransaction($transaction->provider_transaction_id);

        $dto = TransactionResponseDTO::fromApiResponse($response);

        // Create void transaction record
        Transaction::create([
            'order_id' => $transaction->order_id,
            'payment_method_id' => $transaction->payment_method_id,
            'type' => Transaction::TYPE_VOID,
            'status' => $dto->isSuccessful() ? Transaction::STATUS_COMPLETED : Transaction::STATUS_FAILED,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency,
            'provider' => 'authorize_net',
            'provider_transaction_id' => $dto->transactionId,
            'provider_response_code' => (string) $dto->responseCode,
            'provider_response_message' => $dto->responseMessage,
        ]);

        return $dto;
    }

    /**
     * Create a transaction record in the database
     */
    protected function createTransactionRecord(
        TransactionResponseDTO $dto,
        int|string $orderId,
        ?PaymentMethod $paymentMethod,
        string $type,
        float $amount
    ): ?Transaction {
        try {
            return Transaction::create([
                'order_id' => $orderId,
                'payment_method_id' => $paymentMethod?->id,
                'type' => $type,
                'status' => $dto->isSuccessful() ? Transaction::STATUS_COMPLETED : Transaction::STATUS_FAILED,
                'amount' => $amount,
                'currency' => 'USD',
                'provider' => 'authorize_net',
                'provider_transaction_id' => $dto->transactionId,
                'provider_response_code' => (string) $dto->responseCode,
                'provider_response_message' => $dto->responseMessage,
                'provider_metadata' => $dto->rawResponse,
                'last_four' => $paymentMethod?->last_four,
                'card_brand' => $paymentMethod?->brand,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to create transaction record: ' . $e->getMessage());
            return null;
        }
    }
}
