<?php

declare(strict_types=1);

namespace App\Models\Transparencia;

final readonly class Archivo
{
    public function __construct(
        public int $sindicatoId,
        public int $categoriaDocId,
        public string $nombreArchivo,
        public string $rutaAlmacenamiento,
        public int $anio,
        public int $mes,
        public string $subidoPor,
        public ?int $id = null,
        public ?string $descripcion = null,
        public ?string $tipoArchivo = null,
        public ?int $tamanoBytes = null,
        public bool $esPublico = true,
        public ?array $etiquetas = null,
        public bool $activo = true,
        public ?\DateTimeImmutable $fechaSubida = null,
        public int $numeroDescargas = 0,
    ) {}
}
