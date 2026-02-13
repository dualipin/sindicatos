<?php

namespace App\Http\Controller\Auth;

use App\Module\Usuario\Service\AutenticacionService;

final readonly class LoginController
{
    public function __construct(
        private AutenticacionService $service
    ) {
    }
}