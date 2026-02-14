<?php

namespace App\Http\Controller\Sindicatos;

use App\Http\Response\Redirector;
use App\Infrastructure\Session\SessionManager;
use App\Module\Sindicato\Repository\SindicatoRepository;
use App\Shared\Context\TenantContext;

final readonly class EstablecerSindicatoController
{
    public function __construct(
        private SindicatoRepository $repository,
        private TenantContext $tenantContext,
        private SessionManager $sessionManager,
        private Redirector $redirector,
    ) {
    }

    public function handle(array $postData): void
    {
        $this->sessionManager->start();

        $id = isset($postData['id']) ? (int)$postData['id'] : 0;

        if ($id <= 0) {
            $this->redirector->to('/')->send();
            return;
        }

        $sindicato = $this->repository->buscarPorIdBasico($id);

        if ($sindicato === null) {
            $this->redirector->to('/')->send();
            return;
        }

        $this->tenantContext->setSyndicateId($id);

        $this->redirector->to('/')->send();
    }
}