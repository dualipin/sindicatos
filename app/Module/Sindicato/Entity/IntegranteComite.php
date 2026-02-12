<?php

namespace App\Module\Sindicato\Entity;

class IntegranteComite
{
    public function __construct(
        public int $sindicatoId,
        public int $puestoId,
        public string $nombre,
        public ?int $id = null,
        public ?\DateTimeImmutable $periodoInicio = null,
        public ?\DateTimeImmutable $periodoFin = null,
        public ?string $foto = null,
        public ?string $biografia = null,
        public bool $activo = true,
    ) {}
}