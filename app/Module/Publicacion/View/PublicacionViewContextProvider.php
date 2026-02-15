<?php

namespace App\Module\Publicacion\View;

use App\Module\Publicacion\Repository\PublicacionRepository;
use App\Shared\Context\TenantContext;
use App\Shared\View\ViewContextProviderInterface;

final readonly class PublicacionViewContextProvider implements
    ViewContextProviderInterface
{
    public function __construct(
        private PublicacionRepository $repository,
        private TenantContext $tenantContext,
    ) {}

    public function get(): array
    {
        $sindicatoId = $this->tenantContext->get() ?? 1; // Default to 1 if not set

        return [
            "noticias" => $this->repository->obtenerPorTipo(
                "noticia",
                $sindicatoId,
            ),
            "avisos" => $this->repository->obtenerPorTipo(
                "aviso",
                $sindicatoId,
            ),
            "gestiones" => $this->repository->obtenerPorTipo(
                "gestion",
                $sindicatoId,
            ),
            "eventos" => $this->repository->obtenerPorTipo(
                "evento",
                $sindicatoId,
            ),
        ];
    }
}
