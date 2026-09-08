<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
    use HasFactory;

    public const STATUT_ACTIF = 'actif';

    public const STATUT_BIENTOT = 'bientot';

    protected $fillable = [
        'slug',
        'statut',
        'nom',
        'sigle',
        'ville',
        'slogan',
        'telephone_secretariat',
        'logo',
        'tutelle_ligne1',
        'tutelle_ligne2',
        'nom_ligne1',
        'nom_ligne2',
        'adresse',
        'siege_social',
        'telephone_mobile',
        'email',
        'site_web',
        'directeur',
        'signature',
        'cachet',
        'classes',
        'annee_scolaire_courante',
    ];

    protected $casts = [
        'classes' => 'array',
    ];

    public function estActif(): bool
    {
        return $this->statut === self::STATUT_ACTIF;
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function utilisateurs(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Représentation attendue par tout le code existant qui lit
     * config('ecole.*') : le middleware ChargerEtablissementActif recharge ce
     * tableau dans la config à chaque requête, selon l'établissement actif en
     * session — aucun autre fichier n'a besoin de connaître ce modèle.
     *
     * @return array<string, mixed>
     */
    public function versConfig(): array
    {
        return [
            'slug' => $this->slug,
            'nom' => $this->nom,
            'sigle' => $this->sigle,
            'ville' => $this->ville,
            'slogan' => $this->slogan,
            'telephone_secretariat' => $this->telephone_secretariat,
            'logo' => $this->logo,
            'tutelle_ligne1' => $this->tutelle_ligne1,
            'tutelle_ligne2' => $this->tutelle_ligne2,
            'nom_ligne1' => $this->nom_ligne1,
            'nom_ligne2' => $this->nom_ligne2,
            'adresse' => $this->adresse,
            'siege_social' => $this->siege_social,
            'telephone_mobile' => $this->telephone_mobile,
            'email' => $this->email,
            'site_web' => $this->site_web,
            'directeur' => $this->directeur,
            'signature' => $this->signature,
            'cachet' => $this->cachet,
            'classes' => $this->classes ?? [],
            'annee_scolaire_courante' => $this->annee_scolaire_courante,
        ];
    }
}
