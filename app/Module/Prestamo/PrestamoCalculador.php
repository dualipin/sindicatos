<?php

namespace App\Module\Prestamos;

final class PrestamoCalculador
{
    public function calcularInteresesTotal(float $monto, float $tasa): float
    {
        return $monto * ($tasa / 100);
    }

    public function calcularMontoTotal(float $monto, float $tasa): float
    {
        return $monto + $this->calcularInteresTotal($monto, $tasa);
    }

    public function calcularCuotaMensual(float $montoTotal, int $plazo): float
    {
        if ($plazo <= 0) {
            throw new InvalidArgumentException('Plazo inválido');
        }

        return $montoTotal / $plazo;
    }
}