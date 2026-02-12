<?php

declare(strict_types=1);

namespace App\Models\Prestamo;

final readonly class Amortizacion
{
    public function __construct(
        public int $prestamoId,
        public int $numeroPago,
        public int $tipoIngresoId,
        public \DateTimeImmutable $fechaProgramada,
        public string $saldoInicial,
        public string $capital,
        public string $interesOrdinario,
        public string $pagoTotalProgramado,
        public string $saldoFinal,
        public ?int $id = null,
        public string $estadoPago = 'pendiente',
        public ?\DateTimeImmutable $fechaPagoReal = null,
        public string $montoPagadoReal = '0',
        public int $diasAtraso = 0,
        public string $interesMoratorioGenerado = '0',
        public ?string $pagadoPor = null,
        public ?string $comprobantePago = null,
        public int $versionTabla = 1,
        public bool $activa = true,
    ) {}
}
