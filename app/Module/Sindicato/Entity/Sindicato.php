<?php

namespace App\Module\Sindicato\Entity;

final readonly class Sindicato
{
    public function __construct(
        public string $nombre,
        public string $abreviacion,
        public ?int $id = null,
        public ?string $direccion = null,
        public ?string $telefono = null,
        public ?string $correo = null,
        public ?string $facebook = null,
        public ?string $sitioWeb = null,
        public ?string $logo = null,
        public ?string $eslogan = null,
        public ?string $mision = null,
        public ?string $vision = null,
        public ?string $objetivo = null,
        public ?string $compromiso = null,
        public ?string $rfc = null,
        public ?string $representanteLegal = null,
        public bool $activo = true,
        public ?\DateTimeImmutable $fechaCreacion = null,
        public ?\DateTimeImmutable $fechaActualizacion = null,
    ) {
    }
}
