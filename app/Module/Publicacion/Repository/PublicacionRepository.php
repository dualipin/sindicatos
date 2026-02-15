<?php

namespace App\Module\Publicacion\Repository;

use PDO;

final readonly class PublicacionRepository
{
    public function __construct(private PDO $pdo) {}

    public function obtenerPorTipo(
        string $tipo,
        int $sindicatoId,
        int $limite = 10,
    ): array {
        $stmt = $this->pdo->prepare("
            SELECT p.*, pi.ruta as imagen_portada
            FROM publicaciones p
            LEFT JOIN publicacion_imagenes pi ON p.publicacion_id = pi.publicacion_id AND pi.es_portada = 1
            WHERE p.sindicato_id = :sindicato_id 
              AND p.tipo = :tipo 
              AND p.activo = 1 
              AND p.publicado = 1
            ORDER BY p.fijado DESC, p.fecha_publicacion DESC
            LIMIT :limite
        ");

        $stmt->bindValue(":sindicato_id", $sindicatoId, PDO::PARAM_INT);
        $stmt->bindValue(":tipo", $tipo);
        $stmt->bindValue(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerUltimasNoticias(
        int $sindicatoId,
        int $limite = 5,
    ): array {
        $stmt = $this->pdo->prepare("
            SELECT publicacion_id, titulo, resumen, fecha_publicacion
            FROM publicaciones
            WHERE sindicato_id = :sindicato_id
              AND tipo = 'noticia'
              AND activo = 1
              AND publicado = 1
            ORDER BY fecha_publicacion DESC
            LIMIT :limite
        ");
        $stmt->bindValue(":sindicato_id", $sindicatoId, PDO::PARAM_INT);
        $stmt->bindValue(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
