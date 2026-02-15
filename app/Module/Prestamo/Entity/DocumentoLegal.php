<?php

declare(strict_types=1);

namespace App\Models\Prestamo;

final readonly class DocumentoLegal
{
    public function __construct(
        public int $prestamoId,
        public string $tipoDocumento,
        public string $rutaArchivo,
        public ?int $id = null,
        public int $version = 1,
        public bool $requiereFirmaUsuario = false,
        public ?string $firmaUsuarioUrl = null,
        public ?\DateTimeImmutable $fechaFirmaUsuario = null,
        public bool $requiereValidacionFinanzas = false,
        public bool $validadoPorFinanzas = false,
        public ?string $validadoPor = null,
        public ?\DateTimeImmutable $fechaValidacion = null,
        public ?string $observacionesValidacion = null,
        public ?\DateTimeImmutable $fechaGeneracion = null,
        public ?string $generadoPor = null,
    ) {}
}
