<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * Fuso único da aplicação: formulário, banco, Twig e consultas usam o horário de Brasília.
     */
    public const FUSO_HORARIO = 'America/Sao_Paulo';

    public function boot(): void
    {
        date_default_timezone_set(self::FUSO_HORARIO);

        parent::boot();
    }
}
