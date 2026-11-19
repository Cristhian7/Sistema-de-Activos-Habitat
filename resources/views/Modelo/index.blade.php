@extends('layouts.app')

@section('title', 'Modelos - Control de Activos')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-motherboard me-2"></i>Modelos</h1>
    @puede('modelos.crear')
    <a href="{{ route('modelos.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Modelo
    </a>
    @endpuede
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="py-3 ps-3">Marca</th>
                        <th class="py-3">Modelo</th>
                        <th class="py-3 text-center">Equipos</th>
                        <th class="py-3 text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modelos as $modelo)
                        <tr>
                            <td class="ps-3">{{ $modelo->marca->nombre_marca ?? 'Sin marca' }}</td>
                            <td class="fw-semibold">{{ $modelo->nombre_modelo }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $modelo->equipos_count }}</span></td>
                            <td class="text-end pe-3">
                                @puede('modelos.editar')
                                <a href="{{ route('modelos.edit', $modelo->id_modelo) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endpuede
                                @puede('modelos.eliminar')
                                @if($modelo->equipos_count > 0)
                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Lo usan equipos: no se puede eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <form action="{{ route('modelos.destroy', $modelo->id_modelo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar el modelo {{ e($modelo->nombre_modelo) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                                @endpuede
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No hay modelos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
