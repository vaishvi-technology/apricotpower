<?php

namespace App\Shipping\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class ShipStationException extends Exception
{
    protected ?Response $response = null;

    protected ?array $errorDetails = null;

    /**
     * Create a new ShipStation exception.
     */
    public function __construct(
        string $message = 'ShipStation API error',
        int $code = 0,
        ?Exception $previous = null,
        ?Response $response = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->response = $response;

        if ($response) {
            $this->parseErrorDetails($response);
        }
    }

    /**
     * Create from an HTTP response.
     */
    public static function fromResponse(Response $response): self
    {
        $body = $response->json();
        $message = $body['message'] ?? $body['Message'] ?? 'Unknown ShipStation error';

        return new self(
            message: $message,
            code: $response->status(),
            response: $response
        );
    }

    /**
     * Create for authentication failure.
     */
    public static function authenticationFailed(): self
    {
        return new self(
            message: 'ShipStation authentication failed. Please check your API credentials.',
            code: 401
        );
    }

    /**
     * Create for rate limit exceeded.
     */
    public static function rateLimitExceeded(int $retryAfter = 0): self
    {
        $message = 'ShipStation rate limit exceeded.';
        if ($retryAfter > 0) {
            $message .= " Retry after {$retryAfter} seconds.";
        }

        return new self($message, 429);
    }

    /**
     * Create for connection timeout.
     */
    public static function connectionTimeout(): self
    {
        return new self(
            message: 'Connection to ShipStation timed out. Please try again.',
            code: 408
        );
    }

    /**
     * Create for invalid request.
     */
    public static function invalidRequest(string $details): self
    {
        return new self(
            message: "Invalid request to ShipStation: {$details}",
            code: 400
        );
    }

    /**
     * Create for missing configuration.
     */
    public static function missingConfiguration(): self
    {
        return new self(
            message: 'ShipStation API credentials are not configured.',
            code: 0
        );
    }

    /**
     * Parse error details from response.
     */
    protected function parseErrorDetails(Response $response): void
    {
        try {
            $body = $response->json();
            $this->errorDetails = $body;
        } catch (Exception) {
            $this->errorDetails = null;
        }
    }

    /**
     * Get the HTTP response.
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Get error details from the response.
     */
    public function getErrorDetails(): ?array
    {
        return $this->errorDetails;
    }

    /**
     * Check if this is a retryable error.
     */
    public function isRetryable(): bool
    {
        return in_array($this->getCode(), [408, 429, 500, 502, 503, 504]);
    }
}
