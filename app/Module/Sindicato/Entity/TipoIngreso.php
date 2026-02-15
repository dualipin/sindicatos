<?php

namespace App\Module\Sindicato\Entity;

final readonly class TipoIngreso
{
    public function __construct(
        public int $sindicatoId,
        public string $nombre,
        public bool $esPeriodico = false,
        public bool $activo = true,
        public ?int $id = null,
        public ?string $descripcion = null,
        public ?int $frecuenciaDias = null,
        public ?int $mesPagoTentativo = null,
        public ?int $diaPagoTentativo = null,
    ) {
    }
}
