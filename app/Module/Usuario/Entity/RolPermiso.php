<?php

declare(strict_types=1);

namespace App\Module\Usuario\Entity;
final readonly class RolPermiso
{
    public function __construct(
        public int $rolId,
        public int $permisoId,
        public ?int $id = null,
    ) {}
}
