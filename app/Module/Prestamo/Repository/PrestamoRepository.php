<?php

namespace App\Module\Prestamo\Repository;

use App\Module\Prestamo\Entity\Prestamo;
use PDO;
use DateTimeImmutable;

final readonly class PrestamoRepository
{
    public function __construct(private PDO $pdo) {}

    public function buscarPorId(int $id, int $sindicatoId): ?Prestamo
    {
        // ... (existing logic placeholder if needed, or simplified for now)
        return null;
    }

    public function sumaPrestamosActivos(int $sindicatoId): float
    {
        $stmt = $this->pdo->prepare("
            SELECT COALESCE(SUM(saldo_pendiente), 0)
            FROM prestamos
            WHERE sindicato_id = :sindicato_id
            AND estado = 'activo'
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return (float) $stmt->fetchColumn();
    }

    public function porcentajeRecuperacionQuincenal(int $sindicatoId): float
    {
        $stmt = $this->pdo->prepare("
            SELECT
                COALESCE(SUM(pa.pago_total_programado), 0) AS total_programado,
                COALESCE(SUM(pa.monto_pagado_real), 0) AS total_pagado
            FROM prestamo_amortizacion pa
            JOIN prestamos p ON pa.prestamo_id = p.prestamo_id
            WHERE p.sindicato_id = :sindicato_id
              AND MONTH(pa.fecha_programada) = MONTH(CURDATE())
              AND YEAR(pa.fecha_programada) = YEAR(CURDATE())
              AND pa.activa = 1
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            "total_programado" => 0,
            "total_pagado" => 0,
        ];

        $programado = (float) $row["total_programado"];
        $pagado = (float) $row["total_pagado"];

        if ($programado <= 0) {
            return 0.0;
        }

        return round(($pagado / $programado) * 100, 2);
    }

    public function conteoPorEstado(int $sindicatoId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT estado, COUNT(*) as total
            FROM prestamos
            WHERE sindicato_id = :sindicato_id
            GROUP BY estado
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function obtenerPorUsuario(string $usuarioId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM prestamos
            WHERE usuario_id = :usuario_id
            ORDER BY fecha_solicitud DESC
        ");
        $stmt->execute(["usuario_id" => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<string, mixed>[]
     */
    public function obtenerBandejaValidacion(int $sindicatoId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, u.nombre, u.apellidos
            FROM prestamos p
            JOIN usuarios u ON p.usuario_id = u.usuario_id
            WHERE p.sindicato_id = :sindicato_id
            AND p.estado IN ('revision_documental', 'validacion_firmas')
            ORDER BY p.fecha_solicitud ASC
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPendientesValidacion(int $sindicatoId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM prestamos
            WHERE sindicato_id = :sindicato_id
              AND estado IN ('revision_documental', 'validacion_firmas')
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerUltimoPrestamoPorUsuario(string $usuarioId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM prestamos
            WHERE usuario_id = :usuario_id
            ORDER BY fecha_solicitud DESC
            LIMIT 1
        ");
        $stmt->execute(["usuario_id" => $usuarioId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * @return array<string, mixed>[]
     */
    public function obtenerPlanPagosPorPrestamo(
        int $prestamoId,
        int $limite = 6,
    ): array {
        $stmt = $this->pdo->prepare("
            SELECT numero_pago, pago_total_programado, fecha_programada, estado_pago
            FROM prestamo_amortizacion
            WHERE prestamo_id = :prestamo_id
              AND activa = 1
            ORDER BY numero_pago ASC
            LIMIT :limite
        ");
        $stmt->bindValue(":prestamo_id", $prestamoId, PDO::PARAM_INT);
        $stmt->bindValue(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerProximoPagoPorUsuario(string $usuarioId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT pa.fecha_programada, pa.pago_total_programado
            FROM prestamo_amortizacion pa
            JOIN prestamos p ON pa.prestamo_id = p.prestamo_id
            WHERE p.usuario_id = :usuario_id
              AND p.estado = 'activo'
              AND pa.estado_pago = 'pendiente'
              AND pa.activa = 1
            ORDER BY pa.fecha_programada ASC
            LIMIT 1
        ");
        $stmt->execute(["usuario_id" => $usuarioId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
