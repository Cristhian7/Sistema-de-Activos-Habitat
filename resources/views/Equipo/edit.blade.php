@extends('layouts.app')

@section('title', 'Editar Equipo - Control de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-pencil-square me-2"></i>Editar Equipo: {{ $equipo->codigo_contable }}</h1>
    <a href="{{ route('equipos.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<form action="{{ route('equipos.update', $equipo->id_equipo) }}" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Información General -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Información General</h5>
                <div class="row g-3">
                    
                    <!-- Código Contable -->
                    <div class="col-md-4">
                        <label for="codigo_contable" class="form-label fw-semibold">Código Contable *</label>
                        <input type="text" 
                            class="form-control @error('codigo_contable') is-invalid @enderror"
                            id="codigo_contable" 
                            name="codigo_contable" 
                            value="{{ old('codigo_contable', $equipo->codigo_contable) }}">

                        @error('codigo_contable')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Nombre del Equipo -->
                    <div class="col-md-4">
                        <label for="nombre_equipo" class="form-label fw-semibold">Nombre del Equipo *</label>
                        <input type="text" 
                            class="form-control @error('nombre_equipo') is-invalid @enderror"
                            id="nombre_equipo"
                            name="nombre_equipo"  
                            value="{{ old('nombre_equipo', $equipo->nombre_equipo) }}">

                        @error('nombre_equipo')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    
                    <!-- Tipo de Equipo -->
                    <div class="col-md-4">
                        <label for="tipo_equipo_id_tipo_equipo" class="form-label fw-semibold">Tipo de Equipo *</label>
                        <select class="form-select @error('tipo_equipo_id_tipo_equipo') is-invalid @enderror" id="tipo_equipo_id_tipo_equipo" name="tipo_equipo_id_tipo_equipo">
                            <option value="">Seleccione un tipo...</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id_tipo_equipo }}" {{ old('tipo_equipo_id_tipo_equipo', $equipo->tipo_equipo_id_tipo_equipo ?? $equipo->id_tipo_equipo ?? '') == $tipo->id_tipo_equipo ? 'selected' : '' }}>
                                    {{ $tipo->nombre_tipo }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_equipo_id_tipo_equipo')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Marca (Con soporte robusto para persistencia) -->
                    <div class="col-md-4">
                        <label for="marca_id_marca" class="form-label fw-semibold">Marca *</label>
                        <select class="form-select @error('marca_id_marca') is-invalid @enderror" id="marca_id_marca" name="marca_id_marca">
                            <option value="">Seleccione una marca...</option>
                            @foreach($marcas as $marca)
                                <option value="{{ $marca->id_marca }}" 
                                    {{ old('marca_id_marca', $equipo->marca_id_marca ?? $equipo->marca_id ?? $equipo->id_marca ?? optional($equipo->marca)->id_marca ?? '') == $marca->id_marca ? 'selected' : '' }}>
                                    {{ $marca->nombre_marca }}
                                </option>
                            @endforeach
                        </select>
                        @error('marca_id_marca')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Modelo -->
                    <div class="col-md-4">
                        <label for="modelo_id_modelo" class="form-label fw-semibold">Modelo *</label>
                        <select class="form-select @error('modelo_id_modelo') is-invalid @enderror" id="modelo_id_modelo" name="modelo_id_modelo">
                            <option value="">Seleccione un modelo...</option>
                            @foreach($modelos as $modelo)
                                <option value="{{ $modelo->id_modelo }}" {{ old('modelo_id_modelo', $equipo->modelo_id_modelo ?? $equipo->id_modelo ?? '') == $modelo->id_modelo ? 'selected' : '' }}>
                                    {{ $modelo->nombre_modelo }}
                                </option>
                            @endforeach
                        </select>
                        @error('modelo_id_modelo')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- Estado Bloqueado en Activo -->
                    <div class="col-md-4">
                        <label for="estado_id_estado" class="form-label fw-semibold">Estado *</label>
                        <select class="form-select bg-light" disabled>
                            <option value="1" selected>Activo</option>
                        </select>
                        <input type="hidden" name="estado_id_estado" value="{{ $equipo->estado_id_estado ?? 1 }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Especificaciones de Hardware -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-cpu me-2"></i>Especificaciones de Hardware</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="procesador" class="form-label fw-semibold">Procesador</label>
                        <input type="text" class="form-control @error('procesador') is-invalid @enderror" id="procesador" name="procesador" placeholder="Ej. Core i5, i7" value="{{ old('procesador', $equipo->procesador) }}">
                        @error('procesador')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="ram" class="form-label fw-semibold">Memoria RAM</label>
                        <input type="text" class="form-control @error('ram') is-invalid @enderror" id="ram" name="ram" placeholder="Ej. 16GB" value="{{ old('ram', $equipo->ram) }}">
                        @error('ram')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="tipo_ram" class="form-label fw-semibold">Tipo de RAM</label>
                        <input type="text" class="form-control @error('tipo_ram') is-invalid @enderror" id="tipo_ram" name="tipo_ram" placeholder="Ej. DDR4 / DDR5" value="{{ old('tipo_ram', $equipo->tipo_ram) }}">
                        @error('tipo_ram')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="ssd" class="form-label fw-semibold">SSD</label>
                        <input type="text" class="form-control @error('ssd') is-invalid @enderror" id="ssd" name="ssd" placeholder="Ej. 512GB NVMe" value="{{ old('ssd', $equipo->ssd) }}">
                        @error('ssd')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="st" class="form-label fw-semibold">Service Tag</label>
                        <input type="text" class="form-control @error('st') is-invalid @enderror" id="st" name="st" placeholder="Service Tag" value="{{ old('st', $equipo->st) }}">
                        @error('st')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="ex" class="form-label fw-semibold">Express Code</label>
                        <input type="text" class="form-control @error('ex') is-invalid @enderror" id="ex" name="ex" placeholder="Express Code" value="{{ old('ex', $equipo->ex) }}">
                        @error('ex')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Datos Financieros y Notas -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-cash-stack me-2"></i>Datos Financieros y Notas</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="fecha_compra" class="form-label fw-semibold">Fecha de Compra</label>
                        <input type="date" class="form-control @error('fecha_compra') is-invalid @enderror" id="fecha_compra" name="fecha_compra" value="{{ old('fecha_compra', $equipo->fecha_compra) }}">
                        @error('fecha_compra')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="precio_compra" class="form-label fw-semibold">Precio de Compra</label>
                        <input type="number" step="0.01" class="form-control @error('precio_compra') is-invalid @enderror" id="precio_compra" name="precio_compra" value="{{ old('precio_compra', $equipo->precio_compra) }}">
                        @error('precio_compra')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="valor_libros" class="form-label fw-semibold">Valor en Libros</label>
                        <input type="number" step="0.01" class="form-control @error('valor_libros') is-invalid @enderror" id="valor_libros" name="valor_libros" value="{{ old('valor_libros', $equipo->valor_libros) }}">
                        @error('valor_libros')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label for="notas_adicionales" class="form-label fw-semibold">Notas Adicionales</label>
                        <textarea class="form-control @error('notas_adicionales') is-invalid @enderror" id="notas_adicionales" name="notas_adicionales" rows="2" placeholder="Observaciones del equipo...">{{ old('notas_adicionales', $equipo->notas_adicionales) }}</textarea>
                        @error('notas_adicionales')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Imágenes y Códigos -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-images me-2"></i>Imágenes y Códigos</h5>
                <div class="row g-3">
                    
                    <!-- Fotografía del Equipo -->
                    <div class="col-md-6">
                        <label for="imagen_equipo" class="form-label fw-semibold">Fotografía del Equipo</label>
                        @if(!empty($equipo->imagen_equipo))
                            <div class="mb-2 text-center p-2 border rounded bg-light">
                                <img src="data:image/jpeg;base64,{{ base64_encode($equipo->imagen_equipo) }}" 
                                     alt="Imagen del Equipo" 
                                     class="img-fluid rounded" 
                                     style="width: 100%; max-height: 350px; object-fit: contain;">
                                <div class="small text-muted mt-1">Imagen actual cargada</div>
                            </div>
                        @else
                            <div class="mb-2">
                                <span class="badge bg-secondary">Sin imagen registrada</span>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('imagen_equipo') is-invalid @enderror" id="imagen_equipo" name="imagen_equipo" accept="image/*">
                        @error('imagen_equipo')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Fotografía del Código Contable -->
                    <div class="col-md-6">
                        <label for="imagen_codigo" class="form-label fw-semibold">Fotografía del Código Contable</label>
                        @if(!empty($equipo->imagen_codigo))
                            <div class="mb-2 text-center p-2 border rounded bg-light">
                                <img src="data:image/jpeg;base64,{{ base64_encode($equipo->imagen_codigo) }}" 
                                     alt="Imagen del Código Contable" 
                                     class="img-fluid rounded" 
                                     style="max-height: 150px; object-fit: contain;">
                                <div class="small text-muted mt-1">Imagen actual cargada</div>
                            </div>
                        @else
                            <div class="mb-2">
                                <span class="badge bg-secondary">Sin imagen registrada</span>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('imagen_codigo') is-invalid @enderror" id="imagen_codigo" name="imagen_codigo" accept="image/*">
                        @error('imagen_codigo')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 mb-5 text-end">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Actualizar Equipo
        </button>
    </div>
</form>
@endsection