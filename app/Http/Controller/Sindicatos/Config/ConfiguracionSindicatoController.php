<?php

declare(strict_types=1);

namespace App\Http\Controller\Sindicatos\Config;

use App\Http\Response\Redirector;
use App\Infrastructure\Config\AppConfig;
use App\Infrastructure\Templating\RendererInterface;
use App\Module\Sindicato\Repository\SindicatoRepository;
use App\Module\Usuario\Service\AutenticacionService;
use App\Shared\View\LandingViewContextProvider;

final readonly class ConfiguracionSindicatoController
{
    public function __construct(
        private AutenticacionService $authService,
        private SindicatoRepository $repository,
        private RendererInterface $renderer,
        private Redirector $redirector,
        private LandingViewContextProvider $viewContextProvider,
        private AppConfig $settings,
    ) {}

    public function handle(
        string $method,
        array $postData,
        array $query,
        array $files,
    ): void {
        $usuario = $this->authService->getUsuarioAutenticado();

        if ($usuario === null) {
            $this->redirector->to("/cuenta/login.php")->send();
            return;
        }

        $roles = array_map("strtolower", $usuario->roles ?? []);
        $isAdmin =
            in_array("administrador", $roles, true) ||
            in_array("super_admin", $roles, true) ||
            in_array("super admin", $roles, true);

        if (!$isAdmin) {
            $this->redirector->to("/portal/dashboard/index.php")->send();
            return;
        }

        $sindicatoId = (int) ($usuario->sindicatoId ?? 0);
        if ($sindicatoId <= 0) {
            $this->redirector->to("/portal/dashboard/index.php")->send();
            return;
        }

        $error = null;

        if ($method === "POST") {
            // Importar puestos por defecto (botón en la UI)
            if (!empty($postData['import_default_puestos'])) {
                try {
                    $this->repository->insertarPuestosPorDefecto($sindicatoId);
                    $this->redirector
                        ->to('/portal/sindicatos/configuracion.php', ['success' => 'puestos_imported'])
                        ->send();
                    return;
                } catch (\Throwable $e) {
                    $error = 'No se pudieron importar los puestos por defecto.';
                }
            }

            $nombre = trim((string) ($postData["nombre"] ?? ""));
            $abreviacion = trim((string) ($postData["abreviacion"] ?? ""));
            $logoPath = $this->normalize($postData["logo_actual"] ?? null);

            $logoUpload = $files["logo_file"] ?? null;
            if (
                is_array($logoUpload) &&
                ($logoUpload["error"] ?? UPLOAD_ERR_NO_FILE) !==
                    UPLOAD_ERR_NO_FILE
            ) {
                $uploadResult = $this->storeLogo($logoUpload, $sindicatoId);
                if ($uploadResult["error"] !== null) {
                    $error = $uploadResult["error"];
                } else {
                    $logoPath = $uploadResult["path"];
                }
            }

            if ($nombre === "" || $abreviacion === "") {
                $error = "Completa el nombre y la abreviacion del sindicato.";
            } elseif ($error === null) {
                try {
                            $this->repository->actualizarConfiguracion(
                        $sindicatoId,
                        $nombre,
                        $abreviacion,
                        $this->normalize($postData["direccion"] ?? null),
                        $this->normalize($postData["telefono"] ?? null),
                        $this->normalize($postData["correo"] ?? null),
                        $this->normalize($postData["facebook"] ?? null),
                        $this->normalize($postData["sitio_web"] ?? null),
                        $logoPath,
                        $this->normalize($postData["eslogan"] ?? null),
                        $this->normalize($postData["mision"] ?? null),
                        $this->normalize($postData["vision"] ?? null),
                        $this->normalize($postData["objetivo"] ?? null),
                        $this->normalize($postData["compromiso"] ?? null),
                        $this->normalize($postData["rfc"] ?? null),
                        $this->normalize(
                            $postData["representante_legal"] ?? null,
                        ),
                    );

                    // Procesar metas/valores si vienen en el POST (name="metas[]")
                    $rawMetas = $postData['metas'] ?? [];
                    $metas = [];
                    if (is_string($rawMetas)) {
                        $lines = preg_split('/\r\n|\r|\n/', $rawMetas);
                        foreach ($lines as $ln) {
                            $v = trim((string) $ln);
                            if ($v !== '') {
                                $metas[] = $v;
                            }
                        }
                    } elseif (is_array($rawMetas)) {
                        foreach ($rawMetas as $m) {
                            $v = trim((string) $m);
                            if ($v !== '') {
                                $metas[] = $v;
                            }
                        }
                    }

                    if (count($metas) > 0 || is_array($rawMetas)) {
                        // Si el formulario envía un array vacío, eliminamos las metas existentes.
                        $this->repository->guardarValores($sindicatoId, $metas);
                    }

                    // Procesar puestos del comité si vienen en el POST (estructura: puestos[id][], puestos[nombre][], puestos[orden][])
                    $rawPuestos = $postData['puestos'] ?? null;
                    if (is_array($rawPuestos)) {
                        $puestos = [];

                        $ids = $rawPuestos['id'] ?? [];
                        $nombres = $rawPuestos['nombre'] ?? [];
                        $ordenes = $rawPuestos['orden'] ?? [];

                        $count = count($nombres);
                        for ($i = 0; $i < $count; $i++) {
                            $nombre = trim((string) ($nombres[$i] ?? ''));

                            if ($nombre === '') {
                                continue; // ignorar entradas vacías
                            }

                            $puestos[] = [
                                'id' => isset($ids[$i]) && $ids[$i] !== '' ? (int) $ids[$i] : null,
                                'nombre' => $nombre,
                                'orden' => isset($ordenes[$i]) ? (int) $ordenes[$i] : ($i + 1),
                            ];
                        }

                        $this->repository->syncPuestos($sindicatoId, $puestos);
                    }

                    // Procesar integrantes del comité si vienen en el POST (estructura: comite[id][], comite[puesto_id][], comite[nombre][], comite[periodo_inicio][], comite[periodo_fin][], comite[biografia][])
                    $rawComite = $postData['comite'] ?? null;
                    if (is_array($rawComite)) {
                        $integrantes = [];

                        $ids = $rawComite['id'] ?? [];
                        $puestos = $rawComite['puesto_id'] ?? [];
                        $nombres = $rawComite['nombre'] ?? [];
                        $periodosInicio = $rawComite['periodo_inicio'] ?? [];
                        $periodosFin = $rawComite['periodo_fin'] ?? [];
                        $biografias = $rawComite['biografia'] ?? [];

                        $count = max(count($nombres), count($puestos));
                        for ($i = 0; $i < $count; $i++) {
                            $nombre = trim((string) ($nombres[$i] ?? ''));
                            $puestoId = isset($puestos[$i]) ? (int) $puestos[$i] : 0;

                            // Si no hay nombre ni puesto, ignorar la entrada
                            if ($nombre === '' && $puestoId <= 0) {
                                continue;
                            }

                            $integrantes[] = [
                                'id' => isset($ids[$i]) && $ids[$i] !== '' ? (int) $ids[$i] : null,
                                'puestoId' => $puestoId,
                                'nombre' => $nombre,
                                'periodoInicio' => isset($periodosInicio[$i]) && $periodosInicio[$i] !== '' ? $periodosInicio[$i] : null,
                                'periodoFin' => isset($periodosFin[$i]) && $periodosFin[$i] !== '' ? $periodosFin[$i] : null,
                                'biografia' => isset($biografias[$i]) ? trim((string) $biografias[$i]) : null,
                            ];
                        }

                        if (!empty($integrantes)) {
                            $this->repository->syncIntegrantesComite($sindicatoId, $integrantes);
                        } else {
                            // si el formulario envía lista vacía explícita, desactivamos todos
                            $this->repository->syncIntegrantesComite($sindicatoId, []);
                        }
                    }

                    $this->redirector
                        ->to("/portal/sindicatos/configuracion.php", [
                            "success" => "updated",
                        ])
                        ->send();
                    return;
                } catch (\Throwable $exception) {
                    $error =
                        "No se pudo aplicar la configuracion. Intenta de nuevo.";
                }
            }
        }

        $sindicato = $this->repository->buscarPorId($sindicatoId);
        if ($sindicato === null) {
            $this->redirector->to("/portal/dashboard/index.php")->send();
            return;
        }

        // Obtener metas actuales para precargar en el formulario
        $valores = $this->repository->obtenerValores($sindicatoId) ?? [];
        $metas = array_map(fn($v) => $v->valor, $valores);

        // Obtener integrantes del comité y puestos para la sección administrativa
        $comite = $this->repository->obtenerIntegrantesComiteActivos($sindicatoId) ?? [];
        $puestosComite = $this->repository->obtenerPuestos($sindicatoId) ?? [];

        $context = $this->viewContextProvider->get();
        $context["usuario"] = $usuario;
        $context["sindicato"] = $sindicato;
        $context["metas"] = $metas;
        $context["success"] = $query["success"] ?? null;
        $context["error"] = $error;

        $this->renderer->render(
            __DIR__ .
                "/../../../../../public/portal/sindicatos/configuracion.latte",
            $context,
        );
    }

    private function normalize(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value !== "" ? $value : null;
    }

    /** @return array{path: ?string, error: ?string} */
    private function storeLogo(array $file, int $sindicatoId): array
    {
        if (($file["error"] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return [
                "path" => null,
                "error" => "No se pudo subir el logo. Intenta de nuevo.",
            ];
        }

        $maxSize = 2 * 1024 * 1024;
        if (($file["size"] ?? 0) > $maxSize) {
            return [
                "path" => null,
                "error" => "El logo excede el tamano maximo de 2MB.",
            ];
        }

        $tmpName = (string) ($file["tmp_name"] ?? "");
        if ($tmpName === "" || !is_uploaded_file($tmpName)) {
            return [
                "path" => null,
                "error" => "El archivo del logo no es valido.",
            ];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpName);
        $allowed = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp",
        ];

        if (!isset($allowed[$mime])) {
            return [
                "path" => null,
                "error" => "Formato de logo no permitido. Usa JPG, PNG o WEBP.",
            ];
        }

        $uploadDir = rtrim($this->settings->upload->publicDir, "/");
        if (!is_dir($uploadDir)) {
            $parentDir = dirname($uploadDir);
            if (!is_dir($parentDir) || !is_writable($parentDir)) {
                return [
                    "path" => null,
                    "error" =>
                        "No hay permisos para crear el directorio de cargas.",
                ];
            }

            if (!mkdir($uploadDir, 0775, true)) {
                return [
                    "path" => null,
                    "error" => "No se pudo crear el directorio de cargas.",
                ];
            }
        }

        if (!is_writable($uploadDir)) {
            return [
                "path" => null,
                "error" =>
                    "No hay permisos de escritura en el directorio de cargas.",
            ];
        }

        $targetDir = $uploadDir . "/sindicatos";
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0775, true)) {
                return [
                    "path" => null,
                    "error" => "No se pudo crear el directorio de logos.",
                ];
            }
        }

        if (!is_writable($targetDir)) {
            return [
                "path" => null,
                "error" =>
                    "No hay permisos de escritura en el directorio de logos.",
            ];
        }

        $extension = $allowed[$mime];
        $fileName = sprintf(
            "sindicato_%d_logo_%s.%s",
            $sindicatoId,
            date("YmdHis"),
            $extension,
        );
        $targetPath = $targetDir . "/" . $fileName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            return [
                "path" => null,
                "error" => "No se pudo guardar el logo en el servidor.",
            ];
        }

        return [
            "path" => "sindicatos/" . $fileName,
            "error" => null,
        ];
    }
}
