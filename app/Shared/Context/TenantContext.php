<?php

namespace App\Shared\Context;

final readonly class TenantContext
{
    private const string SYNDICATE_ID_KEY = 'syndicate_id';
    private const string USER_ID_KEY = 'user_id';

    public function getSyndicateId(): int
    {
        return $_SESSION[self::SYNDICATE_ID_KEY] ?? 1;
    }

    public function setSyndicateId(int $syndicateId): void
    {
        $_SESSION[self::SYNDICATE_ID_KEY] = $syndicateId;
    }

    public function getUserId(): ?string
    {
        return $_SESSION[self::USER_ID_KEY] ?? null;
    }

    public function setUserId(string $userId): void
    {
        $_SESSION[self::USER_ID_KEY] = $userId;
    }

}