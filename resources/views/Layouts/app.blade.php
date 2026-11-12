<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Activos')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: rgba(255, 255, 255, 0.08);
            --sidebar-active: #0284c7;
        }

        body {
            background-color: #f8fafc;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* Navbar Superior */
        .navbar-custom {
            background-color: var(--sidebar-bg);
        }

        /* Sidebar / Menú Lateral */
        .sidebar-custom {
            background-color: var(--sidebar-bg);
            min-height: calc(100vh - 56px);
        }

        .sidebar-heading {
            color: #94a3b8;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
        }

        /* Enlaces del Menú */
        .sidebar-custom .nav-link {
            color: #cbd5e1;
            padding: 0.65rem 1rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
            font-weight: 500;
        }

        .sidebar-custom .nav-link:hover {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }

        .sidebar-custom .nav-link.active {
            color: #ffffff;
            background-color: var(--sidebar-active);
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Submenú de Reportería */
        .submenu-reportes {
            border-left: 1px solid rgba(255, 255, 255, 0.15);
        }

        .sidebar-custom .submenu-reportes .nav-link.sub-active {
            color: #ffffff;
            background-color: var(--sidebar-hover);
            box-shadow: inset 3px 0 0 var(--sidebar-active);
        }

        .sidebar-custom [data-bs-toggle="collapse"] .bi-chevron-down {
            transition: transform 0.2s ease-in-out;
        }

        .sidebar-custom [data-bs-toggle="collapse"][aria-expanded="true"] .bi-chevron-down {
            transform: rotate(180deg);
        }

        /* Tarjetas y Contenedores */
        .card {
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>

    <!-- Navbar Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold fs-5" href="{{ route('dashboard') }}">
                <i class="bi bi-pc-display-horizontal me-2 text-info"></i>Control de Activos
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    @isset($notifTotal)
                    {{-- Campana: avisos de documentos firmados subidos, pendientes de revisar --}}
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones: {{ $notifTotal }} pendientes">
                            <i class="bi bi-bell fs-5"></i>
                            @if($notifTotal > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $notifTotal > 99 ? '99+' : $notifTotal }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0" style="width: 360px; max-width: 92vw;">
                            <div class="px-3 py-2 border-bottom fw-semibold">Notificaciones</div>
                            @forelse($notifPendientes as $n)
                                <div class="px-3 py-2 border-bottom small">
                                    <a href="{{ $n->url }}" class="text-decoration-none text-dark d-block">
                                        <span class="badge {{ $n->tipo === 'baja' ? 'bg-danger' : 'bg-primary' }} me-1">{{ $n->tipo_texto }}</span>
                                        {{ $n->mensaje }}
                                    </a>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <span class="text-muted">{{ $n->creado_en->format('d/m/Y H:i') }}</span>
                                        <form action="{{ route('notificaciones.revisar', $n->id_notificacion) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success py-0" title="Marcar como revisado">
                                                <i class="bi bi-check2-circle me-1"></i>Revisado
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="px-3 py-3 text-muted small">No tienes notificaciones pendientes.</div>
                            @endforelse
                            <a href="{{ route('notificaciones.index') }}" class="dropdown-item text-center small py-2">
                                Ver todas{{ $notifTotal > count($notifPendientes) ? ' (' . $notifTotal . ' pendientes)' : '' }}
                            </a>
                        </div>
                    </li>
                    @endisset
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 me-2 text-info"></i> 
                            <span>{{ Auth::user()->usuario ?? 'Administrador' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-1">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger rounded py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenedor Principal -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar / Menú Lateral -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block sidebar-custom collapse shadow-sm p-3">
                <h6 class="sidebar-heading text-uppercase fw-bold mb-3 px-2">Menú Principal</h6>
                <ul class="nav flex-column gap-1">
                    @puede('dashboard.ver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    @endpuede
                    @puede('equipos.ver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('equipos.*') ? 'active' : '' }}" href="{{ route('equipos.index') }}">
                            <i class="bi bi-laptop me-2"></i> Equipos
                        </a>
                    </li>
                    @endpuede
                    @puede('empleados.ver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}" href="{{ route('empleados.index') }}">
                            <i class="bi bi-people me-2"></i> Empleados
                        </a>
                    </li>
                    @endpuede
                    @puede('traslados.ver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('traslados.*') ? 'active' : '' }}" href="{{ route('traslados.index') }}">
                            <i class="bi bi-arrow-left-right me-2"></i> Traslados
                        </a>
                    </li>
                    @endpuede
                    @puede('mantenimientos.ver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('mantenimientos.*') ? 'active' : '' }}" href="{{ route('mantenimientos.index') }}">
                            <i class="bi bi-tools me-2"></i> Mantenimiento
                        </a>
                    </li>
                    @endpuede
                    @puede('bajas.ver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('bajas.*') ? 'active' : '' }}" href="{{ route('bajas.index') }}">
                            <i class="bi bi-trash me-2"></i> Bajas
                        </a>
                    </li>
                    @endpuede
                    @puede('tarjetas.ver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tarjetas.*') ? 'active' : '' }}" href="{{ route('tarjetas.index') }}">
                            <i class="bi bi-person-vcard me-2"></i> Tarjeta de Responsabilidad
                        </a>
                    </li>
                    @endpuede
                    @puede('bitacora.ver')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('bitacora.*') ? 'active' : '' }}" href="{{ route('bitacora.index') }}">
                            <i class="bi bi-journal-text me-2 text-warning"></i> Bitácora
                        </a>
                    </li>
                    @endpuede
                    {{-- Reportería: submenú con los reportes (se abre solo cuando se está dentro de uno) --}}
                    @puede('reportes.ver')
                    @php $enReportes = request()->routeIs('reportes.*'); @endphp
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center {{ $enReportes ? 'active' : '' }}" data-bs-toggle="collapse"
                           href="#submenuReportes" role="button" aria-expanded="{{ $enReportes ? 'true' : 'false' }}" aria-controls="submenuReportes">
                            <i class="bi bi-bar-chart-line me-2"></i> Reportería
                            <i class="bi bi-chevron-down ms-auto small"></i>
                        </a>
                        <div class="collapse {{ $enReportes ? 'show' : '' }}" id="submenuReportes">
                            <ul class="nav flex-column submenu-reportes ms-3 mt-1 gap-1">
                                @foreach(\App\Http\Controllers\Reporte\ReporteController::catalogo() as $reporte)
                                    <li class="nav-item">
                                        <a class="nav-link py-2 small {{ request()->routeIs($reporte['ruta'], $reporte['ruta'] . '.*') ? 'sub-active' : '' }}" href="{{ route($reporte['ruta']) }}">
                                            <i class="bi {{ $reporte['icono'] }} me-2"></i>{{ $reporte['titulo'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @endpuede
                </ul>
            </nav>

            <!-- Contenido dinámico -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>