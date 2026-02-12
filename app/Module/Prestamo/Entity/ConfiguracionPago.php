<?php

declare(strict_types=1);

namespace App\Models\Prestamo;

final readonly class ConfiguracionPago
{
    public function __construct(
        public int $prestamoId,
        public int $tipoIngresoId,
        public string $montoTotalADescontar,
        public ?int $id = null,
        public int $numeroCuotas = 1,
        public ?string $montoPorCuota = null,
        public string $metodoInteres = 'simple_aleman',
        public ?string $rutaDocumentoComprobante = null,
        public string $estadoDocumento = 'pendiente',
        public ?string $observacionesDocumento = null,
        public ?\DateTimeImmutable $fechaValidacionDocumento = null,
    ) {}
}
