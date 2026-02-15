<?php

namespace App\Module\Dashboard\Service;

use App\Module\Caja\Repository\CajaRepository;
use App\Module\Mensajeria\Repository\MensajeriaRepository;
use App\Module\Prestamo\Repository\PrestamoRepository;
use App\Module\Publicacion\Repository\PublicacionRepository;
use App\Module\Sindicato\Repository\SindicatoRepository;
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
        private SindicatoRepository $sindicatoRepository,
        private TransparenciaRepository $transparenciaRepository,
    ) {}

    public function getDatosSuperAdmin(int $sindicatoId): array
    {
        $documentosPendientes = $this->usuarioRepository->contarDocumentosPendientes(
            $sindicatoId,
        );
        $prestamosPendientes = $this->prestamoRepository->contarPendientesValidacion(
            $sindicatoId,
        );
        $alertas = $documentosPendientes + $prestamosPendientes;

        $puestosComite = $this->sindicatoRepository->contarPuestos(
            $sindicatoId,
        );
        $integrantesActivos = $this->sindicatoRepository->contarIntegrantesActivos(
            $sindicatoId,
        );

        $alertasDetalle = "";
        if ($alertas > 0) {
            $alertasDetalle = sprintf(
                "%d documentos pendientes, %d solicitudes en validacion",
                $documentosPendientes,
                $prestamosPendientes,
            );
        }

        return [
            "cartera_activa" => $this->prestamoRepository->sumaPrestamosActivos(
                $sindicatoId,
            ),
            "recuperacion" => $this->prestamoRepository->porcentajeRecuperacionQuincenal(
                $sindicatoId,
            ),
            "usuarios_activos" => $this->usuarioRepository->contarActivos(
                $sindicatoId,
            ),
            "alertas" => $alertas,
            "alertas_detalle" => $alertasDetalle,
            "comite_puestos" => $puestosComite,
            "comite_activos" => $integrantesActivos,
            "comite_completo" =>
                $puestosComite > 0
                    ? $integrantesActivos >= $puestosComite
                    : $integrantesActivos > 0,
        ];
    }

    public function getDatosFinanzas(int $sindicatoId): array
    {
        $mes = (int) date("m");
        $anio = (int) date("Y");

        $flujo = $this->cajaRepository->obtenerIngresosEgresosMes(
            $sindicatoId,
            $mes,
            $anio,
        );
        $ingresos = (float) ($flujo["total_ingresos"] ?? 0);
        $egresos = (float) ($flujo["total_egresos"] ?? 0);
        $total = $ingresos + $egresos;
        $porcentajeIngresos =
            $total > 0 ? (int) round(($ingresos / $total) * 100) : 0;
        $porcentajeEgresos = $total > 0 ? 100 - $porcentajeIngresos : 0;

        return [
            "saldo_caja" => $this->cajaRepository->obtenerSaldoActual(
                $sindicatoId,
            ),
            "flujo_mensual" => $flujo,
            "flujo_porcentaje_ingresos" => $porcentajeIngresos,
            "flujo_porcentaje_egresos" => $porcentajeEgresos,
            "solicitudes_pendientes" => $this->prestamoRepository->obtenerBandejaValidacion(
                $sindicatoId,
            ),
        ];
    }

    public function getDatosSecretario(int $sindicatoId): array
    {
        $mes = (int) date("m");
        $anio = (int) date("Y");
        $documentosMes = $this->transparenciaRepository->contarDocumentosMes(
            $sindicatoId,
            $mes,
            $anio,
        );
        $categorias = $this->transparenciaRepository->contarCategorias(
            $sindicatoId,
        );
        $meta = max(1, $categorias);
        $porcentaje = (int) min(100, round(($documentosMes / $meta) * 100));

        return [
            "mensajes_pendientes" => $this->mensajeriaRepository->contarHilosPendientes(
                $sindicatoId,
            ),
            "gestiones_mes" => $documentosMes,
            "transparencia_meta" => $meta,
            "transparencia_porcentaje" => $porcentaje,
            "agenda" => [],
        ];
    }

    public function getDatosAgremiado(
        string $usuarioId,
        int $sindicatoId,
    ): array {
        $mes = (int) date("m");
        $anio = (int) date("Y");
        $documentosMes = $this->transparenciaRepository->contarDocumentosMes(
            $sindicatoId,
            $mes,
            $anio,
        );
        $categorias = $this->transparenciaRepository->contarCategorias(
            $sindicatoId,
        );
        $meta = max(1, $categorias);
        $porcentaje = (int) min(100, round(($documentosMes / $meta) * 100));

        return [
            "prestamos" => $this->prestamoRepository->obtenerPorUsuario(
                $usuarioId,
            ),
            "noticias" => $this->publicacionRepository->obtenerUltimasNoticias(
                $sindicatoId,
            ),
            "proximo_pago" => $this->prestamoRepository->obtenerProximoPagoPorUsuario(
                $usuarioId,
            ),
            "transparencia_porcentaje" => $porcentaje,
            "cumpleanos" => $this->usuarioRepository->obtenerCumpleanosSemana(
                $sindicatoId,
            ),
            "documentos" => $this->usuarioRepository->obtenerDocumentosUsuario(
                $usuarioId,
            ),
        ];
    }

    public function getDatosExterno(string $usuarioId): array
    {
        $prestamo = $this->prestamoRepository->obtenerUltimoPrestamoPorUsuario(
            $usuarioId,
        );
        $planPagos = [];
        if ($prestamo !== null) {
            $planPagos = $this->prestamoRepository->obtenerPlanPagosPorPrestamo(
                (int) $prestamo["prestamo_id"],
                6,
            );
        }

        $estado = $prestamo !== null ? (string) $prestamo["estado"] : null;
        $paso = match ($estado) {
            "revision_documental", "validacion_firmas" => 2,
            "activo" => 3,
            default => 1,
        };
        $progreso = match ($paso) {
            2 => 66,
            3 => 100,
            default => 33,
        };

        return [
            "prestamos" => $this->prestamoRepository->obtenerPorUsuario(
                $usuarioId,
            ),
            "prestamo_actual" => $prestamo,
            "plan_pagos" => $planPagos,
            "timeline" => [
                "paso" => $paso,
                "progreso" => $progreso,
            ],
        ];
    }
}
