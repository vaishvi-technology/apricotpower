<?php

namespace App\AuthorizeNet\DTOs;

class TransactionResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $transactionId,
        public readonly ?string $authCode,
        public readonly int $responseCode,
        public readonly string $responseMessage,
        public readonly ?string $avsResultCode = null,
        public readonly ?string $cvvResultCode = null,
        public readonly ?array $errors = [],
        public readonly ?array $rawResponse = null,
    ) {}

    public function isSuccessful(): bool
    {
        return $this->success && $this->responseCode === 1;
    }

    public function isDeclined(): bool
    {
        return $this->responseCode === 2;
    }

    public function isError(): bool
    {
        return $this->responseCode === 3;
    }

    public function isHeldForReview(): bool
    {
        return $this->responseCode === 4;
    }

    public function getMessage(): string
    {
        return $this->responseMessage;
    }

    public static function fromApiResponse($response): self
    {
        $transactionResponse = $response->getTransactionResponse();

        $success = $response->getMessages()->getResultCode() === 'Ok';
        $responseCode = $transactionResponse ? (int) $transactionResponse->getResponseCode() : 3;

        $errors = [];
        if ($transactionResponse && $transactionResponse->getErrors()) {
            foreach ($transactionResponse->getErrors() as $error) {
                $errors[] = [
                    'code' => $error->getErrorCode(),
                    'text' => $error->getErrorText(),
                ];
            }
        }

        // Extract response message - check errors first (for declined/failed), then messages (for success)
        $responseMessage = 'Unknown error';

        // For declined/error transactions, get message from errors array
        if (!empty($errors)) {
            $responseMessage = $errors[0]['text'] ?? 'Transaction failed';
        } elseif ($transactionResponse && $transactionResponse->getMessages()) {
            // For successful transactions, get from messages
            $messages = $transactionResponse->getMessages();
            if (is_array($messages) && count($messages) > 0) {
                $responseMessage = $messages[0]->getDescription();
            }
        }

        // Also check top-level API messages if still unknown
        if ($responseMessage === 'Unknown error' && $response->getMessages()) {
            $apiMessages = $response->getMessages()->getMessage();
            if (is_array($apiMessages) && count($apiMessages) > 0) {
                $responseMessage = $apiMessages[0]->getText();
            }
        }

        return new self(
            success: $success,
            transactionId: $transactionResponse?->getTransId(),
            authCode: $transactionResponse?->getAuthCode(),
            responseCode: $responseCode,
            responseMessage: $responseMessage,
            avsResultCode: $transactionResponse?->getAvsResultCode(),
            cvvResultCode: $transactionResponse?->getCvvResultCode(),
            errors: $errors,
            rawResponse: method_exists($response, 'getTransactionResponse') ?
                json_decode(json_encode($transactionResponse), true) : null,
        );
    }
}
