<?php

namespace App\Module\Usuario\Repository;

use App\Module\Usuario\Entity\Usuario;
use PDO;

final class UsuarioRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function buscarPorEmail(): ?Usuario
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE correo = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Usuario(
            correo: $row['correo'],
            passwordHash: $row['contra'],
            id: $row['usuario_id'],
            activo: $row['activo']
        );
    }
}