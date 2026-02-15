<?php

declare(strict_types=1);

namespace App\Module\Seguridad\Repository;

use PDO;

final readonly class PermisoRepository
{
    public function __construct(private PDO $pdo) {}

    public function listar(): array
    {
        $sql = "SELECT permiso_id, modulo, accion, nombre, descripcion
                FROM cat_permisos
                ORDER BY modulo, accion";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarPorId(int $permisoId): ?array
    {
        $sql = "SELECT permiso_id, modulo, accion, nombre, descripcion
                FROM cat_permisos
                WHERE permiso_id = :permiso_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(["permiso_id" => $permisoId]);
        $permiso = $stmt->fetch();

        return $permiso ?: null;
    }

    public function crear(
        string $modulo,
        string $accion,
        string $nombre,
        ?string $descripcion,
    ): void {
        $sql = "INSERT INTO cat_permisos (modulo, accion, nombre, descripcion)
                VALUES (:modulo, :accion, :nombre, :descripcion)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "modulo" => $modulo,
            "accion" => $accion,
            "nombre" => $nombre,
            "descripcion" => $descripcion,
        ]);
    }

    public function actualizar(
        int $permisoId,
        string $nombre,
        ?string $descripcion,
    ): void {
        $sql = "UPDATE cat_permisos
                SET nombre = :nombre,
                    descripcion = :descripcion
                WHERE permiso_id = :permiso_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "permiso_id" => $permisoId,
            "nombre" => $nombre,
            "descripcion" => $descripcion,
        ]);
    }
}
