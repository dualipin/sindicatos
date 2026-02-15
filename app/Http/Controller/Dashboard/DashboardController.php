<?php

namespace App\Http\Controller\Dashboard;

use App\Http\Response\Redirector;
use App\Infrastructure\Templating\RendererInterface;
use App\Module\Dashboard\Service\DashboardService;
use App\Module\Usuario\Service\AutenticacionService;
use App\Shared\Enums\Role;
use App\Shared\View\LandingViewContextProvider;

final readonly class DashboardController
{
    public function __construct(
        private AutenticacionService $authService,
        private DashboardService $dashboardService,
        private RendererInterface $renderer,
        private Redirector $redirector,
        private LandingViewContextProvider $viewContextProvider,
    ) {}

    public function index(): void
    {
        $usuario = $this->authService->getUsuarioAutenticado();

        if ($usuario === null) {
            $this->redirector->to("/cuenta/login.php")->send();
            return;
        }

        $sindicatoId = $usuario->sindicatoId;
        $roles = $usuario->roles; // Array de strings

        // Determinar rol prioritario (Super Admin > Finanzas > Secretario > Agremiado > Externo)
        // Esto es una simplificación, idealmente se maneja con una estrategia más robusta

        $context = $this->viewContextProvider->get();
        $context["usuario"] = $usuario;

        if (
            in_array(Role::SUPER_ADMIN->value, $roles) ||
            in_array("Super Admin", $roles)
        ) {
            $context["metrics"] = $this->dashboardService->getDatosSuperAdmin(
                $sindicatoId,
            );
            $this->renderer->render(
                __DIR__ .
                    "/../../../../public/portal/dashboard/super_admin.dashboard.latte",
                $context,
            );
            return;
        }

        if (
            in_array(Role::FINANZAS->value, $roles) ||
            in_array("Finanzas", $roles)
        ) {
            $context["metrics"] = $this->dashboardService->getDatosFinanzas(
                $sindicatoId,
            );
            $this->renderer->render(
                __DIR__ .
                    "/../../../../public/portal/dashboard/finanzas.dashboard.latte",
                $context,
            );
            return;
        }

        if (
            in_array(Role::SECRETARIO_GENERAL->value, $roles) ||
            in_array("Secretario General", $roles)
        ) {
            $context["metrics"] = $this->dashboardService->getDatosSecretario(
                $sindicatoId,
            );
            $this->renderer->render(
                __DIR__ .
                    "/../../../../public/portal/dashboard/secretario.dashboard.latte",
                $context,
            );
            return;
        }

        // Agremiado
        if ($usuario->esAgremiado) {
            $context["metrics"] = $this->dashboardService->getDatosAgremiado(
                $usuario->id,
                $sindicatoId,
            );
            $this->renderer->render(
                __DIR__ .
                    "/../../../../public/portal/dashboard/agremiado.dashboard.latte",
                $context,
            );
            return;
        }

        // Externo (Default)
        $context["metrics"] = $this->dashboardService->getDatosExterno(
            $usuario->id,
        );
        $this->renderer->render(
            __DIR__ .
                "/../../../../public/portal/dashboard/externo.dashboard.latte",
            $context,
        );
    }
}
