<?php

namespace App\Module\Sindicato\Entity;

class Config
{
    public function __construct(
        public int $sindicatoId,
        public string $clave,
        public string $valor,
        public string $tipo = 'texto',
        public ?int $id = null,
        public ?string $descripcion = null,
    ) {
    }
}