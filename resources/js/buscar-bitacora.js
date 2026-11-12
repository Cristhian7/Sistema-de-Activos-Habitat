document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    if (!input) return;

    const filas = Array.from(document.querySelectorAll('tr.fila-bitacora'));
    const filaSinResultados = document.getElementById('filaSinResultados');

    function filtrar() {
        const q = input.value.trim().toLowerCase();
        let visibles = 0;

        filas.forEach(function (fila) {
            // Coincide por código contable (completo o parcial), nombre del equipo o empleado
            const coincide = q === '' ||
                (fila.dataset.codigo || '').includes(q) ||
                (fila.dataset.nombre || '').includes(q) ||
                (fila.dataset.empleado || '').includes(q);

            fila.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        if (filaSinResultados) {
            filaSinResultados.style.display = (q !== '' && visibles === 0) ? '' : 'none';
        }
    }

    input.addEventListener('input', filtrar);

    // Evita que Enter recargue la página
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') e.preventDefault();
    });

    filtrar();
});
