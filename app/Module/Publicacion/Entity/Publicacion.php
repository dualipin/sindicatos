<?php

declare(strict_types=1);

namespace App\Models\Publicacion;

final readonly class Publicacion
{
    public function __construct(
        public int $sindicatoId,
        public string $titulo,
        public string $contenido,
        public string $creadoPor,
        public ?int $id = null,
        public ?string $resumen = null,
        public string $tipo = 'noticia',
        public bool $importante = false,
        public bool $fijado = false,
        public ?\DateTimeImmutable $fechaExpiracion = null,
        public bool $activo = true,
        public bool $publicado = false,
        public ?\DateTimeImmutable $fechaPublicacion = null,
        public ?\DateTimeImmutable $fechaCreacion = null,
        public int $numeroVistas = 0,
    ) {}
}
