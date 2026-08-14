{{-- Styles partagés recto/verso — dimensions exactes format CR80 (85.6mm x 54mm) --}}
<style>
    :root {
        --color-sky: #5EB3E4;
        --color-sky-deep: #3E93C4;
        --color-green: #4CAF6D;
        --color-green-deep: #379258;
        --color-salmon: #F2A6B0;
        --color-salmon-deep: #E58B97;
        --color-ink: #1A1A1A;
        --color-white: #FFFFFF;
    }

    * { box-sizing: border-box; }

    html, body {
        margin: 0;
        padding: 0;
    }

    body {
        width: 85.6mm;
        height: 54mm;
        font-family: 'Inter', Arial, sans-serif;
        color: var(--color-ink);
        background: var(--color-white);
        overflow: hidden;
    }

    .carte {
        width: 85.6mm;
        height: 54mm;
        position: relative;
        display: flex;
        flex-direction: column;
        background: var(--color-white);
    }

    .nom-ecole {
        font-family: 'Cormorant Garamond', 'Times New Roman', serif;
    }

    .bandeau-haut {
        background: linear-gradient(135deg, var(--color-sky) 0%, var(--color-sky-deep) 100%);
        color: var(--color-white);
        display: flex;
        align-items: center;
        gap: 2mm;
        padding: 1.6mm 3mm;
    }

    .monogramme {
        flex: none;
        width: 7mm;
        height: 7mm;
        border-radius: 50%;
        background: var(--color-white);
        padding: 0.5mm;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .monogramme img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        display: block;
    }

    .identite-ecole {
        line-height: 1.1;
    }

    .identite-ecole .nom-ecole {
        font-size: 3.4mm;
        font-weight: 600;
        color: var(--color-white);
        letter-spacing: 0.2px;
    }

    .identite-ecole .sous-texte {
        font-size: 2mm;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--color-white);
        opacity: 0.9;
        font-weight: 500;
    }

    .bandeau-bas {
        margin-top: auto;
        background: linear-gradient(135deg, var(--color-salmon) 0%, var(--color-salmon-deep) 100%);
        color: var(--color-white);
        padding: 1.2mm 3mm;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2mm;
    }

    .cachet-zone {
        flex: none;
        width: 20mm;
        height: 7mm;
        border: 0.3mm dashed var(--color-white);
        border-radius: 1mm;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6mm;
        text-align: center;
        color: var(--color-white);
        opacity: 0.9;
        line-height: 1.1;
    }

    .slogan-ecole {
        text-align: right;
        line-height: 1.15;
    }

    .slogan-ecole .nom {
        font-size: 2.1mm;
        font-weight: 600;
        color: var(--color-white);
        font-family: 'Cormorant Garamond', serif;
    }

    .slogan-ecole .slogan {
        font-size: 1.9mm;
        font-style: italic;
        color: var(--color-white);
    }
</style>
