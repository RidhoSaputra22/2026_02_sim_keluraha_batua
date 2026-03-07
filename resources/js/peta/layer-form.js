document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('warna');
    const hexDisplay = document.getElementById('warna-hex');
    const previewSwatch = document.getElementById('preview-swatch');
    const opacityInput = document.getElementById('fill_opacity');

    if (colorInput) {
        colorInput.addEventListener('input', function() {
            hexDisplay.value = this.value;
            if (previewSwatch) {
                previewSwatch.style.backgroundColor = this.value;
            }
        });
    }

    if (opacityInput && previewSwatch) {
        opacityInput.addEventListener('input', function() {
            previewSwatch.style.opacity = parseFloat(this.value) + 0.3;
        });
    }
});
