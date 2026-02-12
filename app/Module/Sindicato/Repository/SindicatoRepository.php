<?php

namespace App\Module\Sindicato\Repository;

use App\Module\Sindicato\DTO\SindicatoBasic;
use App\Module\Sindicato\DTO\SindicatoInfo;
use App\Module\Sindicato\Entity\Sindicato;
use PDO;

final readonly class SindicatoRepository
{
    public function __construct(
        private PDO $pdo,
    ) {
    }

    /**
     * @return list<SindicatoInfo>|null
     */
    public function listadoActivoSimple(): ?array
    {
        $stmt = $this->pdo->query(
            "
        select sindicato_id, nombre, abreviacion from sindicatos where activo = true
        "
        );

        $results = $stmt->fetchAll();
        if (!$results) {
            return null;
        }

        return array_map(
            fn($row) => new SindicatoInfo(
                id: $row["sindicato_id"],
                nombre: $row["nombre"],
                abreviacion: $row["abreviacion"],
            ),
            $results
        );
    }

    public function buscarPorIdBasico(int $id): ?SindicatoBasic
    {
        $stmt = $this->pdo->prepare(
            "
        select sindicato_id, nombre, abreviacion, logo, direccion, eslogan, correo, sitio_web, facebook, telefono, activo
        from sindicatos where sindicato_id = :id
        "
        );

        $stmt->execute([
            'id' => $id,
        ]);

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        return new SindicatoBasic(
            nombre: $row["nombre"],
            abreviacion: $row["abreviacion"],
            id: $row["sindicato_id"],
            direccion: $row["direccion"] ?? null,
            telefono: $row["telefono"] ?? null,
            correo: $row["correo"] ?? null,
            facebook: $row["facebook"] ?? null,
            sitioWeb: $row["sitio_web"] ?? null,
            logo: $row["logo"] ?? null,
            eslogan: $row["eslogan"] ?? null,
            activo: (bool)$row["activo"],
        );
    }


//
//
//    public function buscarPorId(int $id): ?Sindicato
//    {
//        $stmt = $this->pdo->prepare(
//            "
//        select * from sindicatos where sindicato_id = :id"
//        );
//
//        $stmt->execute([
//            'id' => $id,
//        ]);
//
//
//        $row = $stmt->fetch();
//        if (!$row) {
//            return null;
//        }
//
//        return new Sindicato(
//            nombre: $row["nombre"],
//            abreviacion: $row["abreviacion"],
//            id: $row["sindicato_id"],
//            sitioWeb: $row['sitio_web'] ?? '',
//            telefono: $row['telefono'] ?? '',
//            correo: $row['correo'] ?? '',
//            facebook: $row['facebook'] ?? '',
//            direccion: $row['direccion'] ?? '',
//            eslogan: $row['eslogan'] ?? '',
//            vision: $row['vision'] ?? '',
//            rfc: $row['rfc'] ?? '',
//            logo: $row['logo'] ?? '',
//            representanteLegal: $row['representanteLegal'] ?? '',
//            compromiso: $row['compromiso'] ?? '',
//            mision: $row['mision'] ?? '',
//            activo: (bool)$row["activo"]
//        );
//    }
}