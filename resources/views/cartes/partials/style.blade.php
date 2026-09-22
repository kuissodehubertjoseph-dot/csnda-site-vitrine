{{-- Styles partagés recto/verso — dimensions exactes format CR80 (85.6mm x 54mm) --}}
<style>
    :root {
        --color-sky: #2D3F8B;
        --color-sky-deep: #222F68;
        --color-green: #007347;
        --color-green-deep: #00502f;
        --color-salmon: #D32F2F;
        --color-salmon-deep: #B71C1C;
        --color-ink: #1A1A1A;
        --color-white: #FFFFFF;
    }

    * {
        box-sizing: border-box;
    }

    /* Force Chromium à conserver les fonds/opacités translucides tels quels
       lors de l'export PDF (Browsershot) — sans quoi certains navigateurs
       "aplatissent" les couleurs en mode impression. */
    html {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    html,
    body {
        margin: 0;
        padding: 0;
    }

    body {
        width: 85.6mm;
        height: 54mm;
        font-family: 'Inter', Arial, sans-serif;
        color: var(--color-ink);
        background: #FFFFFF;
        overflow: hidden;
    }

    .carte {
        width: 85.6mm;
        height: 54mm;
        position: relative;
        display: flex;
        flex-direction: column;
        background: #FFFFFF;
    }

    /* Recto UCAO uniquement : la carte physique est en couleur au recto,
       mais le verso (cartouche officiel) reste sur papier blanc — voir plus
       bas .ucao-verso-papier qui reste volontairement sans dégradé. */
    .ucao-fond-recto {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;

        padding: 2.2mm 1.5mm 0.6mm;
        background: linear-gradient(180deg, #f6d743 0%, #f6d743 20%, #e5e0de 30%, #e5e0de 46%, #22aced 56%, #22aced 100%);
    }

    /* --- Recto : en-tête (logo, tutelle, drapeau) --- */
    .entete-recto {
        display: flex;
        align-items: flex-start;
        gap: 1.2mm;
        padding: 1.2mm 1.5mm 0.6mm;
    }

    .rond-logo,
    .rond-drapeau {
        flex: none;
        width: 13mm;
        height: 13mm;
        border-radius: 50%;
        overflow: hidden;
        background: var(--color-white);
    }

    .rond-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .tutelle {
        flex: 1;
        min-width: 0;
        text-align: center;
        line-height: 1.25;
    }

    .ligne-tutelle {
        font-size: 1.5mm;
        font-weight: 400;
        color: var(--color-ink);
        letter-spacing: 0;
        white-space: nowrap;
    }

    .ligne-nom-ecole {
        font-size: 2.1mm;
        font-weight: 800;
        color: var(--color-sky);
        margin-top: 1mm;
        letter-spacing: 0;
        white-space: nowrap;
    }

    /* --- Recto : bandeau titre --- */
    .titre-recto {
        background: var(--color-sky);
        color: var(--color-white);
        text-align: center;
        font-size: 2.7mm;
        font-weight: 800;
        letter-spacing: 0.3px;
        padding: 1mm 2mm;
        margin: 0.4mm 0;
    }

    /* Carte Lycée Les Élites : bandeau du titre en vert foncé personnalisé
       au lieu du bleu générique — n'affecte pas les autres cartes. */
    .ecole-lycee-les-elites .titre-recto {
        background: #005D48;
    }

    /* Carte Lycée Les Élites : le texte sous le logo (tutelle + nom de
       l'établissement) est rendu bien lisible — la ligne de tutelle
       ("MINISTERE DES ENSEIGNEMENTS SECONDAIRE...") est longue et passait
       en nowrap hors du cadre (donc en partie invisible) ; on autorise le
       retour à la ligne et on renforce la taille/graisse pour la clarté.
       N'affecte pas les autres cartes. */
    .ecole-lycee-les-elites .ligne-tutelle {
        white-space: normal;
        font-size: 2.1mm;
        font-weight: 700;
        line-height: 1.2;
    }

    /* Carte Lycée Les Élites : la ligne du ministère (2e ligne de tutelle,
       "MINISTERE DES ENSEIGNEMENTS SECONDAIRE...") est distinguée en rouge
       de "REPUBLIQUE DU BENIN" au-dessus. N'affecte pas les autres cartes. */
    .ecole-lycee-les-elites .ligne-tutelle:nth-of-type(2) {
        color: #C1121F;
    }

    .ecole-lycee-les-elites .ligne-nom-ecole {
        font-size: 3.8mm;
        color: #005D48;
    }

    /* Carte Lycée Les Élites : "LYCEE" et "LES ELITES" (nom_ligne1 et
       nom_ligne2) affichés sur une seule ligne au lieu de deux. */
    .ecole-lycee-les-elites .ligne-nom-ecole-1 {
        display: inline;
        margin-top: 0;
    }

    .ecole-lycee-les-elites .ligne-nom-ecole-1::after {
        content: ' ';
    }

    .ecole-lycee-les-elites .ligne-nom-ecole-2 {
        display: inline;
        margin-top: 0;
    }

    /* Carte Saint Jean-Baptiste : le bandeau "CARTE D'IDENTITÉ SCOLAIRE"
       garde son fond vert (bandeau), mais reprend la taille du nom de
       l'établissement — voir .ligne-nom-ecole-2 plus bas pour l'échange
       réciproque de taille. N'affecte pas la carte CSS. */
    .ecole-jean-baptiste .titre-recto {
        background: linear-gradient(180deg, var(--color-green) 0%, var(--color-green-deep) 100%);
        color: var(--color-white);
        font-size: 1.9mm;
        letter-spacing: 0;
        padding: 0.6mm 2mm;
        margin: 0.2mm 0;
        line-height: 1.2;
        white-space: normal;
    }

    /* Carte Saint Jean-Baptiste : "ARCHIDIOCESE DE COTONOU" (nom_ligne1) en
       rouge, sans toucher à la deuxième ligne ni à la carte CSS. */
    .ecole-jean-baptiste .ligne-nom-ecole-1 {
        color: var(--ucao-rouge);
    }

    /* Carte Saint Jean-Baptiste : logo et drapeau échangés de place
       (drapeau à gauche, logo à droite) — n'affecte pas la carte CSS. */
    .ecole-jean-baptiste .entete-recto .rond-drapeau {
        order: 1;
        width: 11mm;
        height: 9mm;
        border-radius: 0;
        margin-top: 3mm;
        margin-right: 0;
        text-align: center;
    }

    .ecole-jean-baptiste .entete-recto .tutelle {
        order: 2;
        line-height: 1;
        flex: 1;
        text-align: center;
        margin-top: 1mm;

    }

    .ecole-jean-baptiste .ligne-nom-ecole {
        margin-top: 0.1mm;
        text-align: center;
    }

    .ecole-jean-baptiste .ligne-nom-ecole-1 {
        margin-top: 1mm;
        text-align: center;
        height: -2mm;



    }

    .ecole-jean-baptiste .entete-recto .rond-logo {
        order: 3;
        width: 11mm;
        height: 11mm;
        margin-top: 1.5mm;
        margin-left: -2mm;
        text-align: right;
        border-radius: 0;
        overflow: visible;
    }

    /* Carte Saint Jean-Baptiste : signature + libellé "Le (La) Titulaire"
       déplacés sous la photo de l'élève (voir recto-contenu.blade.php), avec
       la photo remontée pour laisser la place en dessous. La colonne photo
       est bornée en hauteur et rognée (overflow hidden) pour que la
       signature ne déborde jamais sur le pied de carte "ANNEE SCOLAIRE". */
    .ecole-jean-baptiste .corps-recto {
        overflow: hidden;
    }

    .ecole-jean-baptiste .zone-photo {
        overflow: hidden;
    }

    .ecole-jean-baptiste .zone-photo .photo-cadre {
        flex-shrink: 0;
        margin-top: 0.8mm;
    }

    .ecole-jean-baptiste .zone-photo .signature-eleve-img {
        flex-shrink: 0;
        max-width: 18mm;
        max-height: 7mm;
        margin-top: 0.6mm;
        margin-bottom: 0;
    }

    .ecole-jean-baptiste .zone-photo .titulaire {
        flex-shrink: 0;
        font-size: 1.2mm;
        margin-top: 0;
    }

    .ecole-jean-baptiste .pied-recto {
        color: var(--ucao-rouge);
        padding: 0.4mm 2mm;
    }

    .ecole-jean-baptiste .ligne-tutelle {
        font-size: 2mm;
        font-weight: 700;
    }

    /* Nom complet de l'établissement ("COLLEGE CATHOLIQUE SAINT
       JEAN-BAPTISTE") trop long pour tenir sur une seule ligne dans
       l'espace disponible entre le drapeau et le logo : on autorise le
       retour à la ligne (au lieu du nowrap hérité de .ligne-nom-ecole) pour
       qu'il reste entièrement lisible plutôt que d'être tronqué. */
    .ecole-jean-baptiste .ligne-nom-ecole-2 {
        display: block;
        font-size: 2.75mm;
        letter-spacing: 0.3px;
        line-height: 1.2;
        white-space: normal;
        margin-top: 0.6mm;
    }





    /* Carte Saint Jean-Baptiste : logo en filigrane flottant en arrière-plan
       du recto, avec un effet de flou "glace" (le contenu reste net devant,
       porté par des panneaux translucides à effet givré). */
    .ecole-jean-baptiste .carte-recto {
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
        width: 85.6mm;
        height: 54mm;
        padding: 0.5mm;
    }

    .ecole-jean-baptiste .carte-recto::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: var(--jb-logo-fond);
        background-repeat: no-repeat;
        background-position: -30mm center;
        background-size: 115%;
        opacity: 0.5;
        /* Le flou est appliqué directement à l'image (filter), pas via
           backdrop-filter : Chromium en mode "impression PDF" (utilisé par
           Browsershot pour générer les cartes) ignore backdrop-filter, ce qui
           faisait disparaître l'effet givré uniquement dans le PDF généré,
           pas dans l'aperçu navigateur. filter fonctionne dans les deux cas.
           Blur renforcé + léger éclaircissement pour un vrai effet "verre
           dépoli" (glacé) au lieu d'un simple flou léger. */
        filter: blur(3.5px) brightness(1.1) saturate(0.9);
        z-index: 0;
    }

    .ecole-jean-baptiste .carte-recto>* {
        position: relative;
        z-index: 1;
    }

    .ecole-jean-baptiste .entete-recto,
    .ecole-jean-baptiste .corps-recto,
    .ecole-jean-baptiste .pied-recto {
        background: rgba(255, 255, 255, 0.35);
        border-radius: 0;
    }

    .ecole-jean-baptiste .rond-drapeau img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Carte Lycée Les Élites : logo en filigrane flottant en arrière-plan
       du recto, avec le même effet de flou "glace" que la carte Saint
       Jean-Baptiste (contenu net devant, panneaux translucides givrés).
       N'affecte pas les autres cartes. */
    .ecole-lycee-les-elites .carte-recto {
        position: relative;
        overflow: hidden;
        box-sizing: border-box;
        width: 85.6mm;
        height: 54mm;
        padding: 0.5mm;
    }

    .ecole-lycee-les-elites .carte-recto::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: var(--lle-logo-fond);
        background-repeat: no-repeat;
        background-position: center center;
        background-size: 115%;
        opacity: 0.5;
        filter: blur(10px) brightness(1.1) saturate(0.9);
        z-index: 0;
    }

    .ecole-lycee-les-elites .carte-recto>* {
        position: relative;
        z-index: 1;
    }

    .ecole-lycee-les-elites .entete-recto,
    .ecole-lycee-les-elites .corps-recto,
    .ecole-lycee-les-elites .pied-recto {
        background: rgba(255, 255, 255, 0.35);
        border-radius: 0;
    }

    /* --- Recto : corps (champs + photo) --- */
    .corps-recto {
        flex: 1;
        display: flex;
        align-items: stretch;
        gap: 1mm;
        padding: 1mm 2mm;
        min-height: 0;
    }

    .champs-recto {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.9mm;
    }

    .champ {
        display: flex;
        align-items: flex-start;
        gap: 1.5mm;
        font-size: 2.2mm;
        line-height: 1.25;
    }

    .etiquette-champ {
        font-weight: 700;
        color: var(--color-ink);
        flex: 0 0 15mm;
        white-space: nowrap;
        padding-top: 0.1mm;
    }

    .valeur-champ {
        font-weight: 800;
        color: #000000;
        min-width: 0;
        overflow-wrap: break-word;
    }

    .zone-titulaire {
        flex: none;
        width: 14mm;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        padding-bottom: 0.5mm;
    }

    .signature-eleve-img {
        max-width: 13mm;
        max-height: 25mm;
        object-fit: contain;
        margin-bottom: 0.5mm;
    }

    .zone-photo {
        flex: none;
        width: 19mm;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }

    .photo-cadre {
        width: 18mm;
        height: 22mm;
        margin-top: 2mm;
        border: 0.25mm solid #999999;
        border-radius: 0.6mm;
        overflow: hidden;
        background: #f2f2f2;
    }

    .photo-cadre img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .titulaire {
        font-size: 1.45mm;
        font-weight: 700;
        color: var(--color-ink);
        text-align: center;
        white-space: nowrap;
    }

    /* --- Recto : pied de carte --- */
    .pied-recto {
        border-top: 0.25mm solid var(--color-ink);
        text-align: center;
        font-size: 2.2mm;
        font-weight: 600;
        color: var(--color-ink);
        padding: 0.8mm 2mm;
        margin-top: auto;
        text-align: center;
    }

    /* --- Verso : cadre de certification --- */
    .cadre-verso {
        flex: 1;
        margin: 2mm;
        border: 0.3mm solid var(--color-ink);
        border-radius: 2mm;
        padding: 1.8mm 2.8mm;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1.3mm;
    }

    .verso-republique {
        font-size: 2.3mm;
        font-weight: 800;
        letter-spacing: 0.4px;
        color: var(--color-ink);
        text-align: center;
    }

    /* Carte Lycée Les Élites : "LYCEE LES ELITES" remplace la mention
       République sur le verso, en très gras, en vert et plus grand pour
       bien ressortir ; le petit trait décoratif sous le texte est retiré.
       N'affecte pas les autres cartes. */
    .ecole-lycee-les-elites .verso-republique {
        font-weight: 900;
        font-size: 6mm;
        color: #005D48;
    }

    .ecole-lycee-les-elites .verso-trait {
        display: none;
    }

    .verso-trait {
        width: 9mm;
        height: 0.35mm;
        background: var(--color-ink);
        margin-top: -0.3mm;
        text-align: center;
    }

    .verso-texte {
        font-size: 1.9mm;
        line-height: 1.45;
        font-weight: 700;
        color: var(--color-ink);
        text-align: center;
    }

    .verso-adresse {
        font-size: 1.95mm;
        line-height: 1.5;
        font-weight: 800;
        color: #000000;
        justify-content: center;
    }

    .verso-cachet {
        flex: 1;
        min-height: 4mm;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        transform: translateX(-5px);
        gap: 3mm;
        padding-right: 3mm;
    }

    .verso-signature-img {
        max-width: 22mm;
        max-height: 9mm;
        object-fi  t: contain;
        width: 35mm;
    }

    .verso-cachet-img {
        max-width: 16mm;
        max-height: 16mm;
        object-fit: contain;
        text-align: center;
    }

    .verso-directeur {
        font-size: 1.95mm;
        font-weight: 800;
        text-decoration: underline;
        color: var(--color-ink);
        margin-top: auto;
        padding-bottom: 0.5mm;
        align-self: flex-end;
        text-align: center;


    }

    /* ==================== Gabarit UCAO (recto/verso) ====================
       Reproduction fidèle de la carte fournie par l'établissement : fond en
       dégradé continu bleu → beige → rose (pas de blocs diagonaux), nom de
       l'école en haut, tutelle (UCAO) en bas. Classes préfixées "ucao-",
       utilisées uniquement par cartes.partials.recto-contenu / verso-contenu
       quand config('ecole.slug') vaut 'ucao' ou 'egei' — n'affecte aucune
       autre carte. */
    :root {
        --ucao-bleu: #22aced;
        --ucao-bleu-texte: #1d4e89;
        --ucao-rouge: #d32f2f;
        --ucao-rose: #f6d743;
        --ucao-beige: #e5e0de;
    }

    .ucao-entete {
        flex: none;
        display: flex;
        align-items: flex-start;
        gap: 1.3mm;
        padding: 1.3mm 3.5mm 0;
    }

    .ucao-logo {
        flex: none;
        width: 9mm;
        height: 9mm;
        border-radius: 50%;
        overflow: hidden;
        background: var(--color-white);
        border: 0.25mm solid var(--color-white);
        margin-top: 0.2mm;
    }

    .ucao-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .ucao-identite {
        flex: 1;
        min-width: 0;
        line-height: 1.15;
    }

    .ucao-nom-etablissement {
        font-size: 2mm;
        font-weight: 800;
        letter-spacing: 0.1px;
        color: var(--ucao-bleu-texte);
        text-align: left;
        white-space: nowrap;
    }

    .ucao-nom-etablissement-centre {
        text-align: center;
    }

    /* Largeur ajustée au texte (pas à toute la carte) : le trait ne dépasse
       pas la ligne la plus longue ("ECOLE SUPERIEURE DE MANAGEMENT"), donc
       il commence au "O" et finit au "E" du texte, pas au bord de la carte. */
    .ucao-nom-bloc {
        width: fit-content;
    }

    .ucao-nom-trait {
        width: 7mm;
        height: 0.35mm;
        background: var(--ucao-bleu-texte);
        margin: 0.4mm auto 0;
    }

    .ucao-titre-ligne {
        margin-top: 0.5mm;
        text-align: center;
    }

    .ucao-titre {
        font-size: 2.3mm;
        font-weight: 900;
        letter-spacing: 0.3px;
        color: var(--ucao-rouge);
    }

    .ucao-annee {
        margin-top: 0.1mm;
        font-size: 1.6mm;
        font-weight: 700;
        color: var(--color-ink);
    }

    .ucao-corps {
        flex: 1;
        display: flex;
        align-items: flex-start;
        gap: 1.5mm;
        padding: 1.6mm 3.5mm 0;
        min-height: 0;
    }

    .ucao-champs {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1.6mm;
    }

    /* 7 lignes au lieu de 6 (élèves de Licence 1, avec la date d'expiration
       en plus) : resserre l'espacement pour que la dernière ligne ne
       chevauche pas le trait séparateur du pied de page. */
    .ucao-champs--compact {
        gap: 1mm;
    }

    .ucao-champ {
        display: flex;
        align-items: flex-start;
        gap: 1.3mm;
        font-size: 2.4mm;
        line-height: 1.25;
    }

    .ucao-etiquette {
        font-weight: 700;
        color: #000000;
        flex: 0 0 20mm;
        white-space: nowrap;
    }

    .ucao-valeur {
        font-weight: 800;
        color: #000000;
        min-width: 0;
        overflow-wrap: break-word;
    }

    /* Doit rester déclaré après .ucao-etiquette / .ucao-valeur ci-dessus
       pour l'emporter sur leur color:#000000 (même spécificité, l'ordre
       dans la feuille de style tranche). */
    .ucao-etiquette-expire,
    .ucao-valeur-expire {
        color: var(--ucao-rouge);
    }

    .ucao-zone-photo {
        flex: none;
        width: 19mm;
        display: flex;
        justify-content: center;
        padding-top: 0.2mm;
    }

    .ucao-photo-cadre {
        width: 16mm;
        height: 18mm;
        border-radius: 0.6mm;
        overflow: hidden;
        background: #f2f2f2;
    }

    .ucao-photo-cadre img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .ucao-signature-zone {
        flex: none;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.3mm;
        padding: 0.8mm 3.5mm 0;
        margin-left: auto;
        margin-bottom: 1.5mm;
    }

    .ucao-signature-libelle {
        font-size: 1.5mm;
        font-weight: 700;
        color: var(--color-ink);
        white-space: nowrap;
    }

    .ucao-signature-img {
        max-width: 16mm;
        max-height: 13mm;
        object-fit: contain;
    }

    .ucao-signature-nom {
        font-size: 1.45mm;
        font-weight: 700;
        color: var(--color-ink);
        white-space: nowrap;
    }

    .ucao-pied {
        flex: none;
        margin-top: auto;
        border-top: 0.2mm solid rgba(0, 0, 0, 0.35);
        text-align: center;
        padding: 1.4mm 3.5mm 1mm;
    }

    .ucao-pied-ligne1 {
        font-size: 1.9mm;
        font-weight: 800;
        letter-spacing: 0.2px;
        color: var(--ucao-bleu-texte);
    }

    .ucao-pied-ligne2 {
        font-size: 1.6mm;
        font-weight: 700;
        letter-spacing: 0.2px;
        color: var(--ucao-rouge);
        margin-top: 0.2mm;
    }

    /* --- Verso UCAO : cartouche officiel sur papier blanc, texte centré --- */
    .ucao-verso-papier {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        text-align: center;
        gap: 0.6mm;
        padding: 3mm 4mm 3mm;
    }

    .ucao-verso-entete {
        font-size: 2.1mm;
        font-weight: 800;
        letter-spacing: 0.3px;
        color: #000000;
    }

    .ucao-verso-coordonnees {
        margin-top: 2.4mm;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1mm;
        font-size: 2.8mm;
        line-height: 1.2;
        font-weight: 700;
        color: #000000;
    }

    /* ==================== Verso Saint Jean-Baptiste ====================
       Reproduction du cartouche officiel (photo de la carte physique) :
       titre bleu en deux lignes, texte de certification, "Le Directeur" +
       signature/cachet superposés à droite, coordonnées en rouge en pied. */
    .ecole-jean-baptiste .carte-verso {
        background: #008751;
    }

    .jb-verso-papier {
        flex: 1;
        overflow: hidden;
        margin: 0;
        border: none;
        border-radius: 0;
        display: flex;
        flex-direction: column;
        padding: 2mm 3mm 1mm;
        background: #ffffff;
        color: #1a3faa;
    }

    .jb-verso-titre {
        font-size: 6mm;
        font-weight: 800;
        line-height: 1.15;
        white-space: nowrap;
        color: #1a3faa;
    }

    .jb-verso-titre-1 {
        font-size: 6mm;
        white-space: nowrap;
    }

    .jb-verso-texte {
        margin-top: 1.6mm;
        font-size: 2.25mm;
        font-weight: 600;
        line-height: 1.1;
        text-align: center;
        color: #1a3faa;
    }

    .jb-verso-texte-ligne1 {
        display: block;
        font-size: 2.55mm;
        white-space: nowrap;
    }

    .jb-verso-texte-suite {
        display: block;
        margin-top: 0.4mm;
        line-height: 1.4;
    }

    .jb-verso-signature-zone {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.4mm;
    }

    .jb-verso-le-directeur {
        font-size: 1.9mm;
        font-style: italic;
        font-weight: 700;
        color: #1a3faa;
        margin-right: 3mm;
    }

    .jb-verso-cachet {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
        min-height: 8mm;
    }

    .jb-verso-cachet-img {
        position: relative;
        max-width: 14mm;
        max-height: 14mm;
        object-fit: contain;
        margin-right: 11mm;
    }

    .jb-verso-signature-img {
        position: absolute;
        right: 1mm;
        max-width: 18mm;
        max-height: 9mm;
        object-fit: contain;
    }

    .jb-verso-directeur {
        font-size: 1.95mm;
        font-weight: 800;
        color: #1a3faa;
        margin-right: 5mm;
        margin-top: -1.2mm;
    }

    .jb-verso-pied {
        margin-top: 1mm;
        width: 100%;
        font-size: 1.85mm;
        font-weight: 700;
        line-height: 1.3;
        text-align: center;
        white-space: normal;
        overflow-wrap: break-word;
        color: var(--ucao-rouge);
    }
</style>