<?php

declare(strict_types=1);

namespace App\Models\Caja;

final readonly class Movimiento
{
    public function __construct(
        public int $cajaId,
        public int $categoriaId,
        public string $tipo,
        public string $monto,
        public string $descripcion,
        public string $comprobanteUrl,
        public string $creadoPor,
        public ?int $id = null,
        public ?string $usuarioRelacionadoId = null,
        public ?int $prestamoId = null,
        public bool $requiereAprobacion = false,
        public bool $aprobado = false,
        public ?string $aprobadoPor = null,
        public ?\DateTimeImmutable $fechaAprobacion = null,
        public ?int $corteId = null,
        public bool $conciliado = false,
        public ?\DateTimeImmutable $fechaMovimiento = null,
        public ?\DateTimeImmutable $fechaCreacion = null,
        public ?\DateTimeImmutable $fechaEliminacion = null,
    ) {}
}
