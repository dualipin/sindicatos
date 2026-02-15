<?php

namespace App\Http\Controller\Sindicatos;

use App\Module\Sindicato\DTO\SindicatoInfo;
use App\Module\Sindicato\Repository\SindicatoRepository;

final readonly class ObtenerListadoSimpleController
{
    public function __construct(
        private SindicatoRepository $repository,
    ) {
    }

    /**
     * @return SindicatoInfo[]|null
     */
    public function handle(): ?array
    {
        return $this->repository->listadoActivoSimple();
    }
}