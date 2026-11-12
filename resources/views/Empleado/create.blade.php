@extends('layouts.app')

@section('title', 'Nuevo Empleado - Control de Activos')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-person-plus me-2"></i>Registrar Nuevo Empleado</h1>
    <a href="{{ route('empleados.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver al listado
    </a>
</div>

<!-- Mostrar errores de validación si existen -->
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>¡Atención!</strong> Por favor corrige los errores en el formulario.
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('empleados.store') }}" method="POST">
            @csrf

            <div class="row">
                <!-- Nombre -->
                <div class="col-md-6 mb-3">
                    <label for="nombre_empleado" class="form-label fw-semibold">Nombres del Empleado <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nombre_empleado') is-invalid @enderror" id="nombre_empleado" name="nombre_empleado" value="{{ old('nombre_empleado') }}" required>
                </div>

                <!-- Apellido -->
                <div class="col-md-6 mb-3">
                    <label for="apellido_empleado" class="form-label fw-semibold">Apellidos del Empleado <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('apellido_empleado') is-invalid @enderror" id="apellido_empleado" name="apellido_empleado" value="{{ old('apellido_empleado') }}" required>
                </div>
            </div>

            <div class="row">
                <!-- Código L4 -->
                <div class="col-md-6 mb-3">
                    <label for="cod_l4" class="form-label fw-semibold">Código L4 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('cod_l4') is-invalid @enderror" id="cod_l4" name="cod_l4" value="{{ old('cod_l4') }}">
                </div>

                <!-- Puesto -->
                <div class="col-md-6 mb-3">
                    <label for="puesto_id_puesto" class="form-label fw-semibold">Puesto <span class="text-danger">*</span></label>
                    <select class="form-select @error('puesto_id_puesto') is-invalid @enderror" id="puesto_id_puesto" name="puesto_id_puesto" required>
                        <option value="">Seleccione un puesto...</option>
                        @foreach($puestos as $puesto)
                            <option value="{{ $puesto->id_puesto }}" {{ old('puesto_id_puesto') == $puesto->id_puesto ? 'selected' : '' }}>
                                {{ $puesto->nombre_puesto }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Afiliado -->
                <div class="col-md-4 mb-3">
                    <label for="afiliado_id_afiliado" class="form-label fw-semibold">Afiliado <span class="text-danger">*</span></label>
                    <select class="form-select @error('afiliado_id_afiliado') is-invalid @enderror" id="afiliado_id_afiliado" name="afiliado_id_afiliado" required>
                        <option value="">Seleccione un afiliado...</option>
                        @foreach($afiliados as $afiliado)
                            <option value="{{ $afiliado->id_afiliado }}" {{ old('afiliado_id_afiliado') == $afiliado->id_afiliado ? 'selected' : '' }}>
                                {{ $afiliado->nombre_afiliado }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rol -->
                <div class="col-md-4 mb-3">
                    <label for="rol_id_rol" class="form-label fw-semibold">Rol <span class="text-danger">*</span></label>
                    <select class="form-select @error('rol_id_rol') is-invalid @enderror" id="rol_id_rol" name="rol_id_rol" required>
                        <option value="">Seleccione un rol...</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id_rol }}" {{ old('rol_id_rol') == $rol->id_rol ? 'selected' : '' }}>
                                {{ $rol->nombre_rol }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Estado (Bloqueado por defecto en Activo) -->
                <div class="col-md-4 mb-3">
                    <label for="estado_id_estado" class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                    
                    <!-- Campo visual bloqueado para el usuario -->
                    <select class="form-select bg-light" id="estado_id_estado" disabled>
                        <option value="{{ $estadoActivo->id_estado }}" selected>
                            {{ $estadoActivo->nombre_estado }}
                        </option>
                    </select>

                    <!-- Campo oculto para asegurar que el ID se envíe al controlador al hacer Submit -->
                    <input type="hidden" name="estado_id_estado" value="{{ $estadoActivo->id_estado }}">
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('empleados.index') }}" class="btn btn-light border">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Guardar Empleado
                </button>
            </div>
        </form>
    </div>
</div>

@endsection