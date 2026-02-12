<?php

declare(strict_types=1);

namespace App\Models\Mensajeria;

final readonly class HiloContacto
{
    public function __construct(
        public int $sindicatoId,
        public string $tipo,
        public string $asunto,
        public ?int $id = null,
        public ?string $usuarioId = null,
        public string $prioridad = 'media',
        public string $estado = 'abierto',
        public ?string $nombreExterno = null,
        public ?string $correoExterno = null,
        public ?string $telefonoExterno = null,
        public ?string $asignadoA = null,
        public ?\DateTimeImmutable $fechaCreacion = null,
        public ?\DateTimeImmutable $fechaUltimoMensaje = null,
        public ?\DateTimeImmutable $fechaCierre = null,
    ) {}
}
