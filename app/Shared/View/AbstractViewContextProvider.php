<?php

namespace App\Shared\View;

abstract class AbstractViewContextProvider implements ViewContextProviderInterface
{
    private ?array $cached = null;

    final public function get(): array
    {
        if ($this->cached !== null) {
            return $this->cached;
        }

        $this->cached = $this->resolve();

        return $this->cached;
    }

    abstract protected function resolve(): array;
}
