<?php

declare(strict_types=1);

namespace App\Models\Prestamo;

final readonly class Reestructuracion
{
    public function __construct(
        public int $prestamoOriginalId,
        public int $prestamoNuevoId,
        public string $motivo,
        public string $saldoPendienteOriginal,
        public string $interesesPendientes,
        public string $moratoriosPendientes,
        public string $nuevoMontoTotal,
        public ?int $id = null,
        public ?string $nuevaTasaInteres = null,
        public ?int $nuevoPlazoQuincenas = null,
        public ?\DateTimeImmutable $fechaReestructuracion = null,
        public ?string $autorizadoPor = null,
        public ?string $observaciones = null,
    ) {}
}
