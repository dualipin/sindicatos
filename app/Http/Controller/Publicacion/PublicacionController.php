<?php

declare(strict_types=1);

namespace App\Http\Controller\Publicacion;

use App\Infrastructure\Templating\RendererInterface;
use App\Module\Publicacion\View\PublicacionViewContextProvider;
use App\Module\Usuario\Service\AutenticacionService;
use App\Shared\View\LandingViewContextProvider;

final readonly class PublicacionController
{
    public function __construct(
        private AutenticacionService $authService,
        private RendererInterface $renderer,
        private LandingViewContextProvider $landingContext,
        private PublicacionViewContextProvider $publicacionContext,
    ) {}

    public function noticias(): void
    {
        $this->render("noticias");
    }

    public function avisos(): void
    {
        $this->render("avisos");
    }

    public function gestiones(): void
    {
        $this->render("gestiones");
    }

    private function render(string $page): void
    {
        $usuario = $this->authService->getUsuarioAutenticado();

        $data = [
            ...$this->landingContext->get(),
            ...$this->publicacionContext->get(),
            "usuario" => $usuario,
            "linkActivo" => $page,
        ];

        $templatePath =
            __DIR__ . "/../../../../public/portal/publicaciones/{$page}.latte";
        $this->renderer->render($templatePath, $data);
    }
}
