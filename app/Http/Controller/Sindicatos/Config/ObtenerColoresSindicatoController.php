<?php

namespace App\Http\Controller\Sindicatos\Config;

use App\Module\Sindicato\Entity\Config;
use App\Module\Sindicato\Service\ConfiguracionService;

final readonly class ObtenerColoresSindicatoController
{
    public function __construct(
        private ConfiguracionService $service
    ) {
    }

    /**
     * @return Config[]|null
     */
    public function handle(): ?array
    {
        return $this->service->obtenerColoresSindicato();
    }
}