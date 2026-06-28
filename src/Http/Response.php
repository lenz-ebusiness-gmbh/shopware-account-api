<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Http;

use Lenz\ShopwareAccountApi\Exception\ApiException;

/**
 * Lightweight, framework-agnostic representation of an HTTP response.
 *
 * @phpstan-type Headers array<string, list<string>>
 */
final class Response
{
    /**
     * @param array<string, list<string>> $headers header names are lower-cased
     */
    public function __construct(
        public readonly int $status,
        public readonly array $headers,
        public readonly string $body,
    ) {
    }

    public function isSuccessful(): bool
    {
        return $this->status >= 200 && $this->status < 300;
    }

    /**
     * Decodes the JSON body to an associative array.
     *
     * An empty body returns an empty array (some endpoints return no content).
     *
     * @return array<int|string, mixed>
     */
    public function json(): array
    {
        $trimmed = trim($this->body);
        if ($trimmed === '' || $trimmed === 'false' || $trimmed === 'null') {
            return [];
        }
        try {
            $data = json_decode($trimmed, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ApiException('Invalid JSON in response: ' . $e->getMessage(), $this->status, $this->body, $e);
        }
        return is_array($data) ? $data : [];
    }

    /**
     * Returns the first value of a (lower-cased) header, or null.
     */
    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)][0] ?? null;
    }
}
