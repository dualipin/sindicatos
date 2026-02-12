<?php

namespace App\Shared\View;

use App\Module\Sindicato\Repository\SindicatoRepository;

final class ListadoSindicatoViewContextProvider extends AbstractViewContextProvider
{
    public function __construct(
        private readonly SindicatoRepository $repository
    ) {
    }

    protected function resolve(): array
    {
        return [
            'sindicatos' => $this->repository->listadoActivoSimple()
        ];
    }
}
