<?php

namespace App\Module\Caja\Repository;

use PDO;

final readonly class CajaRepository
{
    public function __construct(private PDO $pdo) {}

    public function obtenerSaldoActual(int $sindicatoId): float
    {
        $stmt = $this->pdo->prepare("
            SELECT saldo_actual 
            FROM cajas 
            WHERE sindicato_id = :sindicato_id AND activa = 1 
            ORDER BY caja_id ASC 
            LIMIT 1
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return (float) $stmt->fetchColumn();
    }

    public function obtenerIngresosEgresosMes(
        int $sindicatoId,
        int $mes,
        int $anio,
    ): array {
        $sql = "
            SELECT 
                SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as total_ingresos,
                SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as total_egresos
            FROM caja_movimientos cm
            JOIN cajas c ON cm.caja_id = c.caja_id
            WHERE c.sindicato_id = :sindicato_id
            AND MONTH(cm.fecha_movimiento) = :mes
            AND YEAR(cm.fecha_movimiento) = :anio
            AND cm.fecha_eliminacion IS NULL
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            "sindicato_id" => $sindicatoId,
            "mes" => $mes,
            "anio" => $anio,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                "total_ingresos" => 0,
                "total_egresos" => 0,
            ];
    }
}
