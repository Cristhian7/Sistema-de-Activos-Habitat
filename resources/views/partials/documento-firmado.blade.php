{{--
    Resguardo del documento firmado de un traslado o de una baja.
    Variables: $registro (Traslado o Baja), $tipo ('traslados' o 'bajas')
--}}
@php
    $permisoBase = $tipo; // traslados | bajas
    $tiene = $registro->tiene_documento;
    $puedeSubir = \App\Support\Permisos::puede($tiene ? $permisoBase . '.editar' : $permisoBase . '.crear');
    $rutaVer = route($tipo . '.documento.ver', $registro->getKey());
    $rutaSubir = route($tipo . '.documento.subir', $registro->getKey());
    $extension = $tiene ? strtolower(pathinfo($registro->documento_firmado, PATHINFO_EXTENSION)) : null;
    $etiqueta = $tipo === 'traslados' ? 'del traslado' : 'de la baja';
@endphp

<div class="card border-0 shadow-sm p-4 {{ $tiene ? '' : 'border border-warning' }}" id="documento-firmado">
    <h5 class="fw-bold {{ $tiene ? 'text-success' : 'text-warning' }} mb-3">
        <i class="bi bi-file-earmark-check me-2"></i>Documento firmado
        @if($tiene)
            <span class="badge bg-success ms-2">Cargado</span>
        @else
            <span class="badge bg-warning text-dark ms-2">Pendiente</span>
        @endif
    </h5>

    @if($errors->has('documento'))
        <div class="alert alert-danger py-2">{{ $errors->first('documento') }}</div>
    @endif

    @if($tiene)
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="text-muted small">Archivo</div>
                <div class="fw-semibold">
                    <i class="bi {{ $extension === 'pdf' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-image text-primary' }} me-1"></i>{{ $registro->documento_nombre ?? 'documento' }}
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Subido el</div>
                <div class="fw-semibold">{{ $registro->documento_fecha ? \Carbon\Carbon::parse($registro->documento_fecha)->format('d/m/Y H:i') : 'N/A' }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Subido por</div>
                <div class="fw-semibold">
                    {{ $registro->documentoCargadoPor ? $registro->documentoCargadoPor->nombre_empleado . ' ' . $registro->documentoCargadoPor->apellido_empleado : 'N/A' }}
                </div>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2 flex-wrap">
            <a href="{{ $rutaVer }}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye me-1"></i> Ver documento</a>
            <a href="{{ $rutaVer }}?descargar=1" class="btn btn-outline-secondary btn-sm"><i class="bi bi-download me-1"></i> Descargar</a>
        </div>
    @elseif(\App\Support\Permisos::puede($permisoBase . '.crear'))
        <p class="text-muted mb-2">
            Imprime el documento {{ $etiqueta }}, fírmalo y súbelo aquí como comprobante de que se realizó.
        </p>
    @else
        <p class="text-muted mb-0">Este documento firmado {{ $etiqueta }} aún no está disponible.</p>
    @endif

    @if($puedeSubir)
        <form action="{{ $rutaSubir }}" method="POST" enctype="multipart/form-data" class="mt-3">
            @csrf
            <label for="documento" class="form-label small fw-semibold mb-1">
                {{ $tiene ? 'Reemplazar el documento firmado' : 'Subir el documento firmado' }}
                <span class="text-muted fw-normal">(PDF, JPG o PNG, máximo 10 MB)</span>
            </label>
            <div class="input-group" style="max-width: 560px;">
                <input type="file" id="documento" name="documento" class="form-control @error('documento') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" required>
                <button type="submit" class="btn btn-primary" @if($tiene) onclick="return confirm('Esto reemplaza el documento firmado actual. ¿Continuar?');" @endif>
                    <i class="bi bi-upload me-1"></i> {{ $tiene ? 'Reemplazar' : 'Subir' }}
                </button>
            </div>
        </form>
    @elseif(!$tiene)
        <div class="small text-muted mt-2">Todavía no se ha subido. El documento lo sube el Administrador o el Analista.</div>
    @endif
</div>
