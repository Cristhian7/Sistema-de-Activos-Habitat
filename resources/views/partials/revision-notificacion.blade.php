{{--
    Si el usuario tiene un aviso pendiente de este traslado o baja, muestra el check de "revisado".
    Variables: $tipo ('traslado' o 'baja'), $registroId
--}}
@php $aviso = \App\Support\Notificaciones::pendienteDe($tipo, $registroId); @endphp
@if($aviso)
    <div class="alert alert-info d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4" role="alert">
        <div>
            <i class="bi bi-bell me-2"></i>{{ $aviso->mensaje }}
            <div class="small text-muted">Revisa el documento y marca el aviso como revisado.</div>
        </div>
        <form action="{{ route('notificaciones.revisar', $aviso->id_notificacion) }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check2-circle me-1"></i> Marcar como revisado</button>
        </form>
    </div>
@endif
