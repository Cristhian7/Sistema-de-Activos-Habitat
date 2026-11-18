document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    if (!input) return;

    const filas = Array.from(document.querySelectorAll('tr.fila-mantenimiento'));
    const filaSinResultados = document.getElementById('filaSinResultados');
    const contador = document.getElementById('contadorResultados');

    // Minúsculas y sin tildes, para que "jose" encuentre "José"
    function normalizar(texto) {
        return (texto || '')
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '');
    }

    function filtrar() {
        const q = normalizar(input.value.trim());
        let visibles = 0;

        filas.forEach(function (fila) {
            const codigo = normalizar(fila.dataset.codigo);
            const fecha = normalizar(fila.dataset.fecha);       // trae 2026-11-06 y 06/11/2026
            const empleado = normalizar(fila.dataset.empleado);
            const tecnico = normalizar(fila.dataset.tecnico);

            // Coincide si el texto aparece en código contable, fecha, empleado o técnico
            const coincide = q === '' ||
                codigo.includes(q) ||
                fecha.includes(q) ||
                empleado.includes(q) ||
                tecnico.includes(q);

            fila.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        // Mensaje de "sin resultados" solo si hay texto y nada coincide
        if (filaSinResultados) {
            filaSinResultados.style.display = (q !== '' && visibles === 0) ? '' : 'none';
        }

        if (contador) {
            contador.textContent = q === ''
                ? ''
                : visibles + ' de ' + filas.length + ' mantenimientos';
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
