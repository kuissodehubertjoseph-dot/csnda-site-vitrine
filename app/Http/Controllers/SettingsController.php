<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Page de dépôt de la signature du directeur et du cachet de l'établissement,
     * imprimés sur le verso de la carte scolaire.
     */
    public function edit(): View
    {
        Gate::authorize('gerer-parametres-carte');

        return view('parametres.edit', [
            'signatureUrl' => $this->urlSiExiste(config('ecole.signature')),
            'cachetUrl' => $this->urlSiExiste(config('ecole.cachet')),
        ]);
    }

    public function updateSignature(Request $request): RedirectResponse
    {
        Gate::authorize('gerer-parametres-carte');

        $request->validate([
            'signature' => ['required', 'image', 'max:4096'],
        ]);

        $this->enregistrerImage($request->file('signature'), config('ecole.signature'));

        return back()->with('succes', 'Signature du directeur mise à jour.');
    }

    public function updateCachet(Request $request): RedirectResponse
    {
        Gate::authorize('gerer-parametres-carte');

        $request->validate([
            'cachet' => ['required', 'image', 'max:4096'],
        ]);

        $this->enregistrerImage($request->file('cachet'), config('ecole.cachet'));

        return back()->with('succes', 'Cachet de l\'établissement mis à jour.');
    }

    /**
     * Convertit l'image déposée en PNG et l'enregistre au chemin fixe attendu
     * par App\Support\Ecole, quel que soit le format d'origine du fichier.
     */
    private function enregistrerImage(UploadedFile $fichier, string $cheminRelatif): void
    {
        $source = match ($fichier->getMimeType()) {
            'image/png' => imagecreatefrompng($fichier->getRealPath()),
            'image/webp' => imagecreatefromwebp($fichier->getRealPath()),
            default => imagecreatefromstring(file_get_contents($fichier->getRealPath())),
        };

        imagesavealpha($source, true);

        $destination = public_path($cheminRelatif);

        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        imagepng($source, $destination);
        imagedestroy($source);
    }

    private function urlSiExiste(string $cheminRelatif): ?string
    {
        return is_file(public_path($cheminRelatif))
            ? asset($cheminRelatif).'?v='.filemtime(public_path($cheminRelatif))
            : null;
    }
}
