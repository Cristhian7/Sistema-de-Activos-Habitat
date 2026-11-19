@extends('layouts.app')

@section('title', 'Nueva Marca - Control de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-tags me-2"></i>Nueva Marca</h1>
    <a href="{{ route('marcas.index') }}" class="btn btn-outline-secondary btn-sm">
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
        <form action="{{ route('marcas.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nombre_marca" class="form-label fw-semibold">Nombre de la marca <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nombre_marca') is-invalid @enderror" id="nombre_marca" name="nombre_marca"
                           value="{{ old('nombre_marca') }}" maxlength="45" required autofocus>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('marcas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
