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
        $chemin = public_path(config('ecole.logo'));

        if (! is_file($chemin)) {
            return '';
        }

        $mime = mime_content_type($chemin) ?: 'image/jpeg';
        $donnees = base64_encode(file_get_contents($chemin));

        return "data:{$mime};base64,{$donnees}";
    }
}
