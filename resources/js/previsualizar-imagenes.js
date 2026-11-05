document.addEventListener('DOMContentLoaded', function () {
    function setupImagePreview(inputId, wrapperId, imgId, hiddenInputName) {
        const input = document.getElementById(inputId);
        const wrapper = document.getElementById(wrapperId);
        const img = document.getElementById(imgId);
        const hiddenInput = document.querySelector(`input[name="${hiddenInputName}"]`);

        if (!input) return;

        input.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    wrapper.classList.remove('d-none');
                    if (hiddenInput) hiddenInput.value = '';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    setupImagePreview('imagen_equipo', 'preview_imagen_equipo_wrapper', 'preview_imagen_equipo', 'temp_imagen_equipo');
    setupImagePreview('imagen_codigo', 'preview_imagen_codigo_wrapper', 'preview_imagen_codigo', 'temp_imagen_codigo');
});