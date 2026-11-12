<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarjeta de Responsabilidad - {{ trim($empleado->nombre_empleado . ' ' . $empleado->apellido_empleado) }}</title>
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
            min-height: 1056px;        /* como mínimo una carta; crece si hay muchos equipos */
            margin: 20px auto;
            background: #fff;
            padding: 44px 56px 52px;
            box-shadow: 0 2px 14px rgba(0,0,0,.15);
            position: relative;
            overflow: hidden;          /* para que las barras lleguen al borde */
            display: flex;
            flex-direction: column;
        }
        /* Zona central que crece y empuja las firmas al pie */
        .centro { flex: 1 1 auto; }

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
            margin: 6px 0 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
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
        .logo-fallback { font-family: "Cambria", "Georgia", serif; font-weight: 800; line-height: 1.05; }
        .logo-fallback .l1 { color: #0a8a3f; font-size: 34px; }
        .logo-fallback .l2 { color: #111; font-size: 16px; }
        .logo-fallback .l3 { color: #0a8a3f; font-size: 15px; }

        .titulo { position: relative; text-align: center; z-index: 2; width: 100%; }
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
            font-size: 16px;
            font-weight: 700;
            margin: 4px 0 0;
            letter-spacing: 3px;
            color: #0a8a3f;
            padding-left: 90px;        /* se centra en el espacio libre a la derecha del logo */
        }

        .texto { font-size: 14px; margin: 10px 0; line-height: 1.5; }
        .texto strong { font-weight: 700; }

        .subtitulo {
            font-family: "Cambria", "Georgia", serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #14532d;
            margin: 20px 0 6px;
        }

        /* Tablas */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        table td, table th { border: 1px solid #000; padding: 6px 9px; vertical-align: top; }
        table.datos td.etq {
            background: #c9d7ec;
            font-weight: 700;
            text-transform: uppercase;
            width: 18%;
            white-space: nowrap;
        }
        table.datos td.val { width: 32%; }

        table.equipos th {
            background: #c9d7ec;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            text-align: center;
            vertical-align: middle;
        }
        table.equipos td { text-align: center; vertical-align: middle; }
        table.equipos td.codigo { font-weight: 700; }

        .vacio {
            border: 1px dashed #6b7280;
            padding: 18px;
            text-align: center;
            font-size: 14px;
            color: #374151;
        }

        .clausula { font-size: 12px; color: #374151; margin-top: 14px; line-height: 1.5; }

        /* Firmas (siempre al pie de la hoja) */
        .firmas {
            display: flex;
            justify-content: center;
            gap: 110px;
            flex: 0 0 auto;
            margin-top: 50px;
            padding-bottom: 28px;
        }
        .firma { flex: 0 0 260px; text-align: center; }
        .firma .linea { border-bottom: 1px solid #000; width: 220px; margin: 0 auto 8px; height: 1px; }
        .firma .dato { font-weight: 700; font-size: 14px; line-height: 1.4; }
        .firma .dato .rol {
            display: inline-block;
            margin-top: 4px;
            font-family: "Cambria", "Georgia", serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0a8a3f;
            font-weight: 700;
        }

        .barra-acciones { width: 816px; margin: 20px auto 0; text-align: right; font-family: Arial, sans-serif; }
        .btn { display: inline-block; font-size: 14px; padding: 8px 18px; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; }
        .btn-print { background: #0d6efd; color: #fff; }
        .btn-back { background: #6b7280; color: #fff; margin-left: 8px; }

        @media print {
            html, body { height: auto; margin: 0; }
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .hoja {
                box-shadow: none;
                margin: 0;
                width: 21.59cm;            /* ancho carta */
                min-height: 27.94cm;       /* alto carta */
                padding: 1.1cm 1.3cm 1.2cm;
            }
            table tr { page-break-inside: avoid; }
            .barra-acciones { display: none; }
            /* Sin margen de página para que las barras verdes lleguen al borde del papel */
            @page { size: letter; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="barra-acciones">
        <button class="btn btn-print" onclick="window.print()">Imprimir</button>
        <a class="btn btn-back" href="{{ route('tarjetas.show', $empleado->id_empleado) }}">Volver</a>
    </div>

    @php
        $logoPath = public_path('img/logo-habitat.png');
        $logoExiste = file_exists($logoPath);

        $nombreEmpleado = trim($empleado->nombre_empleado . ' ' . $empleado->apellido_empleado);
        $puestoEmpleado = $empleado->puesto->nombre_puesto ?? '';
        $afiliadoEmpleado = $empleado->afiliado->nombre_afiliado ?? '';

        $nombreTecnico = trim(($tecnico->nombre_empleado ?? '') . ' ' . ($tecnico->apellido_empleado ?? ''));
        $afiliadoTecnico = $tecnico->afiliado->nombre_afiliado ?? '';

        $dinero = fn ($v) => $v !== null ? 'Q ' . number_format((float) $v, 2) : 'N/A';
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
                <h2>TARJETA DE RESPONSABILIDAD</h2>
            </div>
        </div>

        <div class="centro">
            <p class="texto">Por este medio se hace constar que el colaborador indicado a continuación tiene bajo su responsabilidad los equipos que se detallan.</p>

            <!-- Datos del colaborador -->
            <div class="subtitulo">Colaborador</div>
            <table class="datos">
                <tr>
                    <td class="etq">Nombre:</td>
                    <td class="val">{{ $nombreEmpleado }}</td>
                    <td class="etq">Código L4:</td>
                    <td class="val">{{ $empleado->cod_l4 ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="etq">Puesto:</td>
                    <td class="val">{{ $puestoEmpleado ?: 'N/A' }}</td>
                    <td class="etq">Afiliado:</td>
                    <td class="val">{{ $afiliadoEmpleado ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="etq">Fecha de emisión:</td>
                    <td class="val" colspan="3">{{ now()->format('d/m/Y') }}</td>
                </tr>
            </table>

            <!-- Equipos bajo su responsabilidad -->
            <div class="subtitulo">Equipos bajo su responsabilidad ({{ $aCargo->count() }})</div>
            @if($aCargo->isEmpty())
                <div class="vacio">El colaborador no tiene equipos bajo su responsabilidad actualmente.</div>
            @else
                <table class="equipos">
                    <thead>
                        <tr>
                            <th style="width: 4%;">No.</th>
                            <th>Código Contable</th>
                            <th>Equipo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Service Tag</th>
                            <th>Entregado el</th>
                            <th>Valor en Libros</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aCargo as $i => $traslado)
                            @php $equipo = $traslado->equipo; @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="codigo">{{ $equipo->codigo_contable }}</td>
                                <td>{{ $equipo->tipoEquipo->nombre_tipo ?? 'N/A' }}</td>
                                <td>{{ $equipo->marca->nombre_marca ?? 'N/A' }}</td>
                                <td>{{ $equipo->modelo->nombre_modelo ?? 'N/A' }}</td>
                                <td>{{ $equipo->st ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($traslado->fecha_envio)->format('d/m/Y') }}</td>
                                <td>{{ $dinero($equipo->valor_libros) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p class="clausula">El colaborador es responsable del uso y cuidado de los equipos listados y deberá devolverlos cuando se le solicite.</p>
            @endif
        </div>

        <!-- Firmas -->
        <div class="firmas">
            <div class="firma">
                <div class="linea"></div>
                <div class="dato">
                    {{ $nombreEmpleado }}<br>
                    {{ $puestoEmpleado ?: '—' }}<br>
                    <span class="rol">Responsable</span>
                </div>
            </div>
            <div class="firma">
                <div class="linea"></div>
                <div class="dato">
                    {{ $nombreTecnico ?: 'Técnico' }}<br>
                    {{ $afiliadoTecnico ?: '—' }}<br>
                    <span class="rol">Emite</span>
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
