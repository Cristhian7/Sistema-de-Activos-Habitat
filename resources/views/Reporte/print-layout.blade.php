<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }} - Reporte</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: "Arial", "Helvetica", sans-serif;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            background: #e5e7eb;
            font-size: 12px;
        }
        .hoja {
            width: 1056px;             /* carta horizontal a 96dpi */
            min-height: 816px;
            margin: 20px auto;
            background: #fff;
            padding: 0 40px 28px;
            box-shadow: 0 2px 14px rgba(0,0,0,.15);
        }

        /* Barras verdes, arriba y abajo del reporte */
        .barra {
            height: 12px;
            margin: 0 -40px;
            background: linear-gradient(90deg, #0a8a3f 0%, #35b55f 55%, #7bd196 100%);
        }
        .barra.abajo { margin-top: 24px; }

        .encabezado { display: flex; align-items: center; gap: 24px; padding: 18px 0 10px; }
        .encabezado .logo { width: 170px; flex: 0 0 auto; }
        .encabezado .logo img { max-width: 100%; height: auto; }
        .logo-fallback { font-family: "Cambria", "Georgia", serif; font-weight: 800; line-height: 1.05; }
        .logo-fallback .l1 { color: #0a8a3f; font-size: 30px; }
        .logo-fallback .l2 { color: #111; font-size: 14px; }
        .logo-fallback .l3 { color: #0a8a3f; font-size: 13px; }
        .titulo { flex: 1 1 auto; text-align: center; padding-right: 170px; }
        .titulo h1 { font-family: "Cambria", "Georgia", serif; font-size: 24px; margin: 0; letter-spacing: 3px; color: #14532d; }
        .titulo h2 { font-family: "Cambria", "Georgia", serif; font-size: 16px; margin: 4px 0 0; letter-spacing: 2px; color: #0a8a3f; text-transform: uppercase; }

        .meta { display: flex; justify-content: space-between; gap: 20px; font-size: 12px; margin: 6px 0 12px; border-top: 1px solid #000; padding-top: 8px; }
        .meta .filtros span { display: inline-block; margin-right: 16px; }
        .meta .filtros b { color: #14532d; }
        .meta .emision { text-align: right; white-space: nowrap; }

        /* Resúmenes y KPIs */
        .kpis { display: flex; gap: 12px; margin: 6px 0 14px; }
        .kpi { border: 1px solid #000; padding: 6px 14px; min-width: 150px; }
        .kpi .num { font-size: 22px; font-weight: 700; color: #14532d; line-height: 1.1; }
        .kpi .etq { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }

        h3.seccion, .seccion-titulo {
            font-family: "Cambria", "Georgia", serif;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #14532d;
            margin: 14px 0 6px;
        }

        .resumenes { display: flex; gap: 24px; align-items: flex-start; }
        .resumenes > div { flex: 1 1 0; }

        table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
        table th, table td { border: 1px solid #000; padding: 4px 7px; vertical-align: middle; text-align: left; }
        table thead th { background: #c9d7ec; text-transform: uppercase; font-size: 10.5px; }
        table thead { display: table-header-group; }   /* el encabezado se repite en cada hoja */
        table tr { page-break-inside: avoid; }
        .text-end { text-align: right !important; }
        .text-center { text-align: center !important; }
        .fw-semibold, .fw-bold { font-weight: 700; }
        .text-muted { color: #4b5563; }

        /* En papel, las insignias y botones de pantalla se ven como texto */
        .badge { font-weight: 700; }
        .no-print { display: none !important; }

        .barra-acciones { width: 1056px; margin: 20px auto 0; text-align: right; font-family: Arial, sans-serif; }
        .btn { display: inline-block; font-size: 14px; padding: 8px 18px; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; }
        .btn-print { background: #0d6efd; color: #fff; }
        .btn-back { background: #6b7280; color: #fff; margin-left: 8px; }

        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .hoja { box-shadow: none; margin: 0; width: auto; min-height: 0; padding: 0; }
            .barra { margin: 0; }
            .barra-acciones { display: none; }
            @page { size: letter landscape; margin: 1cm; }
        }
    </style>
</head>
<body>

    <div class="barra-acciones">
        <button class="btn btn-print" onclick="window.print()">Imprimir</button>
        <a class="btn btn-back" href="{{ url()->previous() }}" onclick="if (history.length > 1) { history.back(); return false; }">Volver</a>
    </div>

    @php
        $logoPath = public_path('img/logo-habitat.png');
        $logoExiste = file_exists($logoPath);
    @endphp

    <div class="hoja">
        <div class="barra"></div>

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
                <h2>Reporte: {{ $titulo }}</h2>
            </div>
        </div>

        <div class="meta">
            <div class="filtros">
                @forelse($filtrosTexto as $etiqueta => $valor)
                    <span><b>{{ $etiqueta }}:</b> {{ $valor }}</span>
                @empty
                    <span><b>Filtros:</b> ninguno (todos los registros)</span>
                @endforelse
            </div>
            <div class="emision">
                Emitido el {{ now()->format('d/m/Y H:i') }}@if($generadoPor)<br>por {{ $generadoPor }}@endif
            </div>
        </div>

        @yield('content')

        <div class="barra abajo"></div>
    </div>

    <script>
        window.addEventListener('load', function () {
            setTimeout(function () { window.print(); }, 350);
        });
    </script>
</body>
</html>
