<?php

declare(strict_types=1);

namespace App\Shared\Enums;

enum Role: string
{
    case SUPER_ADMIN = "Super Admin";
    case FINANZAS = "Finanzas";
    case SECRETARIO_GENERAL = "Secretario General"; // Y otros miembros del comité
    case AGREMIADO = "Agremiado";
    case EXTERNO = "Externo";

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => "Super Administrador",
            self::FINANZAS => "Secretario de Finanzas",
            self::SECRETARIO_GENERAL => "Comité Ejecutivo",
            self::AGREMIADO => "Agremiado",
            self::EXTERNO => "Usuario Externo",
        };
    }
}
