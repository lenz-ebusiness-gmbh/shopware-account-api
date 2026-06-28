<?php

declare(strict_types=1);

namespace Lenz\ShopwareAccountApi\Token;

use Lenz\ShopwareAccountApi\Exception\ShopwareAccountApiException;

/**
 * Persists the token as a JSON file so it can be reused across CLI runs / requests.
 *
 * The file is written with 0600 permissions because it contains a bearer token.
 */
final class FileTokenStorage implements TokenStorageInterface
{
    public function __construct(private readonly string $path)
    {
    }

    public function get(): ?Token
    {
        if (!is_file($this->path)) {
            return null;
        }
        $raw = file_get_contents($this->path);
        if ($raw === false || $raw === '') {
            return null;
        }
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return null;
        }
        $value = $data['value'] ?? null;
        $expiresAt = $data['expiresAt'] ?? null;
        if (!is_string($value) || $value === '' || !is_string($expiresAt) || $expiresAt === '') {
            return null;
        }
        try {
            return new Token($value, new \DateTimeImmutable($expiresAt));
        } catch (\Exception) {
            return null;
        }
    }

    public function save(Token $token): void
    {
        $json = json_encode([
            'value'     => $token->value,
            'expiresAt' => $token->expiresAt->format(\DateTimeInterface::ATOM),
        ], JSON_THROW_ON_ERROR);

        $dir = \dirname($this->path);
        if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new ShopwareAccountApiException("Token storage directory not writable: $dir");
        }
        if (file_put_contents($this->path, $json, LOCK_EX) === false) {
            throw new ShopwareAccountApiException("Unable to write token file: {$this->path}");
        }
        @chmod($this->path, 0600);
    }

    public function clear(): void
    {
        if (is_file($this->path)) {
            @unlink($this->path);
        }
    }
}
