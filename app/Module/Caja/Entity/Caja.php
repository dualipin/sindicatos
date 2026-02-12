<?php

declare(strict_types=1);

namespace App\Models\Caja;

final readonly class Caja
{
    public function __construct(
        public int $sindicatoId,
        public string $nombre,
        public ?int $id = null,
        public ?string $descripcion = null,
        public string $saldoActual = '0',
        public bool $activa = true,
        public int $version = 1,
        public ?\DateTimeImmutable $fechaCreacion = null,
    ) {}
}
