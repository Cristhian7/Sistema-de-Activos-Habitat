{{-- Cantidades principales del reporte. $kpis = [['num' => 3, 'etq' => 'Texto', 'color' => 'danger'], ...] --}}
@if($imprimir)
    <div class="kpis">
        @foreach($kpis as $kpi)
            <div class="kpi">
                <div class="num">{{ $kpi['num'] }}</div>
                <div class="etq">{{ $kpi['etq'] }}</div>
            </div>
        @endforeach
    </div>
@else
    <div class="row g-3 mb-4">
        @foreach($kpis as $kpi)
            <div class="col-6 col-lg-3">
                <div class="card h-100 border-start border-4 border-{{ $kpi['color'] ?? 'primary' }}">
                    <div class="card-body py-3">
                        <div class="fs-2 fw-bold lh-1">{{ $kpi['num'] }}</div>
                        <div class="text-muted small mt-1">{{ $kpi['etq'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
