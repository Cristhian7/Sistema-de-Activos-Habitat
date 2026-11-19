// Piezas cambiadas o agregadas en el formulario de mantenimiento: agregar/quitar filas y vista previa de la foto.
document.addEventListener('DOMContentLoaded', function () {
    const contenedor = document.getElementById('listaPiezas');
    const plantilla = document.getElementById('plantillaPieza');
    const botonAgregar = document.getElementById('btnAgregarPieza');
    if (!contenedor || !plantilla || !botonAgregar) return;

    const maximo = parseInt(contenedor.dataset.max || '10', 10);
    const sinPiezas = document.getElementById('sinPiezas');

    // Siguiente índice libre (las filas con errores del servidor conservan el suyo)
    let siguiente = 0;
    contenedor.querySelectorAll('.fila-pieza').forEach(function (fila) {
        siguiente = Math.max(siguiente, parseInt(fila.dataset.indice, 10) + 1);
    });

    function filas() {
        return contenedor.querySelectorAll('.fila-pieza');
    }

    // Numera las piezas (Pieza 1, Pieza 2...) y muestra u oculta el aviso de "sin piezas"
    function actualizar() {
        filas().forEach(function (fila, posicion) {
            const titulo = fila.querySelector('.titulo-pieza');
            if (titulo) titulo.textContent = 'Pieza ' + (posicion + 1);
        });
        if (sinPiezas) sinPiezas.classList.toggle('d-none', filas().length > 0);
        botonAgregar.disabled = filas().length >= maximo;
    }

    // "Otra" exige describir la pieza
    function marcarObligatorioDetalle(fila) {
        const tipo = fila.querySelector('.tipo-pieza');
        const marca = fila.querySelector('.detalle-obligatorio');
        if (tipo && marca) marca.classList.toggle('d-none', tipo.value !== 'Otra');
    }

    function prepararFila(fila) {
        const tipo = fila.querySelector('.tipo-pieza');
        if (tipo) tipo.addEventListener('change', function () { marcarObligatorioDetalle(fila); });
        marcarObligatorioDetalle(fila);

        const quitar = fila.querySelector('.quitar-pieza');
        if (quitar) {
            quitar.addEventListener('click', function () {
                fila.remove();
                actualizar();
            });
        }

        // Vista previa de la foto elegida
        const archivo = fila.querySelector('.archivo-pieza');
        const vista = fila.querySelector('.vista-pieza');
        const envoltura = fila.querySelector('.vista-pieza-envoltura');
        const respaldo = fila.querySelector('.temp-pieza');
        if (archivo) {
            archivo.addEventListener('change', function (e) {
                const f = e.target.files[0];
                if (!f) return;
                const lector = new FileReader();
                lector.onload = function (ev) {
                    vista.src = ev.target.result;
                    envoltura.classList.remove('d-none');
                    // Un archivo nuevo reemplaza el respaldo del intento anterior
                    if (respaldo) respaldo.value = '';
                };
                lector.readAsDataURL(f);
            });
        }
    }

    botonAgregar.addEventListener('click', function () {
        if (filas().length >= maximo) return;
        const html = plantilla.innerHTML.replace(/__INDICE__/g, String(siguiente));
        siguiente++;
        const envoltorio = document.createElement('div');
        envoltorio.innerHTML = html.trim();
        const fila = envoltorio.firstElementChild;
        contenedor.appendChild(fila);
        prepararFila(fila);
        actualizar();
        const primero = fila.querySelector('select');
        if (primero) primero.focus();
    });

    filas().forEach(prepararFila);
    actualizar();
});
