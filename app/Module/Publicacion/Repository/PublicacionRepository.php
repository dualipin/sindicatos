<?php

namespace App\Module\Publicacion\Repository;

use PDO;

final readonly class PublicacionRepository
{
    public function __construct(private PDO $pdo) {}

    public function obtenerUltimasNoticias(
        int $sindicatoId,
        int $limite = 5,
    ): array {
        // Asumiendo tabla 'publicaciones' o similar.
        // Si no existe en validación previa, devolver array vacío.
        return [];
    }
}
