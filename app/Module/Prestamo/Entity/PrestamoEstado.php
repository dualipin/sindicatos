<?php

namespace App\Module\Prestamos;

enum PrestamoEstado: string
{
    case Pendiente = 'pendiente';
    case EnRevision = 'en_revision';
    case Aprobado = 'aprobado';
    case Rechazado = 'rechazado';
    case Cancelado = 'cancelado';
}
