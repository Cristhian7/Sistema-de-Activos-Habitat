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
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('equipos.*') ? 'active' : '' }}" href="{{ route('equipos.index') }}">
                            <i class="bi bi-laptop me-2"></i> Equipos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-people me-2"></i> Empleados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-arrow-left-right me-2"></i> Traslados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-tools me-2"></i> Mantenimiento
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-trash me-2"></i> Bajas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-journal-text me-2 text-warning"></i> Bitácora
                        </a>
                    </li>
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