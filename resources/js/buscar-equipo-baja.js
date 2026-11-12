document.addEventListener('DOMContentLoaded', function () {
    const inputCodigo = document.getElementById('codigo_equipo');
    if (!inputCodigo) return;

    let ultimoCodigoBuscado = null;
    let ultimoEquipo = null; // datos del último equipo encontrado (para la solicitud)

    const selectReemplazo = document.getElementById('necesita_reemplazo');
    const btnGenerar = document.getElementById('btnGenerarEquipo');
    const panelSolicitud = document.getElementById('panelSolicitud');

    // Años completos y meses restantes (0 a 11) entre la fecha de compra (YYYY-MM-DD) y hoy
    function calcularTiempoUso(fechaCompra) {
        const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(fechaCompra || '');
        if (!m) return null;

        const hoy = new Date();
        let anos = hoy.getFullYear() - Number(m[1]);
        let meses = hoy.getMonth() + 1 - Number(m[2]);

        // Si aún no llega el día del mes de la compra, falta un mes completo
        if (hoy.getDate() < Number(m[3])) meses--;

        if (meses < 0) {
            anos--;
            meses += 12;
        }

        // Fecha de compra futura: sin tiempo de uso
        if (anos < 0) return { anos: 0, meses: 0 };

        return { anos: anos, meses: meses };
    }

    // Imprime SOLO la solicitud de equipo nuevo (título + tabla) en un marco oculto
    function imprimirSolicitud() {
        const tabla = document.getElementById('tablaSolicitud');
        if (!tabla) return;

        const hoy = new Date().toLocaleDateString('es-GT', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const logo = document.getElementById('btnImprimirSolicitud')?.dataset.logo || '/img/logo-habitat.png';

        const html = `<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Solicitud de Equipo Nuevo</title>
<style>
    * { box-sizing: border-box; }
    @page { size: letter landscape; margin: 1.2cm; }
    body { font-family: Arial, Helvetica, sans-serif; color: #1a1a1a; margin: 0;
           -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .barra { height: 12px; background: linear-gradient(90deg, #0a8a3f 0%, #35b55f 55%, #7bd196 100%); }
    .encabezado { display: flex; align-items: center; justify-content: space-between; margin: 14px 0 18px; }
    .encabezado img { height: 70px; }
    .titulo { text-align: center; flex: 1; }
    .titulo h1 { margin: 0; font-size: 22px; letter-spacing: 3px; color: #14532d; }
    .titulo h2 { margin: 4px 0 0; font-size: 16px; letter-spacing: 5px; color: #0a8a3f; }
    .fecha { font-size: 12px; text-align: right; min-width: 90px; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    th, td { border: 1px solid #000; padding: 8px 6px; text-align: center; vertical-align: middle; }
    th { background: #c9d7ec; text-transform: uppercase; font-size: 11px; }
    td { font-weight: 600; }
</style></head>
<body>
    <div class="barra"></div>
    <div class="encabezado">
        <img src="${logo}" alt="Hábitat para la Humanidad Guatemala" onerror="this.style.display='none'">
        <div class="titulo"><h1>OFICINA NACIONAL</h1><h2>SOLICITUD DE EQUIPO NUEVO</h2></div>
        <div class="fecha">Fecha: ${hoy}</div>
    </div>
    ${tabla.outerHTML}
</body></html>`;

        const marco = document.createElement('iframe');
        marco.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;visibility:hidden;';
        marco.srcdoc = html;

        // Se imprime cuando el marco terminó de cargar (incluido el logo) y luego se retira
        marco.onload = function () {
            marco.contentWindow.focus();
            marco.contentWindow.print();
            setTimeout(function () { marco.remove(); }, 1000);
        };

        document.body.appendChild(marco);
    }

    function formatoQuetzales(valor) {
        const n = Number(valor);
        if (valor === null || valor === undefined || valor === '' || Number.isNaN(n)) return 'N/A';
        return 'Q ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatoFecha(valor) {
        const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(valor || '');
        return m ? `${m[3]}/${m[2]}/${m[1]}` : (valor || 'N/A');
    }

    // El botón "Generar equipo" solo se activa si el reemplazo es "Sí"
    function actualizarBoton() {
        const necesita = selectReemplazo && selectReemplazo.value === 'SI';
        if (btnGenerar) btnGenerar.disabled = !necesita;
        if (!necesita && panelSolicitud) panelSolicitud.classList.add('d-none');
    }

    function generarSolicitud() {
        if (!ultimoEquipo) {
            alert('Primero busque el equipo por su código contable.');
            return;
        }

        const e = ultimoEquipo;
        setTexto('s_codigo', e.codigo_contable);
        setTexto('s_fecha_compra', formatoFecha(e.fecha_compra));
        setTexto('s_marca', e.marca);
        setTexto('s_modelo', e.modelo);
        setTexto('s_afiliado', e.empleado_afiliado || 'N/A');
        setTexto('s_precio', formatoQuetzales(e.precio_compra));
        setTexto('s_valor', formatoQuetzales(e.balor_libros));
        setTexto('s_tipo', e.tipo);
        setTexto('s_equipo', 'Solicitar equipo nuevo');

        if (panelSolicitud) panelSolicitud.classList.remove('d-none');
    }

    function setTexto(id, valor) {
        const el = document.getElementById(id);
        if (el) el.textContent = valor ?? 'N/A';
    }

    const btnImprimir = document.getElementById('btnImprimirSolicitud');

    if (selectReemplazo) selectReemplazo.addEventListener('change', actualizarBoton);
    if (btnGenerar) btnGenerar.addEventListener('click', generarSolicitud);
    if (btnImprimir) btnImprimir.addEventListener('click', imprimirSolicitud);
    actualizarBoton();

    // Campos de solo lectura que se rellenan con los datos del equipo
    const campos = {
        nombre_equipo: 'b_nombre_equipo',
        tipo: 'b_tipo',
        marca: 'b_marca',
        modelo: 'b_modelo',
        estado: 'b_estado',
        procesador: 'b_procesador',
        ram: 'b_ram',
        tipo_ram: 'b_tipo_ram',
        ssd: 'b_ssd',
        st: 'b_st',
        ex: 'b_ex',
        fecha_compra: 'b_fecha_compra',
        precio_compra: 'b_precio_compra',
        balor_libros: 'b_valor_libros',
        notas_adicionales: 'b_notas',
    };

    function setValor(id, valor) {
        const el = document.getElementById(id);
        if (el) el.value = valor ?? '';
    }

    function mostrarImagen(imgId, wrapperId, src) {
        const img = document.getElementById(imgId);
        const wrapper = document.getElementById(wrapperId);
        const vacio = document.getElementById(wrapperId + '_vacio');
        if (!img || !wrapper) return;

        if (src) {
            img.src = src;
            wrapper.classList.remove('d-none');
            if (vacio) vacio.classList.add('d-none');
        } else {
            img.src = '';
            wrapper.classList.add('d-none');
            if (vacio) vacio.classList.remove('d-none');
        }
    }

    // Campos de solo lectura del empleado que tiene asignado el equipo
    const camposEmpleado = {
        empleado_cod_l4: 'b_emp_cod_l4',
        empleado_nombre: 'b_emp_nombre',
        empleado_apellido: 'b_emp_apellido',
        empleado_puesto: 'b_emp_puesto',
        empleado_afiliado: 'b_emp_afiliado',
    };

    function limpiarEmpleado() {
        Object.values(camposEmpleado).forEach(function (id) { setValor(id, ''); });
        const aviso = document.getElementById('b_emp_aviso');
        if (aviso) aviso.classList.add('d-none');
        const nota = document.getElementById('b_emp_nota');
        if (nota) nota.classList.add('d-none');
    }

    function mostrarEmpleado(data) {
        if (data.empleado_cod_l4) {
            Object.keys(camposEmpleado).forEach(function (clave) {
                setValor(camposEmpleado[clave], data[clave]);
            });
            const aviso = document.getElementById('b_emp_aviso');
            if (aviso) aviso.classList.add('d-none');

            // Equipo ya devuelto (disponible): la baja usa a su último usuario
            const nota = document.getElementById('b_emp_nota');
            if (nota) nota.classList.toggle('d-none', data.disponible !== true);
        } else {
            // El equipo no tiene traslado registrado
            limpiarEmpleado();
            const aviso = document.getElementById('b_emp_aviso');
            if (aviso) aviso.classList.remove('d-none');
        }
    }

    function limpiarCampos() {
        Object.values(campos).forEach(function (id) { setValor(id, ''); });
        setValor('b_anos_uso', '');
        setValor('b_mes_uso', '');
        ultimoEquipo = null;
        if (panelSolicitud) panelSolicitud.classList.add('d-none');
        limpiarEmpleado();
        mostrarImagen('b_img_equipo', 'b_wrap_img_equipo', null);
        mostrarImagen('b_img_codigo', 'b_wrap_img_codigo', null);
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

                mostrarImagen('b_img_equipo', 'b_wrap_img_equipo', data.imagen_equipo);
                mostrarImagen('b_img_codigo', 'b_wrap_img_codigo', data.imagen_codigo);

                // Datos del empleado que tiene asignado el equipo
                mostrarEmpleado(data);

                // Años y meses de uso = fecha de compra vs. fecha actual
                const uso = calcularTiempoUso(data.fecha_compra);
                setValor('b_anos_uso', uso ? uso.anos : '');
                setValor('b_mes_uso', uso ? uso.meses : '');

                // Guarda los datos por si se genera la solicitud de equipo nuevo
                ultimoEquipo = data;
                if (panelSolicitud && !panelSolicitud.classList.contains('d-none')) generarSolicitud();
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
