<?php

namespace App\Shared\Context;

use App\Infrastructure\Session\SessionManager;

final readonly class TenantContext
{
    private const string KEY = "syndicate_id";

    public function __construct(private SessionManager $manager) {}

    public function get(): ?int
    {
        return $this->manager->get(self::KEY);
    }

    public function set(int $syndicateId): void
    {
        $this->manager->set(self::KEY, $syndicateId);
    }

    public function clear(): void
    {
        $this->manager->remove(self::KEY);
    }
}
