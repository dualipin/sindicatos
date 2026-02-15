<?php

declare(strict_types=1);

namespace App\Module\Usuario\Entity;

final readonly class UsuarioRol
{
    public function __construct(
        public string $usuarioId,
        public int $rolId,
        public ?int $id = null,
        public ?\DateTimeImmutable $fechaAsignacion = null,
        public ?string $asignadoPor = null,
    ) {
    }
}
