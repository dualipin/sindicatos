<?php

declare(strict_types=1);

namespace App\Models\Publicacion;

final readonly class PublicacionImagen
{
    public function __construct(
        public int $publicacionId,
        public string $ruta,
        public ?int $id = null,
        public int $orden = 0,
        public bool $esPortada = false,
    ) {}
}
