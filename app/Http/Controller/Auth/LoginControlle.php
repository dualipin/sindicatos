<?php

namespace App\Http\Controller\Auth;

use App\Module\Usuario\Service\AutenticacionService;

final readonly class LoginControlle
{
    public function __construct(
        private AutenticacionService $service
    ) {
    }
}