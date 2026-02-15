<?php

namespace App\Module\Sindicato\Repository;

use App\Module\Sindicato\DTO\SindicatoBasic;
use App\Module\Sindicato\DTO\SindicatoInfo;
use App\Module\Sindicato\Entity\Sindicato;
use PDO;

final readonly class SindicatoRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * @return list<SindicatoInfo>|null
     */
    public function listadoActivoSimple(): ?array
    {
        $stmt = $this->pdo->query(
            "
        select sindicato_id, nombre, abreviacion from sindicatos where activo = true
        ",
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
            $results,
        );
    }

    public function buscarPorIdBasico(int $id): ?SindicatoBasic
    {
        $stmt = $this->pdo->prepare(
            "
        select sindicato_id, nombre, abreviacion, logo, direccion, eslogan, correo, sitio_web, facebook, telefono, activo
        from sindicatos where sindicato_id = :id
        ",
        );

        $stmt->execute([
            "id" => $id,
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
            activo: (bool) $row["activo"],
        );
    }

    public function buscarPorId(int $id): ?Sindicato
    {
        $stmt = $this->pdo->prepare(
            "
        select sindicato_id, nombre, abreviacion, logo, direccion, eslogan, correo, sitio_web, facebook, telefono,
               mision, vision, objetivo, compromiso, rfc, representante_legal, activo, fecha_creacion, fecha_actualizacion
        from sindicatos where sindicato_id = :id
        ",
        );

        $stmt->execute([
            "id" => $id,
        ]);

        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        $fechaCreacion = $row["fecha_creacion"]
            ? new \DateTimeImmutable($row["fecha_creacion"])
            : null;
        $fechaActualizacion = $row["fecha_actualizacion"]
            ? new \DateTimeImmutable($row["fecha_actualizacion"])
            : null;

        return new Sindicato(
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
            mision: $row["mision"] ?? null,
            vision: $row["vision"] ?? null,
            objetivo: $row["objetivo"] ?? null,
            compromiso: $row["compromiso"] ?? null,
            rfc: $row["rfc"] ?? null,
            representanteLegal: $row["representante_legal"] ?? null,
            activo: (bool) $row["activo"],
            fechaCreacion: $fechaCreacion,
            fechaActualizacion: $fechaActualizacion,
        );
    }

    /**
     * @return IntegranteComite[]|null
     */
    public function obtenerIntegrantesComiteActivos(int $sindicatoId): ?array
    {
        $stmt = $this->pdo->prepare(
            "
        select ic.integrante_id,
               ic.sindicato_id,
               ic.puesto_id,
               sp.nombre_puesto as puesto_nombre,
               ic.nombre,
               ic.periodo_inicio,
               ic.periodo_fin,
               ic.foto,
               ic.biografia,
               ic.activo
        from sindicato_integrante_comite ic
        left join sindicato_puestos sp on ic.puesto_id = sp.puesto_id
        where ic.sindicato_id = :sindicato_id and ic.activo = 1
          and (ic.periodo_fin is null or ic.periodo_fin >= curdate())
        order by sp.orden_jerarquico asc, ic.nombre asc
        ",
        );

        $stmt->execute(["sindicato_id" => $sindicatoId]);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return null;
        }

        return array_map(
            fn($r) => [
                "id" => (int) $r["integrante_id"],
                "sindicatoId" => (int) $r["sindicato_id"],
                "puestoId" => isset($r["puesto_id"])
                    ? (int) $r["puesto_id"]
                    : null,
                "puesto" => $r["puesto_nombre"] ?? null,
                "nombre" => $r["nombre"],
                "periodoInicio" => $r["periodo_inicio"]
                    ? new \DateTimeImmutable($r["periodo_inicio"])
                    : null,
                "periodoFin" => $r["periodo_fin"]
                    ? new \DateTimeImmutable($r["periodo_fin"])
                    : null,
                "foto" => $r["foto"] ?? null,
                "biografia" => $r["biografia"] ?? null,
                "activo" => (bool) $r["activo"],
            ],
            $rows,
        );
    }

    /**
     * @return Valor[]|null
     */
    public function obtenerValores(int $sindicatoId): ?array
    {
        $stmt = $this->pdo->prepare(
            "
        select valor_id, sindicato_id, valor, orden
        from sindicato_valores
        where sindicato_id = :sindicato_id
        order by orden asc
        ",
        );

        $stmt->execute(["sindicato_id" => $sindicatoId]);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return null;
        }

        return array_map(
            fn($r) => new \App\Module\Sindicato\Entity\Valor(
                sindicatoId: (int) $r["sindicato_id"],
                valor: $r["valor"],
                orden: (int) $r["orden"],
                id: (int) $r["valor_id"],
            ),
            $rows,
        );
    }

    /**
     * Guarda (reemplaza) los valores/metas de un sindicato.
     * Se elimina lo existente y se insertan los nuevos respetando el orden.
     *
     * @param int $sindicatoId
     * @param string[] $valores
     */
    public function guardarValores(int $sindicatoId, array $valores): void
    {
        $this->pdo->beginTransaction();
        try {
            $del = $this->pdo->prepare(
                "DELETE FROM sindicato_valores WHERE sindicato_id = :sindicato_id",
            );
            $del->execute(["sindicato_id" => $sindicatoId]);

            $ins = $this->pdo->prepare(
                "INSERT INTO sindicato_valores (sindicato_id, valor, orden) VALUES (:sindicato_id, :valor, :orden)",
            );

            $orden = 1;
            foreach ($valores as $v) {
                $ins->execute([
                    "sindicato_id" => $sindicatoId,
                    "valor" => $v,
                    "orden" => $orden,
                ]);
                $orden++;
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function actualizarConfiguracion(
        int $id,
        string $nombre,
        string $abreviacion,
        ?string $direccion,
        ?string $telefono,
        ?string $correo,
        ?string $facebook,
        ?string $sitioWeb,
        ?string $logo,
        ?string $eslogan,
        ?string $mision,
        ?string $vision,
        ?string $objetivo,
        ?string $compromiso,
        ?string $rfc,
        ?string $representanteLegal,
    ): void {
        $stmt = $this->pdo->prepare(
            "
        update sindicatos
        set nombre = :nombre,
            abreviacion = :abreviacion,
            direccion = :direccion,
            telefono = :telefono,
            correo = :correo,
            facebook = :facebook,
            sitio_web = :sitio_web,
            logo = :logo,
            eslogan = :eslogan,
            mision = :mision,
            vision = :vision,
            objetivo = :objetivo,
            compromiso = :compromiso,
            rfc = :rfc,
            representante_legal = :representante_legal
        where sindicato_id = :id
        ",
        );

        $stmt->execute([
            "id" => $id,
            "nombre" => $nombre,
            "abreviacion" => $abreviacion,
            "direccion" => $direccion,
            "telefono" => $telefono,
            "correo" => $correo,
            "facebook" => $facebook,
            "sitio_web" => $sitioWeb,
            "logo" => $logo,
            "eslogan" => $eslogan,
            "mision" => $mision,
            "vision" => $vision,
            "objetivo" => $objetivo,
            "compromiso" => $compromiso,
            "rfc" => $rfc,
            "representante_legal" => $representanteLegal,
        ]);
    }

    public function contarPuestos(int $sindicatoId): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM sindicato_puestos WHERE sindicato_id = :sindicato_id",
        );
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return (int) $stmt->fetchColumn();
    }

    public function contarIntegrantesActivos(int $sindicatoId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM sindicato_integrante_comite
            WHERE sindicato_id = :sindicato_id
              AND activo = 1
              AND (periodo_fin IS NULL OR periodo_fin >= CURDATE())
        ");
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Devuelve los puestos definidos para un sindicato (ordenados por jerarquía).
     * @return array<int,array{id:int,nombre:string}>|null
     */
    public function obtenerPuestos(int $sindicatoId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT puesto_id, nombre_puesto FROM sindicato_puestos WHERE sindicato_id = :sindicato_id ORDER BY orden_jerarquico ASC",
        );
        $stmt->execute(["sindicato_id" => $sindicatoId]);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return null;
        }

        return array_map(
            fn($r) => [
                "id" => (int) $r["puesto_id"],
                "nombre" => $r["nombre_puesto"],
            ],
            $rows,
        );
    }

    /**
     * Inserta un conjunto de puestos por defecto para el sindicato si la tabla está vacía.
     */
    public function insertarPuestosPorDefecto(int $sindicatoId): void
    {
        if ($this->contarPuestos($sindicatoId) > 0) {
            return; // ya existen puestos
        }

        $puestos = [
            'Secretario General',
            'Secretario General Suplente',
            'Secretario de Organización',
            'Secretario de Trabajos y Conflictos',
            'Secretario de Finanzas',
            'Secretario de Actas y Acuerdos',
            'Presidente de la Comisión de Honor y Justicia',
        ];

        $ins = $this->pdo->prepare(
            'INSERT INTO sindicato_puestos (sindicato_id, nombre_puesto, orden_jerarquico) VALUES (:sindicato_id, :nombre_puesto, :orden_jerarquico)'
        );

        $this->pdo->beginTransaction();
        try {
            $orden = 1;
            foreach ($puestos as $p) {
                $ins->execute([
                    'sindicato_id' => $sindicatoId,
                    'nombre_puesto' => $p,
                    'orden_jerarquico' => $orden,
                ]);
                $orden++;
            }
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Sincroniza la lista de integrantes del comité para un sindicato.
     * - Inserta nuevos integrantes
     * - Actualiza los existentes (por integrante_id)
     * - Marca como inactivos los que ya no aparecen en la lista enviada
     *
     * Formato esperado de $integrantes: array of [
     *   'id' => ?int, 'puestoId' => int, 'nombre' => string,
     *   'periodoInicio' => ?string (YYYY-MM-DD), 'periodoFin' => ?string, 'biografia' => ?string
     * ]
     */
    public function syncIntegrantesComite(
        int $sindicatoId,
        array $integrantes,
    ): void {
        $this->pdo->beginTransaction();
        try {
            // obtener integrantes actuales
            $stmt = $this->pdo->prepare(
                "SELECT integrante_id FROM sindicato_integrante_comite WHERE sindicato_id = :sindicato_id",
            );
            $stmt->execute(["sindicato_id" => $sindicatoId]);
            $rows = $stmt->fetchAll();
            $existingIds = array_map(
                fn($r) => (int) $r["integrante_id"],
                $rows,
            );

            $seen = [];

            $ins = $this->pdo->prepare(
                "INSERT INTO sindicato_integrante_comite (sindicato_id, puesto_id, nombre, periodo_inicio, periodo_fin, foto, biografia, activo) VALUES (:sindicato_id, :puesto_id, :nombre, :periodo_inicio, :periodo_fin, :foto, :biografia, :activo)",
            );

            $upd = $this->pdo->prepare(
                "UPDATE sindicato_integrante_comite SET puesto_id = :puesto_id, nombre = :nombre, periodo_inicio = :periodo_inicio, periodo_fin = :periodo_fin, biografia = :biografia, activo = :activo WHERE integrante_id = :id",
            );

            foreach ($integrantes as $it) {
                $id = isset($it["id"]) && $it["id"] ? (int) $it["id"] : null;
                $puestoId = (int) ($it["puestoId"] ?? 0);
                $nombre = trim((string) ($it["nombre"] ?? ""));
                $periodoInicio =
                    $it["periodoInicio"] !== "" && $it["periodoInicio"] !== null
                        ? $it["periodoInicio"]
                        : null;
                $periodoFin =
                    $it["periodoFin"] !== "" && $it["periodoFin"] !== null
                        ? $it["periodoFin"]
                        : null;
                $biografia = isset($it["biografia"]) ? $it["biografia"] : null;

                if ($id !== null && in_array($id, $existingIds, true)) {
                    $upd->execute([
                        "id" => $id,
                        "puesto_id" => $puestoId,
                        "nombre" => $nombre,
                        "periodo_inicio" => $periodoInicio,
                        "periodo_fin" => $periodoFin,
                        "biografia" => $biografia,
                        "activo" => 1,
                    ]);
                    $seen[] = $id;
                } else {
                    $ins->execute([
                        "sindicato_id" => $sindicatoId,
                        "puesto_id" => $puestoId,
                        "nombre" => $nombre,
                        "periodo_inicio" => $periodoInicio,
                        "periodo_fin" => $periodoFin,
                        "foto" => null,
                        "biografia" => $biografia,
                        "activo" => 1,
                    ]);
                    $seen[] = (int) $this->pdo->lastInsertId();
                }
            }

            // marcar como inactivos los que no fueron enviados
            $toDisable = array_diff($existingIds, $seen);
            if (!empty($toDisable)) {
                $placeholders = implode(
                    ",",
                    array_fill(0, count($toDisable), "?"),
                );
                $sql = "UPDATE sindicato_integrante_comite SET activo = 0 WHERE integrante_id IN ($placeholders)";
                $stmt2 = $this->pdo->prepare($sql);
                $stmt2->execute(array_values($toDisable));
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
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
