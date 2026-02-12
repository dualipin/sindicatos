<?php

namespace App\Module\Sindicato\Repository;

use App\Module\Sindicato\Entity\Config;
use PDO;

final readonly class ConfiguracionRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /**
     * @param int $sindicatoId
     * @return Config[]|null
     */
    public function obtenerColores(int $sindicatoId): ?array
    {
        $stmt = $this->pdo->prepare(
            "
        select configuracion_id, clave, valor, tipo, sindicato_id from sindicato_configuraciones where sindicato_id=:sindicatoId and tipo = 'color'"
        );

        $stmt->execute([
            'sindicatoId' => $sindicatoId,
        ]);

        $results = $stmt->fetchAll();
        if (!$results) {
            return null;
        }

        return array_map(
            fn($row) => new Config(
                sindicatoId: $row["sindicato_id"],
                clave: $row["clave"],
                valor: $row["valor"],
                tipo: $row["tipo"],
                id: $row["configuracion_id"]
            ),
            $results
        );
    }
}