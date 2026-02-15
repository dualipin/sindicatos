<?php

namespace App\Module\Transparencia\Repository;

use PDO;

final readonly class TransparenciaRepository
{
    public function __construct(private PDO $pdo) {}

    public function contarDocumentosMes(
        int $sindicatoId,
        int $mes,
        int $anio,
    ): int {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) 
            FROM transparencia_archivos 
            WHERE sindicato_id = :sindicato_id
            AND MONTH(fecha_subida) = :mes
            AND YEAR(fecha_subida) = :anio
        ");
        $stmt->execute([
            "sindicato_id" => $sindicatoId,
            "mes" => $mes,
            "anio" => $anio,
        ]);
        return (int) $stmt->fetchColumn();
    }
}
