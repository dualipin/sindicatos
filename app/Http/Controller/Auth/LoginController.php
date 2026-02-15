<?php

namespace App\Http\Controller\Auth;

use App\Http\Response\Redirector;
use App\Infrastructure\Templating\RendererInterface;
use App\Module\Usuario\Service\AutenticacionService;
use App\Shared\View\LandingViewContextProvider;

final readonly class LoginController
{
    public function __construct(
        private AutenticacionService $authService,
        private RendererInterface $renderer,
        private Redirector $redirector,
        private LandingViewContextProvider $viewContextProvider,
    ) {}

    public function showLoginForm(
        string $redirectTo = "/",
        ?string $error = null,
    ): void {
        if ($this->authService->estaAutenticado()) {
            $this->redirector->to("/")->send();
        }

        $errorMsg = match ($error) {
            "invalid_credentials"
                => "Credenciales inválidas. Por favor, verifica tu correo y contraseña.",
            "inactive_account"
                => "Tu cuenta está inactiva. Contacta al administrador.",
            default => null,
        };

        $data = $this->viewContextProvider->get();
        $data["redirect"] = $redirectTo;
        $data["error"] = $errorMsg;
        $data["email"] = ""; // Initialize email

        // Use the public/cuenta/login.latte which is outside normal templates dir
        // We need to reference it correctly. Since render() usually takes absolute path:
        $templatePath = __DIR__ . "/../../../../public/cuenta/login.latte";

        $this->renderer->render($templatePath, $data);
    }

    public function login(
        string $email,
        string $password,
        string $redirectTo = "/",
    ): void {
        if ($this->authService->login($email, $password)) {
            // Validate redirect
            if (
                !str_starts_with($redirectTo, "/") ||
                str_starts_with($redirectTo, "//")
            ) {
                $redirectTo = "/";
            }
            $this->redirector->to($redirectTo)->send();
        }

        // Login failed
        $this->redirector
            ->to("/cuenta/login.php", [
                "error" => "invalid_credentials",
                "redirect_to" => $redirectTo,
            ])
            ->send();
    }
}
