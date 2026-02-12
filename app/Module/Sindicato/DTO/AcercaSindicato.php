<?php

namespace App\Module\Sindicato\DTO;

use App\Module\Sindicato\Entity\IntegranteComite;
use App\Module\Sindicato\Entity\Valor;

final readonly class AcercaSindicato
{
    /**
     * @param IntegranteComite[] $comite
     * @param string $mision
     * @param string $vision
     * @param string $objetivos
     * @param string $compromiso
     * @param Valor[] $metas
     */
    public function __construct(
        public array $comite,
        public string $mision,
        public string $vision,
        public string $objetivos,
        public string $compromiso,
        public array $metas,
    ) {
    }
}