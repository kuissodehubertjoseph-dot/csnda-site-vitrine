{{-- Contenu du recto pour un élève donné. Attend $eleve et, en boucle, $logoDataUri. --}}
@php($logo = $logoDataUri ?? \App\Support\Ecole::logoDataUri())
@if (in_array(config('ecole.slug'), ['ucao', 'egei'], true))
    <div class="ucao-fond-recto">
    <div class="ucao-entete">
        <div class="ucao-logo">
            <img src="{{ $logo }}" alt="Logo {{ config('ecole.sigle') }}">
        </div>
        <div class="ucao-identite">
            <div class="ucao-nom-bloc">
                <div class="ucao-nom-etablissement">{{ config('ecole.tutelle_ligne1') }}</div>
                <div class="ucao-nom-etablissement ucao-nom-etablissement-centre">{{ config('ecole.tutelle_ligne2') }}</div>
                <div class="ucao-nom-trait"></div>
                <div class="ucao-titre-ligne">
                    <div class="ucao-titre">CARTE D'APPRENANT</div>
                    <div class="ucao-annee">{{ $eleve->annee_scolaire }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="ucao-corps">
        @php($neLeAffiche = ($eleve->date_naissance?->format('d/m/Y') ?? '—').' à '.$eleve->lieu_naissance)
        @php($dateExpiration = match (true) {
            str_starts_with($eleve->classe, 'Licence 1') => '31/07/2029',
            str_starts_with($eleve->classe, 'Licence 2') => '31/07/2028',
            str_starts_with($eleve->classe, 'Licence 3') => '31/07/2027',
            default => null,
        })
        <div class="ucao-champs @if ($dateExpiration) ucao-champs--compact @endif">
            <div class="ucao-champ"><span class="ucao-etiquette">Nom :</span><span class="ucao-valeur">{{ mb_strtoupper($eleve->nom) }}</span></div>
            <div class="ucao-champ"><span class="ucao-etiquette">Prénoms :</span><span class="ucao-valeur">{{ $eleve->prenoms }}</span></div>
            <div class="ucao-champ"><span class="ucao-etiquette">Né(e) le :</span><span class="ucao-valeur">{{ $neLeAffiche }}</span></div>
            <div class="ucao-champ"><span class="ucao-etiquette">Cycle-Filière :</span><span class="ucao-valeur">{{ $eleve->classe }}</span></div>
            <div class="ucao-champ"><span class="ucao-etiquette">Matricule :</span><span class="ucao-valeur">{{ $eleve->matricule }}</span></div>
            <div class="ucao-champ"><span class="ucao-etiquette">Contact :</span><span class="ucao-valeur">{{ $eleve->telephone ?: '—' }}</span></div>
            @if ($dateExpiration)
                <div class="ucao-champ"><span class="ucao-etiquette ucao-etiquette-expire">Expire :</span><span class="ucao-valeur ucao-valeur-expire">{{ $dateExpiration }}</span></div>
            @endif
        </div>

        <div class="ucao-zone-photo">
            <div class="ucao-photo-cadre">
                <img src="{{ $eleve->photo_data_uri }}" alt="Photo de {{ $eleve->nom_complet }}">
            </div>
        </div>
    </div>

    <div class="ucao-signature-zone">
        <div class="ucao-signature-libelle">Directeur/Academique</div>
        @if ($signatureDirecteur = \App\Support\Ecole::signatureDataUri())
            <img src="{{ $signatureDirecteur }}" alt="Signature" class="ucao-signature-img">
        @endif
        <div class="ucao-signature-nom">{{ config('ecole.directeur') }}</div>
    </div>

    <div class="ucao-pied">
        <div class="ucao-pied-ligne1">{{ config('ecole.nom_ligne1') }}</div>
        <div class="ucao-pied-ligne2">{{ config('ecole.nom_ligne2') }}</div>
    </div>
    </div>
@else
<div class="entete-recto">
    <div class="rond-logo">
        <img src="{{ $logo }}" alt="Logo {{ config('ecole.sigle') }}">
    </div>
    <div class="tutelle">
        <div class="ligne-tutelle">{{ config('ecole.tutelle_ligne1') }}</div>
        @unless (config('ecole.slug') === 'jean-baptiste')
            <div class="ligne-tutelle">{{ config('ecole.tutelle_ligne2') }}</div>
        @endunless
        <div class="ligne-nom-ecole ligne-nom-ecole-1">{{ config('ecole.nom_ligne1') }}</div>
        <div class="ligne-nom-ecole ligne-nom-ecole-2">{{ config('ecole.nom_ligne2') }}</div>
    </div>
    <div class="rond-drapeau">
        @if (config('ecole.slug') === 'jean-baptiste')
            <img src="{{ \App\Support\Ecole::drapeauBeninDataUri() }}" alt="Drapeau du Bénin">
        @else
            @include('cartes.partials.drapeau-benin')
        @endif
    </div>
</div>

<div class="titre-recto">CARTE D'IDENTITÉ SCOLAIRE</div>

<div class="corps-recto">
    @php($nomAffiche = mb_strtoupper($eleve->nom))
    @php($neLeAffiche = ($eleve->date_naissance?->format('d/m/Y') ?? '—').' à '.$eleve->lieu_naissance)
    <div class="champs-recto">
        <div class="champ"><span class="etiquette-champ">Nom :</span><span class="valeur-champ">{{ $nomAffiche }}</span></div>
        <div class="champ"><span class="etiquette-champ">Prénoms :</span><span class="valeur-champ">{{ $eleve->prenoms }}</span></div>
        <div class="champ"><span class="etiquette-champ">Né (e) le :</span><span class="valeur-champ">{{ $neLeAffiche }}</span></div>
        <div class="champ"><span class="etiquette-champ">Sexe :</span><span class="valeur-champ">{{ $eleve->sexe }}</span></div>
        <div class="champ"><span class="etiquette-champ">Classe :</span><span class="valeur-champ">{{ $eleve->classe }}</span></div>
        <div class="champ"><span class="etiquette-champ">N° Matricule :</span><span class="valeur-champ">{{ $eleve->matricule }}</span></div>
        @if (config('ecole.slug') === 'jean-baptiste')
            <div class="champ"><span class="etiquette-champ">Adr/Tél.Parent :</span><span class="valeur-champ">{{ $eleve->telephone ?: '—' }}</span></div>
        @endif
    </div>

    @php($signatureEleve = $eleve->signature_data_uri)
    @unless (config('ecole.slug') === 'jean-baptiste')
        <div class="zone-titulaire">
            @if ($signatureEleve)
                <img src="{{ $signatureEleve }}" alt="Signature de {{ $eleve->nom_complet }}" class="signature-eleve-img">
            @endif
            <div class="titulaire">Le (La) Titulaire</div>
        </div>
    @endunless

    <div class="zone-photo">
        <div class="photo-cadre">
            <img src="{{ $eleve->photo_data_uri }}" alt="Photo de {{ $eleve->nom_complet }}">
        </div>
        @if (config('ecole.slug') === 'jean-baptiste')
            @if ($signatureEleve)
                <img src="{{ $signatureEleve }}" alt="Signature de {{ $eleve->nom_complet }}" class="signature-eleve-img">
            @endif
        @endif
    </div>
</div>

<div class="pied-recto">ANNEE SCOLAIRE {{ $eleve->annee_scolaire }}</div>
@endif
