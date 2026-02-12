<?php

namespace App\Module\Prestamos;

use App\Shared\Context\TenantContext;

final readonly class PrestamoService
{
    public function __construct(
        private PrestamoRepository $prestamoRepository,
        private PrestamoCalculador $prestamoCalculador,
        private TenantContext $tenantContext,
    ) {
    }

    public function aprobarPrestamo(int $prestamoId): void
    {
        $sindicatoId = $this->tenantContext->getSindicatoId();

        $prestamo = $this->repository->buscarPorId($prestamoId, $sindicatoId);

        if (!$prestamo) {
            throw new RuntimeException('Préstamo no encontrado');
        }

        $prestamo->aprobar();

        $this->repository->guardar($prestamo);
    }

    public function calcularResumen(int $prestamoId): array
    {
        $sindicatoId = $this->tenantContext->getSindicatoId();

        $prestamo = $this->repository->buscarPorId($prestamoId, $sindicatoId);

        if (!$prestamo) {
            throw new RuntimeException('No encontrado');
        }

        $total = $this->calculator->calcularMontoTotal(
            $prestamo->getMonto(),
            $prestamo->getTasaInteres()
        );

        $cuota = $this->calculator->calcularCuotaMensual(
            $total,
            $prestamo->getPlazoMeses()
        );

        return [
            'total' => $total,
            'cuota' => $cuota,
        ];
    }
}