<?php

namespace App\Shared\View;

use App\Module\Sindicato\Repository\SindicatoRepository;
use App\Shared\Context\TenantContext;

final class SindicatoViewContextProvider extends AbstractViewContextProvider
{
    private ?array $cached = null;

    public function __construct(
        private readonly SindicatoRepository $sindicatoRepository,
        private readonly TenantContext $tenantContext,
        private readonly \App\Infrastructure\Config\AppConfig $settings,
    ) {}

    protected function resolve(): array
    {
        $tenantId = $this->tenantContext->get() ?? 1;

        $sindicatoBasic = $this->sindicatoRepository->buscarPorIdBasico(
            $tenantId,
        );
        $sindicatoFull = $this->sindicatoRepository->buscarPorId($tenantId);

        // Obtener integrantes del comité y valores (metas)
        $comite =
            $this->sindicatoRepository->obtenerIntegrantesComiteActivos(
                $tenantId,
            ) ?? [];
        $valores = $this->sindicatoRepository->obtenerValores($tenantId) ?? [];

        $metas = array_map(fn($v) => $v->valor, $valores);

        // Verificar existencia de imagenes en disco y normalizar foto de integrante
        $uploadBase = rtrim($this->settings->upload->publicDir, "/") . "/";

        $normalizedComite = array_map(function ($m) use ($uploadBase) {
            $foto = $m["foto"] ?? null;
            if ($foto && !is_file($uploadBase . $foto)) {
                // Si el archivo no existe, dejamos foto = null y mostraremos el icono por defecto en la vista
                $foto = null;
            }

            return [
                "id" => $m["id"] ?? ($m["integrante_id"] ?? null),
                "sindicatoId" =>
                    $m["sindicatoId"] ?? ($m["sindicato_id"] ?? null),
                "puesto" => $m["puesto"] ?? ($m["puesto_nombre"] ?? null),
                "nombre" => $m["nombre"] ?? null,
                "periodoInicio" => $m["periodoInicio"] ?? null,
                "periodoFin" => $m["periodoFin"] ?? null,
                "foto" => $foto,
                "biografia" => $m["biografia"] ?? null,
                "activo" => $m["activo"] ?? true,
            ];
        }, $comite);

        $contextoAcerca = (object) [
            "datos" => [
                "mision" => $sindicatoFull?->mision ?? "",
                "vision" => $sindicatoFull?->vision ?? "",
                "objetivos" => $sindicatoFull?->objetivo ?? "",
                "compromiso" => $sindicatoFull?->compromiso ?? "",
                "metas" => $metas,
            ],
            "comite" => $normalizedComite,
        ];

        return [
            "sindicatoActual" => $sindicatoBasic,
            "contextoAcerca" => $contextoAcerca,
            "comite" => $normalizedComite,
        ];
    }
}
