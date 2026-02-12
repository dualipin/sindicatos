<?php

namespace App\Module\Sindicato\DTO;

final readonly class SindicatoBasic
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
        public bool $activo = true,
    ) {
    }
}