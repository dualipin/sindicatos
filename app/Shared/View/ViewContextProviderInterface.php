<?php

namespace App\Shared\View;

interface ViewContextProviderInterface
{
    public function get(): array;
}