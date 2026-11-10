<?php

namespace App\Support;

/**
 * Libellé du lieu de naissance avec la bonne préposition : « à Cotonou »,
 * mais « au Mali », « au Togo », « en France »… quand le lieu est un pays.
 * La valeur stockée en base n'est jamais modifiée : seul le rendu change.
 */
class LieuNaissance
{
    private const PAYS_AU = [
        'Mali', 'Togo', 'Niger', 'Bénin', 'Benin', 'Burkina Faso', 'Burkina', 'Sénégal', 'Senegal', 'Ghana',
        'Gabon', 'Cameroun', 'Tchad', 'Maroc', 'Liban', 'Congo', 'Canada', 'Japon', 'Brésil', 'Portugal',
        'Zaïre', 'Rwanda', 'Burundi', 'Soudan', 'Kenya', 'Nigeria', 'Nigéria', 'Qatar', 'Quatar', 'Danemark',
    ];

    private const PAYS_EN = [
        'France', 'Côte d\'Ivoire', 'Guinée', 'Mauritanie', 'Algérie', 'Tunisie', 'Italie', 'Espagne',
        'Allemagne', 'Belgique', 'Chine', 'Inde', 'Angleterre', 'Suisse', 'Libye', 'Égypte', 'Egypte',
        'RDC', 'RCA', 'Centrafrique', 'Guinée-Bissau', 'Grèce', 'Turquie', 'Russie',
    ];

    public static function libelle(?string $lieu): string
    {
        $lieu = trim((string) $lieu);

        if ($lieu === '') {
            return '';
        }

        foreach ([['au', self::PAYS_AU], ['en', self::PAYS_EN]] as [$prep, $liste]) {
            foreach ($liste as $pays) {
                if (preg_match('/^'.preg_quote($pays, '/').'(?![\p{L}\-])/iu', $lieu)) {
                    return "{$prep} {$lieu}";
                }
            }
        }

        return "à {$lieu}";
    }
}
