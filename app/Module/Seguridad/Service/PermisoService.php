<?php

declare(strict_types=1);

namespace App\Module\Seguridad\Service;

use App\Module\Seguridad\Repository\PermisoRepository;

final readonly class PermisoService
{
    public function __construct(private PermisoRepository $repository) {}

    public function listar(): array
    {
        return $this->repository->listar();
    }

    public function buscarPorId(int $permisoId): ?array
    {
        return $this->repository->buscarPorId($permisoId);
    }

    public function crear(
        string $modulo,
        string $accion,
        string $nombre,
        ?string $descripcion,
    ): void {
        $this->repository->crear($modulo, $accion, $nombre, $descripcion);
    }

    public function actualizar(
        int $permisoId,
        string $nombre,
        ?string $descripcion,
    ): void {
        $this->repository->actualizar($permisoId, $nombre, $descripcion);
    }
}
