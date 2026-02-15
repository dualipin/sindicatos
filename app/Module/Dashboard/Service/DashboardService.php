<?php

namespace App\Module\Dashboard\Service;

use App\Module\Caja\Repository\CajaRepository;
use App\Module\Mensajeria\Repository\MensajeriaRepository;
use App\Module\Prestamo\Repository\PrestamoRepository;
use App\Module\Publicacion\Repository\PublicacionRepository;
use App\Module\Transparencia\Repository\TransparenciaRepository;
use App\Module\Usuario\Repository\UsuarioRepository;

final readonly class DashboardService
{
    public function __construct(
        private CajaRepository $cajaRepository,
        private PrestamoRepository $prestamoRepository,
        private UsuarioRepository $usuarioRepository,
        private MensajeriaRepository $mensajeriaRepository,
        private PublicacionRepository $publicacionRepository,
        private TransparenciaRepository $transparenciaRepository,
    ) {}

    public function getDatosSuperAdmin(int $sindicatoId): array
    {
        return [
            "cartera_activa" => $this->prestamoRepository->sumaPrestamosActivos(
                $sindicatoId,
            ),
            "recuperacion" => $this->prestamoRepository->porcentajeRecuperacionQuincenal(
                $sindicatoId,
            ),
            // 'membresia' => $this->usuarioRepository->obtenerMetricasMembresia($sindicatoId), // Pendiente implementar en Repo
            "usuarios_activos" => 0, // Placeholder
        ];
    }

    public function getDatosFinanzas(int $sindicatoId): array
    {
        $mes = (int) date("m");
        $anio = (int) date("Y");

        return [
            "saldo_caja" => $this->cajaRepository->obtenerSaldoActual(
                $sindicatoId,
            ),
            "flujo_mensual" => $this->cajaRepository->obtenerIngresosEgresosMes(
                $sindicatoId,
                $mes,
                $anio,
            ),
            "solicitudes_pendientes" => $this->prestamoRepository->obtenerBandejaValidacion(
                $sindicatoId,
            ),
        ];
    }

    public function getDatosSecretario(int $sindicatoId): array
    {
        return [
            "mensajes_pendientes" => $this->mensajeriaRepository->contarHilosPendientes(
                $sindicatoId,
            ),
            // 'citas' => ...
        ];
    }

    public function getDatosAgremiado(
        string $usuarioId,
        int $sindicatoId,
    ): array {
        return [
            "prestamos" => $this->prestamoRepository->obtenerPorUsuario(
                $usuarioId,
            ),
            "noticias" => $this->publicacionRepository->obtenerUltimasNoticias(
                $sindicatoId,
            ),
        ];
    }

    public function getDatosExterno(string $usuarioId): array
    {
        return [
            "prestamos" => $this->prestamoRepository->obtenerPorUsuario(
                $usuarioId,
            ),
        ];
    }
}
