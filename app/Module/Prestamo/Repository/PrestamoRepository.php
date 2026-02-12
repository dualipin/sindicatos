<?php

namespace App\Module\Prestamos;

final readonly class PrestamoRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    public function buscarPorId(int $id, int $sindicatoId): ?Prestamo
    {
        $stmt = $this->pdo->prepare(
            "
            SELECT * FROM prestamos
            WHERE sindicato_id = :id AND sindicato_id = :sindicato_id
        "
        );

        $stmt->execute([
            'id' => $id,
            'sindicato_id' => $sindicatoId,
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Prestamo(
            id: (int)$row['id'],
            sindicatoId: (int)$row['sindicato_id'],
            usuarioId: (int)$row['usuario_id'],
            monto: (float)$row['monto_aprobado'],
            tasaInteres: (float)$row['tasa_interes_aplicada'],
            plazoMeses: (int)$row['plazo_quincenas'],
            estado: $row['estado'],
            fechaSolicitud: new DateTimeImmutable($row['fecha_solicitud']),
        );
    }

}