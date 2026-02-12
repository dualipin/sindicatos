<?php

namespace App\Shared\View;

final readonly class LandingViewContextProvider implements ViewContextProviderInterface
{
    public function __construct(
        private ListadoSindicatoViewContextProvider $listadoSindicatoViewContextProvider,
        private SindicatoViewContextProvider $sindicatoViewContextProvider
    ) {
    }

    public function get(): array
    {
        return [
            ...$this->listadoSindicatoViewContextProvider->get(),
            ...$this->sindicatoViewContextProvider->get()
        ];
    }
}