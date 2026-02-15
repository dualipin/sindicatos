<?php

declare(strict_types=1);

namespace App\Models\Publicacion;

final readonly class PublicacionAdjunto
{
    public function __construct(
        public int $publicacionId,
        public string $nombreArchivo,
        public string $ruta,
        public ?int $id = null,
        public ?string $tipoArchivo = null,
        public ?int $tamanoBytes = null,
    ) {}
}
