<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'nom',
        'prenoms',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'classe',
        'telephone',
        'photo',
        'annee_scolaire',
        'statut',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            if (empty($student->matricule)) {
                $student->matricule = static::genererMatricule($student->annee_scolaire);
            }
        });
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
        $chemin = $this->photo ? storage_path('app/public/'.$this->photo) : null;

        if (! $chemin || ! is_file($chemin)) {
            $chemin = public_path('images/photo-placeholder.png');
        }

        $mime = mime_content_type($chemin) ?: 'image/png';
        $donnees = base64_encode(file_get_contents($chemin));

        return "data:{$mime};base64,{$donnees}";
    }

    /**
     * Génère le prochain matricule pour l'année scolaire donnée.
     * Format : 2 derniers chiffres de l'année de début + lettre fixe "T" + numéro séquentiel sur 3 chiffres.
     * Ex: 2026-2027 -> 26T-001, 26T-002, ...
     */
    public static function genererMatricule(string $anneeScolaire): string
    {
        $prefixeAnnee = substr($anneeScolaire, 2, 2);
        $lettre = 'T';
        $prefixe = "{$prefixeAnnee}{$lettre}-";

        $dernier = static::where('matricule', 'like', "{$prefixe}%")
            ->orderByDesc('matricule')
            ->value('matricule');

        $prochainNumero = 1;
        if ($dernier) {
            $numero = (int) substr($dernier, strlen($prefixe));
            $prochainNumero = $numero + 1;
        }

        return $prefixe.str_pad((string) $prochainNumero, 3, '0', STR_PAD_LEFT);
    }
}
