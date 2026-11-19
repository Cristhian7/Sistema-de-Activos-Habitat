{{--
    Una fila de "pieza cambiada o agregada".
    Variables: $i (índice, o "__INDICE__" en la plantilla), $fila (array con los datos anteriores, vacío si es nueva)
--}}
@php
    $fila = $fila ?? [];
    $tipoActual = $fila['tipo_pieza'] ?? '';
    $accionActual = $fila['accion'] ?? \App\Models\Mantenimiento\MantenimientoPieza::ACCION_CAMBIO;
    $respaldo = $fila['temp_imagen'] ?? '';
    $campo = fn ($nombre) => "piezas.$i.$nombre";
@endphp
<div class="border rounded p-3 mb-3 bg-light fila-pieza" data-indice="{{ $i }}">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong class="titulo-pieza">Pieza</strong>
        <button type="button" class="btn btn-outline-danger btn-sm quitar-pieza" title="Quitar esta pieza">
            <i class="bi bi-x-lg me-1"></i>Quitar
        </button>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Pieza <span class="text-danger">*</span></label>
            <select name="piezas[{{ $i }}][tipo_pieza]" class="form-select tipo-pieza @error($campo('tipo_pieza')) is-invalid @enderror">
                <option value="">Seleccione...</option>
                @foreach(\App\Models\Mantenimiento\MantenimientoPieza::TIPOS as $tipo)
                    <option value="{{ $tipo }}" @selected($tipoActual === $tipo)>{{ $tipo }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">¿Qué se hizo? <span class="text-danger">*</span></label>
            <select name="piezas[{{ $i }}][accion]" class="form-select @error($campo('accion')) is-invalid @enderror">
                @foreach(\App\Models\Mantenimiento\MantenimientoPieza::ACCIONES as $valor => $texto)
                    <option value="{{ $valor }}" @selected($accionActual === $valor)>{{ $texto }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-semibold">Detalle <span class="text-danger d-none detalle-obligatorio">*</span></label>
            <input type="text" name="piezas[{{ $i }}][descripcion]" maxlength="255"
                   class="form-control @error($campo('descripcion')) is-invalid @enderror"
                   value="{{ $fila['descripcion'] ?? '' }}" placeholder="Ej. de 8 GB a 16 GB DDR4">
        </div>

        <div class="col-md-12">
            <label class="form-label fw-semibold">Fotografía de la pieza <span class="text-danger">*</span></label>
            <input type="hidden" name="piezas[{{ $i }}][temp_imagen]" class="temp-pieza" value="{{ $respaldo }}">

            <div class="vista-pieza-envoltura mb-2 text-center p-2 border rounded bg-white {{ $respaldo ? '' : 'd-none' }}">
                <img class="vista-pieza img-fluid rounded" alt="Vista previa de la pieza"
                     src="{{ $respaldo ? 'data:image/jpeg;base64,' . $respaldo : '' }}"
                     style="max-height: 220px; object-fit: contain;">
                <div class="small text-muted mt-1">Vista previa</div>
            </div>

            <input type="file" name="piezas[{{ $i }}][imagen]" class="form-control archivo-pieza @error($campo('imagen')) is-invalid @enderror"
                   accept="image/jpeg,image/png,image/webp">
            <div class="form-text">Obligatoria. JPG, PNG o WEBP, máximo 5 MB.</div>
            @error($campo('imagen'))
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            @error($campo('descripcion'))
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            @error($campo('tipo_pieza'))
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
            @error($campo('accion'))
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
