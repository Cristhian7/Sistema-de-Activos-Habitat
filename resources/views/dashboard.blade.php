@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Activos')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">Panel General</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="text-muted small">Bienvenido al sistema de control de inventario</span>
    </div>
</div>

<!-- Tarjetas de Estadísticas / Resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white p-3 rounded-3 me-3">
                    <i class="bi bi-laptop fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Total de Equipos</span>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white p-3 rounded-3 me-3">
                    <i class="bi bi-check-circle fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">Equipos Asignados</span>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="bg-warning text-dark p-3 rounded-3 me-3">
                    <i class="bi bi-tools fs-4"></i>
                </div>
                <div>
                    <span class="text-muted small d-block">En Mantenimiento</span>
                    <h3 class="fw-bold mb-0">0</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de accesos rápidos o tablas recientes -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3">Actividad Reciente</h5>
        <p class="text-muted">Aquí podrás visualizar los últimos movimientos, traslados o registros de equipos agregados al sistema.</p>
    </div>
</div>
@endsection