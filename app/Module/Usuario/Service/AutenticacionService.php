<?php

namespace App\Module\Usuario\Service;

use App\Http\Response\Redirector;
use App\Infrastructure\Session\SessionManager;
use App\Module\Usuario\Entity\Usuario;
use App\Module\Usuario\Repository\UsuarioRepository;

final readonly class AutenticacionService
{
    private const SESSION_USER_ID = "user_id";
    private const SESSION_USER_EMAIL = "user_email";
    private const SESSION_SINDICATO_ID = "sindicato_id";

    public function __construct(
        private UsuarioRepository $usuarioRepository,
        private SessionManager $sessionManager,
        private Redirector $redirector,
    ) {}

    public function login(string $email, string $password): bool
    {
        $usuario = $this->usuarioRepository->buscarPorEmail($email);

        if ($usuario === null) {
            return false;
        }

        if (!$usuario->activo) {
            return false;
        }

        if (!password_verify($password, $usuario->passwordHash)) {
            return false;
        }

        $this->sessionManager->regenerate();
        $this->sessionManager->set(self::SESSION_USER_ID, $usuario->id);
        $this->sessionManager->set(self::SESSION_USER_EMAIL, $usuario->correo);
        $this->sessionManager->set(
            self::SESSION_SINDICATO_ID,
            $usuario->sindicatoId,
        );

        return true;
    }

    public function logout(): void
    {
        $this->sessionManager->destroy();
    }

    public function getUsuarioAutenticado(): ?Usuario
    {
        $email = $this->sessionManager->get(self::SESSION_USER_EMAIL);

        if ($email === null) {
            return null;
        }

        return $this->usuarioRepository->buscarPorEmail($email);
    }

    public function estaAutenticado(): bool
    {
        return $this->sessionManager->get(self::SESSION_USER_ID) !== null;
    }

    public function requireLogin(): void
    {
        if (!$this->estaAutenticado()) {
            $redirectTo = $_SERVER["REQUEST_URI"] ?? "/";
            $this->redirector
                ->to("/cuenta/login.php", ["redirect_to" => $redirectTo])
                ->send();
        }
    }
}
