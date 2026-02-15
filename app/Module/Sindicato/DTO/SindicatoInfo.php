<?php

namespace App\Module\Sindicato\DTO;

final readonly class SindicatoInfo
{
    public function __construct(
        public string $id,
        public string $nombre,
        public string $abreviacion
    ) {
    }
}