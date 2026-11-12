@extends('layouts.app')

@section('title', 'Notificaciones - Control de Activos')

@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark"><i class="bi bi-bell me-2"></i>Notificaciones</h1>
    @if($pendientes->count() > 1)
        <form action="{{ route('notificaciones.revisarTodas') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-success btn-sm"><i class="bi bi-check2-all me-1"></i> Marcar todas como revisadas</button>
        </form>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<p class="text-muted">Aquí llegan los avisos cuando el Administrador o el Analista suben el documento firmado de un traslado o de una baja. Revísalo y márcalo como revisado.</p>

<h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Pendientes de revisar ({{ $pendientes->count() }})</h2>
<div class="card mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Aviso</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendientes as $n)
                    <tr>
                        <td class="text-nowrap">{{ $n->creado_en->format('d/m/Y H:i') }}</td>
                        <td><span class="badge {{ $n->tipo === 'baja' ? 'bg-danger' : 'bg-primary' }}">{{ $n->tipo_texto }}</span></td>
                        <td>{{ $n->mensaje }}</td>
                        <td class="text-end text-nowrap">
                            <a href="{{ $n->url }}#documento-firmado" class="btn btn-outline-secondary btn-sm" title="Ver el {{ strtolower($n->tipo_texto) }} y su documento">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('notificaciones.revisar', $n->id_notificacion) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" title="Marcar como revisado">
                                    <i class="bi bi-check2-circle me-1"></i>Revisado
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No tienes notificaciones pendientes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<h2 class="h6 fw-bold text-uppercase text-secondary mb-2">Revisadas (últimas {{ $revisadas->count() }})</h2>
<div class="card mb-5">
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Aviso del</th>
                    <th>Tipo</th>
                    <th>Aviso</th>
                    <th>Revisado el</th>
                </tr>
            </thead>
            <tbody>
                @forelse($revisadas as $n)
                    <tr class="text-muted">
                        <td class="text-nowrap">{{ $n->creado_en->format('d/m/Y H:i') }}</td>
                        <td><span class="badge bg-secondary">{{ $n->tipo_texto }}</span></td>
                        <td><a href="{{ $n->url }}" class="text-decoration-none text-muted">{{ $n->mensaje }}</a></td>
                        <td class="text-nowrap"><i class="bi bi-check2-circle text-success me-1"></i>{{ $n->revisado_en->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Todavía no has revisado ninguna.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
