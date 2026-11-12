document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    if (!input) return;

    const filas = Array.from(document.querySelectorAll('tr.fila-tarjeta'));
    const filaSinResultados = document.getElementById('filaSinResultados');

    function filtrar() {
        const q = input.value.trim().toLowerCase();
        let visibles = 0;

        filas.forEach(function (fila) {
            // Coincide por nombre del empleado, código L4 o código contable del equipo que tiene a su cargo
            const coincide = q === '' ||
                (fila.dataset.nombre || '').includes(q) ||
                (fila.dataset.l4 || '').includes(q) ||
                (fila.dataset.equipos || '').includes(q);

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
