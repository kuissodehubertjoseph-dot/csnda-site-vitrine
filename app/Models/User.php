<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Développeur : accès technique complet. Peut tout ce que fait le DG, et
     * lui seul peut créer, modifier ou supprimer un autre compte développeur.
     */
    public const ROLE_DEVELOPPEUR = 'developpeur';

    /**
     * Directeur général : accès complet à la gestion de l'établissement,
     * y compris la gestion des accès et les suppressions d'élèves.
     */
    public const ROLE_DG = 'dg';

    /**
     * Secrétariat : gestion quotidienne des élèves (ajout, modification,
     * déplacement de classe, import PDF, impression par lot, signature et
     * cachet) sans droit de suppression ni gestion des accès.
     */
    public const ROLE_SECRETAIRE = 'secretaire';

    /**
     * Libellés affichés dans l'interface, du plus privilégié au moins privilégié.
     *
     * @var array<string, string>
     */
    public const ROLES = [
        self::ROLE_DEVELOPPEUR => 'Développeur',
        self::ROLE_DG => 'Directeur général',
        self::ROLE_SECRETAIRE => 'Secrétaire',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'etablissement_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function estDeveloppeur(): bool
    {
        return $this->role === self::ROLE_DEVELOPPEUR;
    }

    public function estDg(): bool
    {
        return $this->role === self::ROLE_DG;
    }

    /**
     * Direction au sens large : DG et développeur partagent les mêmes droits
     * sur la gestion de l'établissement.
     */
    public function estDirection(): bool
    {
        return $this->estDg() || $this->estDeveloppeur();
    }

    /**
     * Libellé du rôle. Le rôle peut être absent de l'instance juste après une
     * création (valeur posée par défaut en base) : on retombe alors sur le
     * rôle le moins privilégié plutôt que de renvoyer une valeur vide.
     */
    public function libelleRole(): string
    {
        $role = $this->role ?: self::ROLE_SECRETAIRE;

        return self::ROLES[$role] ?? $role;
    }

    public function etablissement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    /**
     * Un utilisateur a-t-il le droit de se connecter à cet établissement ?
     * Un développeur peut se connecter à n'importe lequel ; les autres rôles
     * sont rattachés à un établissement précis.
     */
    public function peutAccederA(Etablissement $etablissement): bool
    {
        return $this->estDeveloppeur() || $this->etablissement_id === $etablissement->id;
    }
}
