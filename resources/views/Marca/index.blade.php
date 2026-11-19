@extends('layouts.app')

@section('title', 'Marcas - Control de Activos')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-tags me-2"></i>Marcas</h1>
    @puede('marcas.crear')
    <a href="{{ route('marcas.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Nueva Marca
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
                        <th class="py-3 text-center">Modelos</th>
                        <th class="py-3 text-center">Equipos</th>
                        <th class="py-3 text-end pe-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($marcas as $marca)
                        <tr>
                            <td class="ps-3 fw-semibold">{{ $marca->nombre_marca }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $marca->modelos_count }}</span></td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $marca->equipos_count }}</span></td>
                            <td class="text-end pe-3">
                                @puede('marcas.editar')
                                <a href="{{ route('marcas.edit', $marca->id_marca) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endpuede
                                @puede('marcas.eliminar')
                                @if($marca->modelos_count > 0)
                                    <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Tiene modelos: no se puede eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <form action="{{ route('marcas.destroy', $marca->id_marca) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la marca {{ e($marca->nombre_marca) }}?');">
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
                            <td colspan="4" class="text-center text-muted py-4">No hay marcas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
