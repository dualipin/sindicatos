<?php

declare(strict_types=1);

namespace App\Models\Prestamo;

final readonly class Comprobante
{
    public function __construct(
        public int $prestamoId,
        public string $tipoComprobante,
        public string $folioComprobante,
        public string $monto,
        public ?int $id = null,
        public ?int $amortizacionId = null,
        public ?string $descripcion = null,
        public ?\DateTimeImmutable $fechaEmision = null,
        public ?string $rutaPdf = null,
    ) {}
}
