{{-- Tabla "Solicitar equipo nuevo": solo cuando la baja indica que necesita reemplazo --}}
<table class="solicitud table table-bordered align-middle mb-0">
    <thead class="table-dark">
        <tr>
            <th>Código Contable</th>
            <th>Fecha de Compra</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Afiliado</th>
            <th>Precio de Compra</th>
            <th>Valor en Libros</th>
            <th>Tipo de Equipo</th>
            <th>Equipo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="fw-semibold">{{ $baja->equipo->codigo_contable ?? 'N/A' }}</td>
            <td>{{ $baja->equipo?->fecha_compra ? \Carbon\Carbon::parse($baja->equipo->fecha_compra)->format('d/m/Y') : 'N/A' }}</td>
            <td>{{ $baja->equipo->marca->nombre_marca ?? 'N/A' }}</td>
            <td>{{ $baja->equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
            <td>{{ $baja->empleado->afiliado->nombre_afiliado ?? 'N/A' }}</td>
            <td>{{ $baja->equipo?->precio_compra !== null ? 'Q ' . number_format((float) $baja->equipo->precio_compra, 2) : 'N/A' }}</td>
            <td>{{ $baja->equipo?->valor_libros !== null ? 'Q ' . number_format((float) $baja->equipo->valor_libros, 2) : 'N/A' }}</td>
            <td>{{ $baja->equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
            <td class="fw-semibold">Solicitar equipo nuevo</td>
        </tr>
    </tbody>
</table>
