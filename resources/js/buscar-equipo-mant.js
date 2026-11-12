document.addEventListener('DOMContentLoaded', function () {
    const inputCodigo = document.getElementById('codigo_equipo');
    if (!inputCodigo) return;

    let ultimoCodigoBuscado = null;

    // Campos de solo lectura que se rellenan con los datos del equipo
    const campos = {
        nombre_equipo: 'm_nombre_equipo',
        tipo: 'm_tipo',
        marca: 'm_marca',
        modelo: 'm_modelo',
        estado: 'm_estado',
        procesador: 'm_procesador',
        ram: 'm_ram',
        tipo_ram: 'm_tipo_ram',
        ssd: 'm_ssd',
        st: 'm_st',
        ex: 'm_ex',
        fecha_compra: 'm_fecha_compra',
        precio_compra: 'm_precio_compra',
        balor_libros: 'm_valor_libros',
        notas_adicionales: 'm_notas',
    };

    function setValor(id, valor) {
        const el = document.getElementById(id);
        if (el) el.value = valor ?? '';
    }

    function limpiarCampos() {
        Object.values(campos).forEach(function (id) { setValor(id, ''); });
    }

    function buscarEquipo() {
        const codigo = inputCodigo.value.trim();

        if (codigo === '') {
            ultimoCodigoBuscado = null;
            limpiarCampos();
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
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                // Autocompleta el código completo y oficial (sirve con los últimos 4 dígitos)
                inputCodigo.value = data.codigo_contable;
                ultimoCodigoBuscado = data.codigo_contable;

                Object.keys(campos).forEach(function (clave) {
                    setValor(campos[clave], data[clave]);
                });
            } else {
                limpiarCampos();
                alert('No se encontró ningún equipo con ese código.');
            }
        })
        .catch(function (error) {
            console.error('Error en la búsqueda:', error);
        });
    }

    // Al escribir, permite volver a buscar aunque sea el mismo valor anterior
    inputCodigo.addEventListener('input', function () {
        ultimoCodigoBuscado = null;
    });

    inputCodigo.addEventListener('blur', buscarEquipo);

    inputCodigo.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            buscarEquipo();
        }
    });

    // Si el campo ya trae valor (por ejemplo tras un error de validación), busca al cargar
    if (inputCodigo.value.trim() !== '') {
        buscarEquipo();
    }
});
