<?php

namespace App\Module\Usuario\Repository;

use App\Module\Usuario\Entity\Usuario;
use PDO;

final class UsuarioRepository
{
    public function __construct(private readonly PDO $pdo) {}

    public function buscarPorEmail(string $email): ?Usuario
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM usuarios WHERE correo = :email LIMIT 1",
        );
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $roles = $this->obtenerRolesUsuario($row["usuario_id"]);

        return new Usuario(
            sindicatoId: (int) $row["sindicato_id"],
            correo: $row["correo"],
            passwordHash: $row["contra"],
            nombre: $row["nombre"],
            apellidos: $row["apellidos"],
            id: $row["usuario_id"],
            activo: (bool) $row["activo"],
            roles: $roles,
        );
    }

    public function crear(Usuario $usuario): void
    {
        $sql = "INSERT INTO usuarios (
            usuario_id, sindicato_id, correo, contra, nombre, apellidos, 
            curp, rfc, nss, fecha_nacimiento, telefono, direccion, 
            categoria, departamento, fecha_ingreso_laboral
        ) VALUES (
            :id, :sindicato_id, :correo, :contra, :nombre, :apellidos, 
            :curp, :rfc, :nss, :fecha_nacimiento, :telefono, :direccion, 
            :categoria, :departamento, :fecha_ingreso_laboral
        )";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "id" => $usuario->id,
            "sindicato_id" => $usuario->sindicatoId,
            "correo" => $usuario->correo,
            "contra" => $usuario->passwordHash,
            "nombre" => $usuario->nombre,
            "apellidos" => $usuario->apellidos,
            "curp" => $usuario->curp,
            "rfc" => $usuario->rfc,
            "nss" => $usuario->nss,
            "fecha_nacimiento" => $usuario->fechaNacimiento?->format("Y-m-d"),
            "telefono" => $usuario->telefono,
            "direccion" => $usuario->direccion,
            "categoria" => $usuario->categoria,
            "departamento" => $usuario->departamento,
            "fecha_ingreso_laboral" => $usuario->fechaIngresoLaboral?->format(
                "Y-m-d",
            ),
        ]);
    }

    public function asignarRol(string $usuarioId, int $rolId): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuario_roles (usuario_id, rol_id) VALUES (:usuario_id, :rol_id)",
        );
        $stmt->execute([
            "usuario_id" => $usuarioId,
            "rol_id" => $rolId,
        ]);
    }

    public function buscarRolIdPorNombre(string $nombre, int $sindicatoId): ?int
    {
        $stmt = $this->pdo->prepare(
            "SELECT rol_id FROM cat_roles WHERE nombre = :nombre AND sindicato_id = :sindicato_id LIMIT 1",
        );
        $stmt->execute(["nombre" => $nombre, "sindicato_id" => $sindicatoId]);
        $val = $stmt->fetchColumn();
        return $val ? (int) $val : null;
    }

    /**
     * @return string[]
     */
    private function obtenerRolesUsuario(string $usuarioId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT cr.nombre 
            FROM usuario_roles ur
            JOIN cat_roles cr ON ur.rol_id = cr.rol_id
            WHERE ur.usuario_id = :usuario_id
        ");
        $stmt->execute(["usuario_id" => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
