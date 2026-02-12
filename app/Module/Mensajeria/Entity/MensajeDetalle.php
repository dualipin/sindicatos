<?php

declare(strict_types=1);

namespace App\Models\Mensajeria;

final readonly class MensajeDetalle
{
    public function __construct(
        public int $hiloId,
        public string $mensaje,
        public ?int $id = null,
        public ?string $autorUsuarioId = null,
        public bool $esRespuestaStaff = false,
        public ?string $adjuntoUrl = null,
        public ?\DateTimeImmutable $fechaEnvio = null,
        public bool $leido = false,
        public ?\DateTimeImmutable $fechaLectura = null,
    ) {}
}
