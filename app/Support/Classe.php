<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Affichage des noms de classe avec le suffixe ordinal en exposant
 * ("3ème-E2" → 3ᵉᵐᵉ-E2), pour Saint Jean-Baptiste uniquement. La valeur
 * stockée en base n'est jamais modifiée : seul le rendu change.
 */
class Classe
{
    /** "3ème", "1ère", "2nde", "Tle" : le suffixe passe en exposant. */
    private const MOTIF = '/^(\d+|T)(ème|ère|nde|le)(?=[-\s]|$)/u';

    /** Caractères Unicode en exposant, pour les menus déroulants (pas d'accent possible). */
    private const EXPOSANTS = ['è' => 'ᵉ', 'e' => 'ᵉ', 'm' => 'ᵐ', 'r' => 'ʳ', 'n' => 'ⁿ', 'd' => 'ᵈ', 'l' => 'ˡ'];

    public static function texte(?string $classe): string
    {
        $classe = (string) $classe;

        if (! self::concerne()) {
            return $classe;
        }

        return preg_replace_callback(
            self::MOTIF,
            fn ($m) => $m[1].strtr($m[2], self::EXPOSANTS),
            $classe
        );
    }

    /** Vrai exposant HTML, accents conservés, pour les cartes et les pages. */
    public static function html(?string $classe): HtmlString
    {
        $classe = e((string) $classe);

        if (self::concerne()) {
            $classe = preg_replace(self::MOTIF, '$1<sup>$2</sup>', $classe);
        }

        return new HtmlString($classe);
    }

    private static function concerne(): bool
    {
        return config('ecole.slug') === 'jean-baptiste';
    }
}
