@extends('layouts.app')

@section('title', 'Editar Modelo - Control de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-motherboard me-2"></i>Editar Modelo</h1>
    <a href="{{ route('modelos.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if($modelo->equipos_count > 0)
            <p class="text-muted small">Este modelo lo usan {{ $modelo->equipos_count }} {{ $modelo->equipos_count === 1 ? 'equipo' : 'equipos' }}: se puede cambiar el nombre, pero no la marca.</p>
        @endif
        <form action="{{ route('modelos.update', $modelo->id_modelo) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="marca_id_marca" class="form-label fw-semibold">Marca <span class="text-danger">*</span></label>
                    <select class="form-select @error('marca_id_marca') is-invalid @enderror" id="marca_id_marca" name="marca_id_marca" required>
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->id_marca }}" @selected(old('marca_id_marca', $modelo->marca_id_marca) == $marca->id_marca)>{{ $marca->nombre_marca }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nombre_modelo" class="form-label fw-semibold">Nombre del modelo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nombre_modelo') is-invalid @enderror" id="nombre_modelo" name="nombre_modelo"
                           value="{{ old('nombre_modelo', $modelo->nombre_modelo) }}" maxlength="45" required>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('modelos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
@endsection
