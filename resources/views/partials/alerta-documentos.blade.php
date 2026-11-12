{{--
    Aviso de registros sin documento firmado.
    Variables: $pendientes (colección de ['texto' => ..., 'url' => ...]), $singular, $plural (ej. 'traslado', 'traslados'), $id
--}}
@if($pendientes->isNotEmpty())
    <div class="alert alert-warning mb-4" role="alert">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <i class="bi bi-exclamation-triangle me-2"></i>
                Falta subir el documento firmado de <strong>{{ $pendientes->count() }}</strong>
                {{ $pendientes->count() === 1 ? $singular : $plural }}.
            </div>
            <button class="btn btn-sm btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $id }}" aria-expanded="false" aria-controls="{{ $id }}">
                Ver cuáles
            </button>
        </div>
        <div class="collapse mt-2" id="{{ $id }}">
            <ul class="list-group list-group-flush small">
                @foreach($pendientes as $p)
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                        <span>{{ $p['texto'] }}</span>
                        @if(\App\Support\Permisos::puede($permiso . '.crear'))
                            <a href="{{ $p['url'] }}#documento-firmado" class="btn btn-sm btn-warning">
                                <i class="bi bi-upload me-1"></i> Subir documento
                            </a>
                        @else
                            <a href="{{ $p['url'] }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-eye me-1"></i> Ver</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
