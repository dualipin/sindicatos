<?php

namespace App\Shared\Context;

final readonly class TenantContext
{
    private const string SindicateIdKey = 'syndicate_id';
    private const string UserIdKey = 'user_id';

    public function getSyndicateId(): int
    {
        return $_SESSION[self::SindicateIdKey] ?? 1;
    }

    public function setSyndicateId(int $syndicateId): void
    {
        $_SESSION[self::SindicateIdKey] = $syndicateId;
    }

    public function getUserId(): ?string
    {
        return $_SESSION[self::UserIdKey] ?? null;
    }

    public function setUserId(string $userId): void
    {
        $_SESSION[self::UserIdKey] = $userId;
    }

}