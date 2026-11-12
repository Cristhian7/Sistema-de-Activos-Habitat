@extends('layouts.app')

@section('title', 'Sin permiso - Control de Activos')

@section('content')
<div class="text-center py-5">
    <div class="display-1 text-warning"><i class="bi bi-shield-lock"></i></div>
    <h1 class="h3 fw-bold mt-3">No tienes permiso para esta acción</h1>
    <p class="text-muted">Tu rol no incluye acceso a esta sección. Si lo necesitas, pídelo al administrador del sistema.</p>
    @php $inicio = \App\Support\Permisos::rutaInicio(); @endphp
    @if($inicio && !request()->routeIs($inicio))
        <a href="{{ route($inicio) }}" class="btn btn-primary btn-sm"><i class="bi bi-house me-1"></i> Ir al inicio</a>
    @endif
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">Volver</a>
</div>
@endsection
