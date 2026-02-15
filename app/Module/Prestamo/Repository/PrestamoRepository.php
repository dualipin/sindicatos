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
        // Lógica simplificada: (pagado_real / programado) * 100 en el mes actual
        // Por ahora devolveremos un valor dummy o una consulta básica si es posible.
        // Dado que requiere tablas de amortización complejas, dejaremos un placeholder calculado.
        return 0.0;
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
}
