document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    if (!input) return;

    const filas = Array.from(document.querySelectorAll('tr.fila-traslado'));
    const filaSinResultados = document.getElementById('filaSinResultados');

    function filtrar() {
        const q = input.value.trim().toLowerCase();
        let visibles = 0;

        filas.forEach(function (fila) {
            const codigo = fila.dataset.codigo || '';
            const empleado = fila.dataset.empleado || '';
            const st = fila.dataset.st || '';

            // Coincide si el texto buscado aparece en código contable, empleado o service tag
            const coincide = q === '' ||
                codigo.includes(q) ||
                empleado.includes(q) ||
                st.includes(q);

            fila.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        // Muestra el mensaje de "sin resultados" solo si hay texto y nada coincide
        if (filaSinResultados) {
            filaSinResultados.style.display = (q !== '' && visibles === 0) ? '' : 'none';
        }
    }

    // Filtra en vivo mientras se escribe
    input.addEventListener('input', filtrar);

    // Evita que Enter envíe el formulario
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') e.preventDefault();
    });

    // Filtra al cargar, por si el campo ya trae texto
    filtrar();
});
