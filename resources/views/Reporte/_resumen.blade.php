{{-- Tabla de cantidades agrupadas. $filas = colección de ['etiqueta' => ..., 'cantidad' => ...] --}}
<div>
    @if($imprimir)
        <div class="seccion-titulo">{{ $titulo }}</div>
    @else
        <h2 class="h6 fw-bold text-uppercase text-secondary mb-2">{{ $titulo }}</h2>
    @endif

    <div class="{{ $imprimir ? '' : 'card' }}">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>{{ $columna }}</th>
                    <th class="text-end" style="width: 110px;">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @forelse($filas as $fila)
                    <tr>
                        <td>{{ $fila['etiqueta'] }}</td>
                        <td class="text-end fw-semibold">{{ $fila['cantidad'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-center text-muted">Sin datos</td></tr>
                @endforelse
            </tbody>
            @if($filas->count() > 1)
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th class="text-end">{{ $filas->sum('cantidad') }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
