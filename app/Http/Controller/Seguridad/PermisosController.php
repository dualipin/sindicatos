<?php

declare(strict_types=1);

namespace App\Http\Controller\Seguridad;

use App\Http\Response\Redirector;
use App\Infrastructure\Templating\RendererInterface;
use App\Module\Seguridad\Service\PermisoService;
use App\Module\Usuario\Service\AutenticacionService;
use App\Shared\View\LandingViewContextProvider;

final readonly class PermisosController
{
    public function __construct(
        private AutenticacionService $authService,
        private PermisoService $permisoService,
        private RendererInterface $renderer,
        private Redirector $redirector,
        private LandingViewContextProvider $viewContextProvider,
    ) {}

    public function handle(string $method, array $postData, array $query): void
    {
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
            $this->redirector->to("/portal/dashboard")->send();
            return;
        }

        $error = null;

        if ($method === "POST") {
            $action = (string) ($postData["action"] ?? "");

            if ($action === "create") {
                $modulo = trim((string) ($postData["modulo"] ?? ""));
                $accion = trim((string) ($postData["accion"] ?? ""));
                $nombre = trim((string) ($postData["nombre"] ?? ""));
                $descripcion = trim((string) ($postData["descripcion"] ?? ""));

                if ($modulo === "" || $accion === "" || $nombre === "") {
                    $error =
                        "Completa modulo, accion y nombre para crear el permiso.";
                } else {
                    try {
                        $this->permisoService->crear(
                            $modulo,
                            $accion,
                            $nombre,
                            $descripcion !== "" ? $descripcion : null,
                        );
                        $this->redirector
                            ->to("/portal/seguridad/permisos.php", [
                                "success" => "created",
                            ])
                            ->send();
                        return;
                    } catch (\Throwable $exception) {
                        $error =
                            "No se pudo crear el permiso. Verifica si ya existe.";
                    }
                }
            }

            if ($action === "update") {
                $permisoId = (int) ($postData["permiso_id"] ?? 0);
                $nombre = trim((string) ($postData["nombre"] ?? ""));
                $descripcion = trim((string) ($postData["descripcion"] ?? ""));

                if ($permisoId <= 0 || $nombre === "") {
                    $error =
                        "Selecciona un permiso valido y completa el nombre.";
                } else {
                    try {
                        $this->permisoService->actualizar(
                            $permisoId,
                            $nombre,
                            $descripcion !== "" ? $descripcion : null,
                        );
                        $this->redirector
                            ->to("/portal/seguridad/permisos.php", [
                                "success" => "updated",
                                "edit" => $permisoId,
                            ])
                            ->send();
                        return;
                    } catch (\Throwable $exception) {
                        $error = "No se pudo actualizar el permiso.";
                    }
                }
            }
        }

        $editId = isset($query["edit"]) ? (int) $query["edit"] : 0;
        $permisos = $this->permisoService->listar();
        $permisoEdit =
            $editId > 0 ? $this->permisoService->buscarPorId($editId) : null;

        $context = $this->viewContextProvider->get();
        $context["usuario"] = $usuario;
        $context["permisos"] = $permisos;
        $context["permisoEdit"] = $permisoEdit;
        $context["success"] = $query["success"] ?? null;
        $context["error"] = $error;

        $this->renderer->render(
            __DIR__ . "/../../../../public/portal/seguridad/permisos.latte",
            $context,
        );
    }
}
