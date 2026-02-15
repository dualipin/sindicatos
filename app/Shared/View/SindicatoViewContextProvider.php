<?php

namespace App\Shared\View;

use App\Module\Sindicato\Repository\SindicatoRepository;
use App\Shared\Context\TenantContext;

final class SindicatoViewContextProvider extends AbstractViewContextProvider
{
    private ?array $cached = null;

    public function __construct(
        private readonly SindicatoRepository $sindicatoRepository,
        private readonly TenantContext $tenantContext,
    ) {}

    protected function resolve(): array
    {
        return [
            "sindicatoActual" => $this->sindicatoRepository->buscarPorIdBasico(
                $this->tenantContext->get() ?? 1,
            ),
        ];
    }
}
