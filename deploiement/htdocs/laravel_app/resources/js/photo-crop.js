/**
 * Prévisualisation + recadrage simple (pan/zoom) d'une photo d'élève avant envoi.
 * Rendu final carré (format standard pour une photo d'identité sur carte scolaire),
 * injecté dans l'input file du formulaire via DataTransfer avant soumission.
 */
export function initPhotoCrop(root) {
    const input = root.querySelector('[data-photo-input]');
    const canvas = root.querySelector('[data-photo-canvas]');
    const placeholder = root.querySelector('[data-photo-placeholder]');
    const zoomRange = root.querySelector('[data-photo-zoom]');
    const form = root.closest('form');

    if (!input || !canvas || !form) return;

    const ctx = canvas.getContext('2d');
    const SIZE = 480;
    canvas.width = SIZE;
    canvas.height = SIZE;

    let image = null;
    let scale = 1;
    let minScale = 1;
    let offsetX = 0;
    let offsetY = 0;
    let dragging = false;
    let lastX = 0;
    let lastY = 0;

    function draw() {
        if (!image) return;
        ctx.clearRect(0, 0, SIZE, SIZE);
        const w = image.width * scale;
        const h = image.height * scale;
        ctx.drawImage(image, offsetX, offsetY, w, h);
    }

    function clampOffsets() {
        const w = image.width * scale;
        const h = image.height * scale;
        offsetX = Math.min(0, Math.max(SIZE - w, offsetX));
        offsetY = Math.min(0, Math.max(SIZE - h, offsetY));
    }

    function loadImage(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                image = img;
                minScale = Math.max(SIZE / img.width, SIZE / img.height);
                scale = minScale;
                offsetX = (SIZE - img.width * scale) / 2;
                offsetY = (SIZE - img.height * scale) / 2;
                canvas.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
                if (zoomRange) {
                    zoomRange.min = minScale;
                    zoomRange.max = minScale * 3;
                    zoomRange.step = (minScale * 3 - minScale) / 100 || 0.01;
                    zoomRange.value = scale;
                    zoomRange.classList.remove('hidden');
                }
                draw();
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    input.addEventListener('change', () => {
        if (input.files && input.files[0]) {
            loadImage(input.files[0]);
        }
    });

    canvas.addEventListener('mousedown', (e) => {
        dragging = true;
        lastX = e.clientX;
        lastY = e.clientY;
    });
    window.addEventListener('mouseup', () => (dragging = false));
    window.addEventListener('mousemove', (e) => {
        if (!dragging || !image) return;
        offsetX += e.clientX - lastX;
        offsetY += e.clientY - lastY;
        lastX = e.clientX;
        lastY = e.clientY;
        clampOffsets();
        draw();
    });

    if (zoomRange) {
        zoomRange.addEventListener('input', () => {
            if (!image) return;
            const centerX = SIZE / 2 - offsetX;
            const centerY = SIZE / 2 - offsetY;
            const ratio = centerX / (image.width * scale);
            const ratioY = centerY / (image.height * scale);
            scale = parseFloat(zoomRange.value);
            offsetX = SIZE / 2 - ratio * image.width * scale;
            offsetY = SIZE / 2 - ratioY * image.height * scale;
            clampOffsets();
            draw();
        });
    }

    form.addEventListener('submit', function (e) {
        if (!image) return; // pas de nouvelle photo : on laisse le champ tel quel
        e.preventDefault();
        canvas.toBlob((blob) => {
            const file = new File([blob], 'photo.jpg', { type: 'image/jpeg' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;
            form.submit();
        }, 'image/jpeg', 0.92);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-photo-crop]').forEach(initPhotoCrop);
});
