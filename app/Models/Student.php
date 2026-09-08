<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'matricule',
        'nom',
        'prenoms',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'classe',
        'telephone',
        'photo',
        'signature',
        'annee_scolaire',
        'statut',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /**
     * Isolation entre établissements : toute lecture est automatiquement
     * limitée à l'établissement actif en session, et tout élève créé lui est
     * automatiquement rattaché — sans qu'aucun contrôleur n'ait à s'en
     * préoccuper explicitement.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('etablissement', function (Builder $query) {
            if ($id = static::etablissementActifId()) {
                $query->where('etablissement_id', $id);
            }
        });

        static::creating(function (Student $eleve) {
            if (empty($eleve->etablissement_id)) {
                $eleve->etablissement_id = static::etablissementActifId();
            }
        });
    }

    /**
     * Id de l'établissement actif pour la requête en cours, ou null hors
     * contexte web (artisan, tests sans session démarrée) : dans ce cas la
     * portée n'est pas appliquée plutôt que de risquer une erreur.
     */
    public static function etablissementActifId(): ?int
    {
        if (! app()->bound('session') || ! app('session')->isStarted()) {
            return null;
        }

        return session('etablissement_id');
    }

    public function etablissement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    /**
     * Age calculé à la volée à partir de la date de naissance (jamais stocké en base).
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance?->age;
    }

    public function getNomCompletAttribute(): string
    {
        return trim("{$this->nom} {$this->prenoms}");
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : asset('images/photo-placeholder.png');
    }

    /**
     * Photo encodée en data URI (base64), utilisée dans les templates de carte
     * générés par Browsershot : évite tout appel HTTP vers le serveur pendant
     * le rendu (qui bloquerait si le serveur de dev est mono-thread).
     */
    public function getPhotoDataUriAttribute(): string
    {
        $chemin = $this->photo ? Storage::disk('public')->path($this->photo) : null;

        if (! $chemin || ! is_file($chemin)) {
            $chemin = public_path('images/photo-placeholder.png');
        }

        $mime = mime_content_type($chemin) ?: 'image/png';
        $donnees = base64_encode(file_get_contents($chemin));

        return "data:{$mime};base64,{$donnees}";
    }

    /**
     * Signature manuscrite de l'élève encodée en data URI, utilisée sur le recto
     * de la carte. Chaîne vide si aucune signature n'a été importée : le gabarit
     * laisse alors l'espace vide plutôt que d'afficher un espace réservé.
     */
    public function getSignatureDataUriAttribute(): string
    {
        $chemin = $this->signature ? Storage::disk('public')->path($this->signature) : null;

        if (! $chemin || ! is_file($chemin)) {
            return '';
        }

        $mime = mime_content_type($chemin) ?: 'image/png';
        $donnees = base64_encode(file_get_contents($chemin));

        return "data:{$mime};base64,{$donnees}";
    }

}
