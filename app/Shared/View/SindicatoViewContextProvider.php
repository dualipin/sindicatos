<?php

namespace App\Shared\View;

use App\Module\Sindicato\Repository\SindicatoRepository;
use App\Shared\Context\TenantContext;

final class SindicatoViewContextProvider
{
    private ?array $cached = null;

    public function __construct(
        private readonly SindicatoRepository $sindicatoRepository,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function get(): array
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        $this->cached = [
            'sindicatos' => $this->sindicatoRepository->listadoActivoSimple(),
            'sindicatoActual' => $this->sindicatoRepository->buscarPorIdBasico(
                $this->tenantContext->getSyndicateId()
            )
        ];

        return $this->cached;
    }
}