<?php

namespace App\Module\Sindicato\Entity;

class TasaInteres
{
    public function __construct(
        public int $sindicatoId,
        public string $nombre,
        public bool $esAgremiado,
        public bool $esAhorrador,
        public string $tasaAnual,
        public ?int $id = null,
        public bool $activa = true,
        public ?\DateTimeImmutable $fechaVigenciaInicio = null,
        public ?\DateTimeImmutable $fechaVigenciaFin = null,
    ) {}
}