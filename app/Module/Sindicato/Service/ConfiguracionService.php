<?php

namespace App\Module\Sindicato\Service;

use App\Module\Sindicato\Repository\ConfiguracionRepository;
use App\Shared\Context\TenantContext;

class ConfiguracionService
{
    public function __construct(
        private readonly ConfiguracionRepository $repository,
        private readonly TenantContext $context,
    ) {}

    public function obtenerColoresSindicato()
    {
        $id = $this->context->get() ?? 1;
        return $this->repository->obtenerColores($id);
    }
}
