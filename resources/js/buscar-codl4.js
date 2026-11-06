document.addEventListener('DOMContentLoaded', function () {
    const inputL4 = document.getElementById('cod_l4');
    const infoEmpleado = document.getElementById('infoEmpleado');

    if (inputL4) {
        function buscarEmpleado() {
            const codL4 = inputL4.value.trim();
            if (!codL4) {
                if (infoEmpleado) infoEmpleado.style.display = 'none';
                return;
            }

            fetch(`/traslados/buscar-empleado?cod_l4=${encodeURIComponent(codL4)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('lblNombreEmpleado').textContent = data.nombre_completo;
                    document.getElementById('lblPuesto').textContent = data.puesto;
                    document.getElementById('lblAfiliado').textContent = data.afiliado;
                    if (infoEmpleado) infoEmpleado.style.display = 'block';
                } else {
                    if (infoEmpleado) infoEmpleado.style.display = 'none';
                    alert('No se encontró ningún empleado con ese código L4.');
                }
            })
            .catch(error => console.error('Error en la búsqueda de empleado:', error));
        }

        // Búsqueda al perder el foco (blur)
        inputL4.addEventListener('blur', buscarEmpleado);

        // Búsqueda al presionar Enter (evita que se envíe el formulario)
        inputL4.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Detiene el envío del formulario
                buscarEmpleado();   // Ejecuta la búsqueda AJAX
            }
        });

        // Si el campo ya trae un valor al cargar (ej. en edición), busca automáticamente
        if (inputL4.value.trim() !== '') {
            buscarEmpleado();
        }
    }
});