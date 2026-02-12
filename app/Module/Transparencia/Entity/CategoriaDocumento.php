<?php

declare(strict_types=1);

namespace App\Models\Transparencia;

final readonly class CategoriaDocumento
{
    public function __construct(
        public int $sindicatoId,
        public string $nombre,
        public ?int $id = null,
        public ?string $descripcion = null,
        public ?string $icono = null,
        public int $orden = 0,
    ) {}
}
