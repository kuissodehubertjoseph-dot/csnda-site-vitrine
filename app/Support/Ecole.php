<?php

namespace App\Support;

class Ecole
{
    /**
     * Logo de l'école encodé en data URI (base64), utilisé dans les templates de carte
     * générés par Browsershot : évite tout appel HTTP vers le serveur pendant le rendu.
     */
    public static function logoDataUri(): string
    {
        return static::fichierVersDataUri(config('ecole.logo'));
    }

    /**
     * Signature scannée du directeur, si elle a été importée depuis les paramètres.
     * Chaîne vide si aucun fichier n'a encore été déposé : le gabarit laisse alors
     * l'espace vide plutôt que d'afficher un espace réservé.
     */
    public static function signatureDataUri(): string
    {
        return static::fichierVersDataUri(config('ecole.signature'));
    }

    /**
     * Cachet officiel de l'établissement, si importé depuis les paramètres.
     */
    public static function cachetDataUri(): string
    {
        return static::fichierVersDataUri(config('ecole.cachet'));
    }

    /**
     * Image du drapeau du Bénin (photo fournie par Saint Jean-Baptiste),
     * utilisée à la place du drapeau SVG générique sur cette carte.
     */
    public static function drapeauBeninDataUri(): string
    {
        return static::fichierVersDataUri('images/benin.jpg');
    }

    private static function fichierVersDataUri(?string $cheminRelatif): string
    {
        if (! $cheminRelatif) {
            return '';
        }

        $chemin = public_path($cheminRelatif);

        if (! is_file($chemin)) {
            return '';
        }

        $mime = mime_content_type($chemin) ?: 'image/png';
        $donnees = base64_encode(file_get_contents($chemin));

        return "data:{$mime};base64,{$donnees}";
    }
}
