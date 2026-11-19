{{--
    Valor en libros: lo calcula el sistema, no se captura.
    Regla: vida útil de 3 años, depreciación de 33.33% anual sobre el precio de compra, por meses completos desde la fecha de compra.
    Variable opcional: $equipo (al editar)
--}}
@php
    $dep = isset($equipo) ? $equipo->depreciacion : null;
    $fmt = fn ($v) => $v !== null ? 'Q ' . number_format((float) $v, 2) : '';
@endphp
<label for="valor_libros_calculado" class="form-label fw-semibold">Valor en Libros <span class="text-muted fw-normal">(automático)</span></label>
<input type="text" class="form-control bg-light" id="valor_libros_calculado" readonly tabindex="-1"
       value="{{ $dep ? $fmt($dep['valor_libros']) : '' }}" placeholder="Se calcula con el precio y la fecha de compra">
<div class="small text-muted mt-1" id="detalle_depreciacion">
    @if($dep && $dep['valor_libros'] !== null)
        Depreciado {{ rtrim(rtrim(number_format($dep['porcentaje'], 2), '0'), '.') }}% ({{ $fmt($dep['acumulada']) }}) en {{ $dep['meses'] }} {{ $dep['meses'] === 1 ? 'mes' : 'meses' }}.
    @else
        Pierde 33.33% de su precio por año durante {{ \App\Models\Equipo\Equipo::VIDA_UTIL_ANOS }} años.
    @endif
</div>

<script>
    // Vista previa en pantalla; el valor oficial lo calcula y guarda el servidor.
    (function () {
        var VIDA_MESES = {{ \App\Models\Equipo\Equipo::VIDA_UTIL_ANOS * 12 }};
        var precio = document.getElementById('precio_compra');
        var fecha = document.getElementById('fecha_compra');
        var salida = document.getElementById('valor_libros_calculado');
        var detalle = document.getElementById('detalle_depreciacion');
        if (!precio || !fecha || !salida) return;

        function q(n) { return 'Q ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

        function calcular() {
            var p = parseFloat(precio.value), f = fecha.value;
            if (isNaN(p) || p < 0 || !/^\d{4}-\d{2}-\d{2}$/.test(f)) {
                salida.value = '';
                detalle.textContent = 'Pierde 33.33% de su precio por año durante {{ \App\Models\Equipo\Equipo::VIDA_UTIL_ANOS }} años.';
                return;
            }
            var a = f.split('-').map(Number), h = new Date();
            var meses = (h.getFullYear() - a[0]) * 12 + (h.getMonth() + 1 - a[1]);
            if (h.getDate() < a[2]) meses--;
            meses = Math.max(0, Math.min(meses, VIDA_MESES));
            var acumulada = Math.round(p * meses / VIDA_MESES * 100) / 100;
            salida.value = q(Math.round((p - acumulada) * 100) / 100);
            var pct = Math.round(meses / VIDA_MESES * 10000) / 100;
            detalle.textContent = 'Depreciado ' + pct + '% (' + q(acumulada) + ') en ' + meses + (meses === 1 ? ' mes.' : ' meses.');
        }

        precio.addEventListener('input', calcular);
        fecha.addEventListener('input', calcular);
        calcular();
    })();
</script>
