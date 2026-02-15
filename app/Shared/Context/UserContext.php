<?php

namespace App\Shared\Context;

final readonly class UserContext
{
    private const string KEY = "user_id";

    public function get(): ?string
    {
        return $_SESSION[self::KEY] ?? null;
    }

    public function set(string $userId): void
    {
        $_SESSION[self::KEY] = $userId;
    }

    public function clear(): void
    {
        unset($_SESSION[self::KEY]);
    }
}
