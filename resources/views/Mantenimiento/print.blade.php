<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de Mantenimiento #{{ $mantenimiento->id_mantenimiento }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: "Arial", "Georgia", "Times New Roman", serif;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            background: #e5e7eb;
        }
        .hoja {
            width: 816px;              /* ~ carta a 96dpi */
            height: 1056px;            /* alto exacto de una carta a 96dpi */
            margin: 20px auto;
            background: #fff;
            padding: 44px 56px 52px;
            box-shadow: 0 2px 14px rgba(0,0,0,.15);
            position: relative;
            overflow: hidden;          /* para que las barras lleguen al borde */
            display: flex;
            flex-direction: column;    /* reparte el contenido en vertical */
        }
        /* Zona central (fotografías) que crece para llenar la hoja */
        .centro {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        /* Barras verdes superior e inferior, de borde a borde */
        .hoja::before,
        .hoja::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 16px;
            background: linear-gradient(90deg, #0a8a3f 0%, #35b55f 55%, #7bd196 100%);
        }
        .hoja::before { top: 0; }
        .hoja::after { bottom: 0; }

        /* Encabezado */
        .encabezado {
            position: relative;
            min-height: 96px;
            margin: 6px 0 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Logo como marca detrás del texto */
        .encabezado .logo {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 180px;
            text-align: left;
            z-index: 1;
            opacity: .92;
        }
        .encabezado .logo img { max-width: 100%; height: auto; }
        .logo-fallback {
            font-family: "Cambria", "Georgia", serif;
            font-weight: 800;
            line-height: 1.05;
        }
        .logo-fallback .l1 { color: #0a8a3f; font-size: 34px; }
        .logo-fallback .l2 { color: #111; font-size: 16px; }
        .logo-fallback .l3 { color: #0a8a3f; font-size: 15px; }

        /* Título centrado respecto a toda la hoja, por encima del logo */
        .titulo {
            position: relative;
            text-align: center;
            z-index: 2;
            width: 100%;
        }
        .titulo h1 {
            font-family: "Cambria", "Georgia", serif;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 3px;
            color: #14532d;
        }
        .titulo h2 {
            font-family: "Cambria", "Georgia", serif;
            font-size: 21px;
            font-weight: 700;
            margin: 3px 0 0;
            letter-spacing: 6px;
            color: #0a8a3f;
        }

        .saludo { font-size: 15px; margin: 10px 0; line-height: 1.5; }
        .saludo strong { font-weight: 700; }

        /* Tabla de datos */
        table.datos {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-top: 8px;
        }
        table.datos td {
            border: 1px solid #000;
            padding: 6px 10px;
            vertical-align: top;
        }
        table.datos td.etq {
            background: #c9d7ec;
            font-weight: 700;
            text-transform: uppercase;
            width: 18%;
            white-space: nowrap;
        }
        table.datos td.val { width: 32%; }
        .texto-largo { white-space: pre-line; }

        /* Fotografías del mantenimiento */
        .imagenes {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 36px;
            margin: 0;
        }
        .imagenes img {
            max-width: 330px;
            max-height: 300px;
            object-fit: contain;
            box-shadow: 0 4px 12px rgba(0,0,0,.35);
            border-radius: 6px;
        }

        /* Firmas (fijas al pie de la hoja) */
        .firmas {
            display: flex;
            justify-content: center;
            gap: 110px;
            flex: 0 0 auto;
            margin-top: 40px;
            padding-bottom: 28px;
        }
        .firma {
            flex: 0 0 260px;
            text-align: center;
        }
        .firma .linea {
            border-bottom: 1px solid #000;
            width: 220px;
            margin: 0 auto 8px;
            height: 1px;
        }
        .firma .dato {
            font-weight: 700;
            font-size: 14px;
            line-height: 1.4;
        }
        .firma .dato .rol {
            display: inline-block;
            margin-top: 4px;
            font-family: "Cambria", "Georgia", serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0a8a3f;
            font-weight: 700;
        }

        .barra-acciones {
            width: 816px;
            margin: 20px auto 0;
            text-align: right;
            font-family: Arial, sans-serif;
        }
        .btn {
            display: inline-block;
            font-size: 14px;
            padding: 8px 18px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-print { background: #0d6efd; color: #fff; }
        .btn-back { background: #6b7280; color: #fff; margin-left: 8px; }

        @media print {
            html, body { height: auto; margin: 0; }
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .hoja {
                box-shadow: none;
                margin: 0;
                width: 21.59cm;            /* ancho carta */
                height: 27.94cm;           /* alto carta: llena toda la página */
                padding: 1.1cm 1.3cm 1.2cm;
                overflow: hidden;
            }
            .barra-acciones { display: none; }
            /* Sin margen de página para que las barras verdes lleguen al borde del papel */
            @page { size: letter; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="barra-acciones">
        <button class="btn btn-print" onclick="window.print()">Imprimir</button>
        <a class="btn btn-back" href="{{ route('mantenimientos.show', $mantenimiento->id_mantenimiento) }}">Volver</a>
    </div>

    @php
        $logoPath = public_path('img/logo-habitat.png');
        $logoExiste = file_exists($logoPath);

        $equipo = $mantenimiento->equipo;

        // Técnico que realizó el mantenimiento (empleado guardado al registrarlo)
        $nombreTecnico = trim(($tecnico->nombre_empleado ?? '') . ' ' . ($tecnico->apellido_empleado ?? ''));
        $afiliadoTecnico = $tecnico->afiliado->nombre_afiliado ?? '';

        // Empleado del mantenimiento
        $nombreEmpleado = trim(($mantenimiento->empleado->nombre_empleado ?? '') . ' ' . ($mantenimiento->empleado->apellido_empleado ?? ''));
        $afiliadoEmpleado = $mantenimiento->empleado->afiliado->nombre_afiliado ?? '';

        $hayFotos = !empty($mantenimiento->imagen_mantenimiento_1) || !empty($mantenimiento->imagen_mantenimiento_2);
    @endphp

    <div class="hoja">
        <!-- Encabezado con logo -->
        <div class="encabezado">
            <div class="logo">
                @if($logoExiste)
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Hábitat para la Humanidad Guatemala">
                @else
                    <div class="logo-fallback">
                        <div class="l1">Hábitat</div>
                        <div class="l2">para la Humanidad®</div>
                        <div class="l3">Guatemala</div>
                    </div>
                @endif
            </div>
            <div class="titulo">
                <h1>OFICINA NACIONAL</h1>
                <h2>MANTENIMIENTO</h2>
            </div>
        </div>

        <!-- Saludo -->
        <p class="saludo">Es un gusto poder saludarle por este medio.</p>
        <p class="saludo">La presente es para hacer constar el <strong>MANTENIMIENTO</strong> realizado al equipo con las siguientes características:</p>

        <!-- Tabla de datos -->
        <table class="datos">
            <tr>
                <td class="etq">Equipo:</td>
                <td class="val">{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                <td class="etq">Código:</td>
                <td class="val">{{ $equipo->codigo_contable ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="etq">Marca:</td>
                <td class="val">{{ $equipo->marca->nombre_marca ?? 'N/A' }}</td>
                <td class="etq">Modelo:</td>
                <td class="val">{{ $equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="etq">RAM:</td>
                <td class="val">{{ $equipo->ram ?? 'N/A' }}</td>
                <td class="etq">Tipo de RAM:</td>
                <td class="val">{{ $equipo->tipo_ram ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="etq">Procesador:</td>
                <td class="val">{{ $equipo->procesador ?? 'N/A' }}</td>
                <td class="etq">SSD:</td>
                <td class="val">{{ $equipo->ssd ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="etq">S/N:</td>
                <td class="val">{{ $equipo->st ?? 'N/A' }}</td>
                <td class="etq">ST:</td>
                <td class="val">{{ $equipo->ex ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="etq">Trabajo realizado:</td>
                <td class="val texto-largo" colspan="3">{{ $mantenimiento->descripcion ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="etq">Para:</td>
                <td class="val" colspan="3">{{ $nombreEmpleado ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="etq">Afiliado:</td>
                <td class="val" colspan="3">{{ $afiliadoEmpleado ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="etq">Fecha de mantenimiento:</td>
                <td class="val" colspan="3">{{ \Carbon\Carbon::parse($mantenimiento->fecha_mantenimiento)->format('d/m/Y') }}</td>
            </tr>
        </table>

        <!-- Zona central: fotografías del mantenimiento (crece para llenar la hoja) -->
        <div class="centro">
            @if($hayFotos)
                <div class="imagenes">
                    @if(!empty($mantenimiento->imagen_mantenimiento_1))
                        <img src="data:image/jpeg;base64,{{ base64_encode($mantenimiento->imagen_mantenimiento_1) }}" alt="Fotografía 1 del mantenimiento">
                    @endif
                    @if(!empty($mantenimiento->imagen_mantenimiento_2))
                        <img src="data:image/jpeg;base64,{{ base64_encode($mantenimiento->imagen_mantenimiento_2) }}" alt="Fotografía 2 del mantenimiento">
                    @endif
                </div>
            @endif
        </div>

        <!-- Firmas -->
        <div class="firmas">
            <div class="firma">
                <div class="linea"></div>
                <div class="dato">
                    {{ $nombreTecnico ?: 'Técnico' }}<br>
                    {{ $afiliadoTecnico ?: '—' }}<br>
                    <span class="rol">Realiza</span>
                </div>
            </div>
            <div class="firma">
                <div class="linea"></div>
                <div class="dato">
                    {{ $nombreEmpleado ?: 'Empleado' }}<br>
                    {{ $afiliadoEmpleado ?: '—' }}<br>
                    <span class="rol">Recibe</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 350);
        });
    </script>
</body>
</html>
