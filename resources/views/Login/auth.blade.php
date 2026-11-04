<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Control de Activos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow-sm p-4 border-0" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <div class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                <i class="bi bi-pc-display-horizontal fs-4"></i>
            </div>
            <h4 class="fw-bold text-dark">Control de Activos</h4>
            <p class="text-muted small">Ingrese sus credenciales para acceder</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                @foreach ($errors->all() as $error)
                    <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="usuario" class="form-label small fw-semibold">Usuario</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="usuario" name="usuario" value="{{ old('usuario') }}" required autofocus placeholder="Ingrese su usuario">
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn btn-dark w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar al Sistema
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>