<?php

namespace App\Module\Mensajeria\Repository;

use PDO;

final readonly class MensajeriaRepository
{
    public function __construct(private PDO $pdo) {}

    public function contarHilosPendientes(int $sindicatoId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(DISTINCT md.hilo_id)
            FROM mensajes_detalle md
            JOIN usuarios u ON u.usuario_id = md.autor_usuario_id
            WHERE u.sindicato_id = :sindicato_id
              AND md.es_respuesta_staff = 0
              AND md.leido = 0
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return (int) $stmt->fetchColumn();
    }
}
