<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

/**
 * Filtro "cor_texto": preto ou branco, o que tiver mais contraste com a cor de fundo (#RRGGBB).
 */
final class CorExtension
{
    #[AsTwigFilter('cor_texto')]
    public function corTexto(?string $corFundo): string
    {
        if (null === $corFundo || 1 !== preg_match('/^#([0-9A-Fa-f]{2})([0-9A-Fa-f]{2})([0-9A-Fa-f]{2})$/', $corFundo, $partes)) {
            return '#000000';
        }

        $luminancia = 0.2126 * self::canal($partes[1])
            + 0.7152 * self::canal($partes[2])
            + 0.0722 * self::canal($partes[3]);

        // Contraste (WCAG) do texto preto e do texto branco sobre o fundo
        $contrastePreto = ($luminancia + 0.05) / 0.05;
        $contrasteBranco = 1.05 / ($luminancia + 0.05);

        return $contrasteBranco >= $contrastePreto ? '#FFFFFF' : '#000000';
    }

    private static function canal(string $hex): float
    {
        $valor = hexdec($hex) / 255;

        return $valor <= 0.04045 ? $valor / 12.92 : (($valor + 0.055) / 1.055) ** 2.4;
    }
}
