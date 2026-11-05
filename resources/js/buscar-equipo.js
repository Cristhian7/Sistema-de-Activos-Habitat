document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('.equipo-row');
    const noResultsRow = document.getElementById('noResultsRow');

    if (!searchInput) return;

    function FiltrarEquipo() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let matchesFound = 0;

        tableRows.forEach(row => {
            const codeCol = row.querySelector('.codigo-equipo'); 
            const nameCol = row.querySelector('.nombre-equipo');

            const codeText = codeCol ? codeCol.textContent.toLowerCase() : '';
            const nameText = nameCol ? nameCol.textContent.toLowerCase() : '';

            if (codeText.includes(searchTerm) || nameText.includes(searchTerm)) {
                row.style.display = '';
                matchesFound++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResultsRow) {
            noResultsRow.style.display = (matchesFound === 0 && tableRows.length > 0) ? '' : 'none';
        }
    }

    searchInput.addEventListener('input', FiltrarEquipo);
});