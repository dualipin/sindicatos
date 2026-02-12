<?php

declare(strict_types=1);

namespace App\Models\Caja;

final readonly class Corte
{
    public function __construct(
        public int $cajaId,
        public \DateTimeImmutable $periodoInicio,
        public \DateTimeImmutable $periodoFin,
        public string $saldoInicial,
        public string $totalIngresos,
        public string $totalEgresos,
        public string $saldoFinal,
        public ?int $id = null,
        public string $estado = 'abierto',
        public ?\DateTimeImmutable $fechaCierre = null,
        public ?string $cerradoPor = null,
        public ?string $observaciones = null,
        public ?string $rutaReportePdf = null,
    ) {}
}
