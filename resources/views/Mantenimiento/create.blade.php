@extends('layouts.app')

@section('title', 'Registrar Mantenimiento')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-tools me-2"></i> Registrar Nuevo Mantenimiento
                </div>
                <div class="card-body">
                    <form action="{{ route('mantenimientos.store') }}" method="POST">
                        @csrf

                        <!-- Código de Equipo con Autocompletado -->
                        <div class="mb-3">
                            <label for="codigo_equipo" class="form-label fw-bold">Código de Equipo *</label>
                            <input type="text" class="form-control @error('codigo_equipo') is-invalid @enderror" 
                                   id="codigo_equipo" name="codigo_equipo" value="{{ old('codigo_equipo') }}" 
                                   placeholder="Ingrese código o service tag y presione Enter..." required>
                            @error('codigo_equipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Empleado Responsable -->
                        <div class="mb-3">
                            <label for="empleado_id_empleado" class="form-label fw-bold">Empleado Responsable *</label>
                            <select name="empleado_id_empleado" id="empleado_id_empleado" class="form-select @error('empleado_id_empleado') is-invalid @enderror" required>
                                <option value="">Seleccione un empleado...</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{ $empleado->id_empleado }}" {{ old('empleado_id_empleado') == $empleado->id_empleado ? 'selected' : '' }}>
                                        {{ $empleado->nombre }} {{ $empleado->apellido }}
                                    </option>
                                @endforeach
                            </select>
                            @error('empleado_id_empleado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fecha de Mantenimiento -->
                        <div class="mb-3">
                            <label for="fecha_mantenimiento" class="form-label fw-bold">Fecha de Mantenimiento *</label>
                            <input type="date" class="form-control @error('fecha_mantenimiento') is-invalid @enderror" 
                                   id="fecha_mantenimiento" name="fecha_mantenimiento" value="{{ old('fecha_mantenimiento', date('Y-m-d')) }}" required>
                            @error('fecha_mantenimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripción del Mantenimiento *</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="4" placeholder="Detalle los trabajos realizados..." required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('mantenimientos.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Mantenimiento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- Puedes reutilizar el mismo script de búsqueda rápida de equipos -->
    @vite('resources/js/buscar-traslado.js')
@endsection