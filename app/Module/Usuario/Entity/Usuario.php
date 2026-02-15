<?php

declare(strict_types=1);

namespace App\Module\Usuario\Entity;
final readonly class Usuario
{
    public function __construct(
        public int $sindicatoId,
        public string $correo,
        public string $passwordHash,
        public string $nombre,
        public string $apellidos,
        public ?string $id = null,
        public bool $activo = true,
        public bool $esAgremiado = true,
        public bool $esAhorrador = false,
        public ?string $curp = null,
        public ?string $rfc = null,
        public ?string $nss = null,
        public ?\DateTimeImmutable $fechaNacimiento = null,
        public ?string $foto = null,
        public ?string $telefono = null,
        public ?string $direccion = null,
        public ?string $bancoNombre = null,
        public ?string $cuentaBancaria = null,
        public ?string $clabeInterbancaria = null,
        public ?string $categoria = null,
        public ?string $departamento = null,
        public ?string $salarioBase = null,
        public ?string $salarioQuincenal = null,
        public ?\DateTimeImmutable $fechaIngresoLaboral = null,
        public ?\DateTimeImmutable $ultimoIngreso = null,
        public ?\DateTimeImmutable $fechaCreacion = null,
        public ?\DateTimeImmutable $fechaActualizacion = null,
        public ?\DateTimeImmutable $fechaEliminacion = null,
        public array $roles = [],
    ) {}
}
