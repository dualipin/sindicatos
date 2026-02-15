<?php

declare(strict_types=1);

namespace App\Module\Usuario\Entity;
final readonly class Rol
{
    public function __construct(
        public string $nombre,
        public ?int $id = null,
        public ?int $sindicatoId = null,
        public ?string $descripcion = null,
        public bool $esRolSistema = false,
        public bool $activo = true,
    ) {}
}
