<?php

declare(strict_types=1);

namespace App\Models\Prestamo;

final readonly class PagoExtraordinario
{
    public function __construct(
        public int $prestamoId,
        public string $tipoPago,
        public string $monto,
        public ?int $id = null,
        public ?\DateTimeImmutable $fechaPago = null,
        public ?string $aplicadoACapital = null,
        public ?string $aplicadoAInteres = null,
        public ?string $aplicadoAMoratorio = null,
        public bool $regeneroTablaAmortizacion = true,
        public ?int $versionTablaGenerada = null,
        public ?string $observaciones = null,
        public ?string $comprobantePago = null,
        public ?string $registradoPor = null,
    ) {}
}
