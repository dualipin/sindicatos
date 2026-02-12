<?php

declare(strict_types=1);

namespace App\Module\Usuario\Entity;
final readonly class UsuarioDocumentacion
{
    public function __construct(
        public string $usuarioId,
        public string $tipoDocumento,
        public string $rutaArchivo,
        public ?int $id = null,
        public string $estado = 'pendiente',
        public ?string $observaciones = null,
        public ?\DateTimeImmutable $fechaSubida = null,
        public ?\DateTimeImmutable $fechaValidacion = null,
        public ?string $validadoPor = null,
    ) {}
}
