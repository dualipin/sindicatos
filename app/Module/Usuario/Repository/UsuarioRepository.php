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

    public function contarActivos(int $sindicatoId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM usuarios WHERE sindicato_id = :sindicato_id AND activo = 1",
        );
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return (int) $stmt->fetchColumn();
    }

    public function contarDocumentosPendientes(int $sindicatoId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM usuario_documentacion ud
            JOIN usuarios u ON u.usuario_id = ud.usuario_id
            WHERE u.sindicato_id = :sindicato_id
              AND ud.estado = 'pendiente'
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * @return array<string, mixed>[]
     */
    public function obtenerDocumentosUsuario(string $usuarioId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT tipo_documento, estado, fecha_subida
            FROM usuario_documentacion
            WHERE usuario_id = :usuario_id
            ORDER BY fecha_subida DESC
        ");
        $stmt->execute(["usuario_id" => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<string, mixed>[]
     */
    public function obtenerCumpleanosSemana(int $sindicatoId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT usuario_id, nombre, apellidos, fecha_nacimiento,
                   CASE
                       WHEN STR_TO_DATE(
                           CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(fecha_nacimiento, '%m-%d')),
                           '%Y-%m-%d'
                       ) < CURDATE()
                       THEN DATE_ADD(
                           STR_TO_DATE(
                               CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(fecha_nacimiento, '%m-%d')),
                               '%Y-%m-%d'
                           ),
                           INTERVAL 1 YEAR
                       )
                       ELSE STR_TO_DATE(
                           CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(fecha_nacimiento, '%m-%d')),
                           '%Y-%m-%d'
                       )
                   END AS proximo_cumple
            FROM usuarios
            WHERE sindicato_id = :sindicato_id
              AND activo = 1
              AND fecha_nacimiento IS NOT NULL
            HAVING proximo_cumple BETWEEN CURDATE()
                                  AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            ORDER BY proximo_cumple ASC
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
