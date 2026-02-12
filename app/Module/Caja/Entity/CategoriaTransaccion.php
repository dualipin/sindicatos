<?php

declare(strict_types=1);

namespace App\Models\Caja;

final readonly class CategoriaTransaccion
{
    public function __construct(
        public int $sindicatoId,
        public string $nombre,
        public string $tipo,
        public ?int $id = null,
        public ?string $descripcion = null,
        public bool $activa = true,
    ) {}
}
