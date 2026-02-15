<?php

declare(strict_types=1);

namespace App\Module\Usuario\Entity;

final readonly class Permiso
{
    public function __construct(
        public string $modulo,
        public string $accion,
        public string $nombre,
        public ?int $id = null,
        public ?string $descripcion = null,
    ) {}
}
