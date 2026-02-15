<?php

namespace App\Module\Sindicato\Entity;

final  class Puesto
{
    public function __construct(
        public ?int $id = null,
        public int $sindicatoId,
        public string $nombre,
        public ?string $descripcion = null,
        public int $nivel = 1,
    ) {
        if ($nivel < 1) {
            throw new \InvalidArgumentException("El nivel debe ser mayor o igual a 1");
        }
    }
}