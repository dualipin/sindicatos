<?php

namespace App\Module\Sindicato\Entity;

class Valor
{
    public function __construct(
        public int $sindicatoId,
        public string $valor,
        public int $orden = 1,
        public ?int $id = null,
    ) {
    }
}