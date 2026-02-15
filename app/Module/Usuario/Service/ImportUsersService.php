<?php

declare(strict_types=1);

namespace App\Module\Usuario\Service;

use App\Module\Usuario\Entity\Usuario;
use App\Module\Usuario\Repository\UsuarioRepository;
use DateTimeImmutable;

final readonly class ImportUsersService
{
    public function __construct(
        private UsuarioRepository $usuarioRepository
    ) {}

    public function import(string $csvPath, int $sindicatoId): void
    {
        if (!file_exists($csvPath)) {
            throw new \RuntimeException("Archivo no encontrado: $csvPath");
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            throw new \RuntimeException("No se pudo abrir el archivo");
        }

        // Saltar cabecera
        fgetcsv($handle);

        $tempPasswordHash = password_hash('temporal', PASSWORD_DEFAULT);

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 12) continue;

            // nombre,apellidos,direccion,telefono,email,categoria,departamento,nss,curp,fecha_nacimiento,fecha_ingreso,rol
            [
                $nombre, $apellidos, $direccion, $telefono, $email, 
                $categoria, $departamento, $nss, $curp, 
                $fechaNacimientoStr, $fechaIngresoStr, $rolNombre
            ] = $data;

            if (empty($email)) continue;

            $existente = $this->usuarioRepository->buscarPorEmail($email);
            if ($existente) continue;

            $usuarioId = bin2hex(random_bytes(16)); // UUID simplificado o generar uno real
            // Formato UUID: 8-4-4-4-12
            $usuarioId = sprintf('%08s-%04s-%04x-%04x-%12s',
                substr($usuarioId, 0, 8),
                substr($usuarioId, 8, 4),
                (hexdec(substr($usuarioId, 12, 4)) & 0x0fff) | 0x4000,
                (hexdec(substr($usuarioId, 16, 4)) & 0x3fff) | 0x8000,
                substr($usuarioId, 20, 12)
            );

            $fechaNacimiento = $this->parseDate($fechaNacimientoStr);
            $fechaIngreso = $this->parseDate($fechaIngresoStr);

            $usuario = new Usuario(
                sindicatoId: $sindicatoId,
                correo: $email,
                passwordHash: $tempPasswordHash,
                nombre: $nombre,
                apellidos: $apellidos,
                id: $usuarioId,
                curp: $curp ?: null,
                nss: $nss ?: null,
                fechaNacimiento: $fechaNacimiento,
                telefono: $telefono ?: null,
                direccion: $direccion ?: null,
                categoria: $categoria ?: null,
                departamento: $departamento ?: null,
                fechaIngresoLaboral: $fechaIngreso
            );

            $this->usuarioRepository->crear($usuario);

            // Asignar rol
            $rolId = $this->usuarioRepository->buscarRolIdPorNombre($rolNombre, $sindicatoId);
            if ($rolId) {
                $this->usuarioRepository->asignarRol($usuarioId, $rolId);
            }
        }

        fclose($handle);
    }

    private function parseDate(string $dateStr): ?DateTimeImmutable
    {
        if (empty($dateStr)) return null;
        
        // El formato en el CSV parece ser d/m/Y H:i
        $date = DateTimeImmutable::createFromFormat('d/m/Y H:i', $dateStr);
        if ($date) return $date;

        $date = DateTimeImmutable::createFromFormat('d/m/Y', $dateStr);
        if ($date) return $date;

        return null;
    }
}
