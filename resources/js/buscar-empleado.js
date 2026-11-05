document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const empleadoRows = document.querySelectorAll('.empleado-row');
    const noResultsRow = document.getElementById('noResultsRow');

    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;

        empleadoRows.forEach(row => {
            const nombre = row.querySelector('.nombre-empleado')?.textContent.toLowerCase() || '';
            const apellido = row.querySelector('.apellido-empleado')?.textContent.toLowerCase() || '';
            const codL4 = row.querySelector('.cod-l4')?.textContent.toLowerCase() || '';

            // Busca si el texto coincide con el nombre, apellido o código L4
            if (nombre.includes(searchTerm) || apellido.includes(searchTerm) || codL4.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Mostrar u ocultar la fila de "No se encontraron resultados"
        if (noResultsRow) {
            noResultsRow.style.display = (visibleCount === 0) ? '' : 'none';
        }
    });
});