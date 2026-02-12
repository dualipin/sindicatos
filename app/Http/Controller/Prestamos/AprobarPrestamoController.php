<?php

namespace App\Http\Controller\Prestamos;

use App\Module\Prestamos\PrestamoService;

final readonly class AprobarPrestamoController
{
    public function __construct(
        private PrestamoService $service
    ) {
    }

    public function handle(int $prestamoId): void
    {
        $this->service->aprobarPrestamo($prestamoId);
        // redirect
    }
}
