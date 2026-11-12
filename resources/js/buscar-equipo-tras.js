document.addEventListener('DOMContentLoaded', function () {
    const inputCodigo = document.getElementById('codigo_equipo');
    const infoEquipo = document.getElementById('infoEquipo');

    if (!inputCodigo) return;

    let ultimoCodigoBuscado = null;

    function setTexto(id, valor) {
        const el = document.getElementById(id);
        if (el) el.textContent = valor ?? 'N/A';
    }

    // Aviso: al crear un traslado de un equipo que ya tiene dueño, se le quitará al anterior
    const aviso = document.getElementById('avisoReasignacion');

    function mostrarAviso(data) {
        if (!aviso) return;

        aviso.textContent = '';

        if (inputCodigo.dataset.modoCrear === '1' && data && data.asignado_vigente) {
            const icono = document.createElement('i');
            icono.className = 'bi bi-exclamation-triangle-fill me-1';

            const nombre = document.createElement('strong');
            nombre.textContent = data.asignado_a || 'otro empleado';

            aviso.append(
                icono,
                'Este equipo está asignado actualmente a ',
                nombre,
                '. Al guardar el traslado se le quitará y quedará registrada su devolución.'
            );
            aviso.classList.remove('d-none');
        } else {
            aviso.classList.add('d-none');
        }
    }

    function mostrarImagen(imgId, wrapperId, src) {
        const img = document.getElementById(imgId);
        const wrapper = document.getElementById(wrapperId);
        if (!img || !wrapper) return;

        if (src) {
            img.src = src;
            wrapper.classList.remove('d-none');
        } else {
            img.src = '';
            wrapper.classList.add('d-none');
        }
    }

    function buscarEquipo() {
        const codigo = inputCodigo.value.trim();

        if (codigo === '') {
            ultimoCodigoBuscado = null;
            mostrarAviso(null);
            if (infoEquipo) infoEquipo.style.display = 'none';
            return;
        }

        // Evita repetir la búsqueda (y el alert) para el mismo valor
        if (codigo === ultimoCodigoBuscado) return;
        ultimoCodigoBuscado = codigo;

        fetch(`/traslados/buscar-equipo?codigo=${encodeURIComponent(codigo)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Autocompleta el código contable completo y oficial de la BD
                inputCodigo.value = data.codigo_contable;
                ultimoCodigoBuscado = data.codigo_contable;

                // Llena la tarjeta de información del equipo
                setTexto('lblNombreEquipo', data.nombre_equipo);
                setTexto('lblTipoEquipo', data.tipo);
                setTexto('lblMarcaEquipo', data.marca);
                setTexto('lblModeloEquipo', data.modelo);
                setTexto('lblProcesador', data.procesador);
                setTexto('lblRam', `${data.ram} (${data.tipo_ram})`);
                setTexto('lblSsd', data.ssd);
                setTexto('lblSt', data.st);
                setTexto('lblEx', data.ex);
                setTexto('lblFechaCompra', data.fecha_compra);
                setTexto('lblPrecioCompra', data.precio_compra);
                setTexto('lblBalorLibros', data.balor_libros);
                setTexto('lblEstado', data.estado);
                setTexto('lblNotas', data.notas_adicionales);

                // Imágenes del equipo
                mostrarImagen('imgEquipo', 'wrapImgEquipo', data.imagen_equipo);
                mostrarImagen('imgCodigo', 'wrapImgCodigo', data.imagen_codigo);

                mostrarAviso(data);

                if (infoEquipo) infoEquipo.style.display = 'block';
            } else {
                mostrarAviso(null);
                if (infoEquipo) infoEquipo.style.display = 'none';
                alert('No se encontró ningún equipo con ese código.');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda:', error);
        });
    }

    // Al escribir, permite volver a buscar aunque sea el mismo valor anterior
    inputCodigo.addEventListener('input', function () {
        ultimoCodigoBuscado = null;
    });

    // Búsqueda al perder el foco
    inputCodigo.addEventListener('blur', buscarEquipo);

    // Búsqueda al presionar Enter (sin enviar el formulario)
    inputCodigo.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            buscarEquipo();
        }
    });

    // Si el campo ya trae un valor al cargar (ej. en edición), busca automáticamente
    if (inputCodigo.value.trim() !== '') {
        buscarEquipo();
    }
});
