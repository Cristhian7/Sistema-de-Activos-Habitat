<?php

namespace App\Http\Controllers\Baja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Baja\Baja;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;
use App\Models\Estado\Estado;
use App\Models\Traslado\Traslado;

class BajaController extends Controller
{
    // Coordinador de informática que firma las bajas (se busca por apellido)
    private const COORDINADOR_APELLIDO = 'Dardon';
    private const COORDINADOR_NOMBRE_RESPALDO = 'Luis Dardon';

    // Relaciones que necesitan las vistas de detalle e impresión
    private const RELACIONES = [
        'equipo.tipoEquipo',
        'equipo.marca',
        'equipo.modelo',
        'equipo.estado',
        'empleado.puesto',
        'empleado.afiliado',
        'tecnico.puesto',
        'tecnico.afiliado',
    ];

    // Listado de bajas
    public function index()
    {
        $bajas = Baja::with(['equipo.tipoEquipo', 'equipo.marca', 'equipo.modelo', 'empleado', 'tecnico'])
            ->orderByDesc('id_baja')
            ->get();

        return view('baja.index', compact('bajas'));
    }

    // Formulario para dar de baja un equipo
    public function create()
    {
        return view('baja.create');
    }

    // Guardar la baja y cambiar el estado del equipo a "Baja"
    public function store(Request $request)
    {
        // Los años y meses de uso NO se piden: se calculan con la fecha de compra del equipo
        $request->validate([
            'codigo_equipo'      => 'required|string',
            'fecha_baja'         => 'required|date',
            'problema'           => 'required|string|max:100',
            'detalle'            => 'required|string|max:1000',
            'necesita_reemplazo' => 'required|in:SI,NO',
        ], [
            'codigo_equipo.required'      => 'El código del equipo es obligatorio.',
            'fecha_baja.required'         => 'La fecha de la baja es obligatoria.',
            'problema.required'           => 'Debe indicar el problema del equipo.',
            'problema.max'                => 'El problema no puede superar los 100 caracteres.',
            'detalle.required'            => 'Debe detallar el motivo de la baja.',
            'detalle.max'                 => 'El detalle no puede superar los 1000 caracteres.',
            'necesita_reemplazo.required' => 'Indique si el equipo necesita reemplazo.',
            'necesita_reemplazo.in'       => 'El valor de reemplazo no es válido.',
        ]);

        // Acepta el código completo (CE-04728) o solo los últimos dígitos (4728)
        $codigo = trim($request->codigo_equipo);
        $equipo = Equipo::where('codigo_contable', $codigo)->first()
            ?? Equipo::where('codigo_contable', 'LIKE', '%' . $codigo)->first();

        if (!$equipo) {
            return back()->withInput()->withErrors([
                'codigo_equipo' => 'No existe un equipo con ese código contable.',
            ]);
        }

        // El empleado es quien tiene el equipo asignado (su último traslado)
        $traslado = Traslado::where('equipo_id_equipo', $equipo->id_equipo)
            ->orderByDesc('id_traslado')
            ->first();

        if (!$traslado) {
            return back()->withInput()->withErrors([
                'codigo_equipo' => 'El equipo con código ' . $equipo->codigo_contable .
                    ' no está asignado a ningún empleado. Registre primero su traslado.',
            ]);
        }

        $empleado = Empleado::find($traslado->empleado_id_empleado);

        $resultado = DB::transaction(function () use ($request, $equipo, $empleado) {
            // Se vuelve a leer el equipo bloqueado para evitar dos bajas simultáneas
            $equipoActual = Equipo::where('id_equipo', $equipo->id_equipo)->lockForUpdate()->first();

            // Solo se puede dar de baja un equipo que esté Activo (según la tabla estado)
            if (!Estado::esActivo($equipoActual->estado_id_estado)) {
                return false;
            }

            // Tiempo de uso: fecha de compra vs. fecha actual (no se toma del formulario)
            $tiempoUso = $this->calcularTiempoUso($equipoActual->fecha_compra);

            Baja::create([
                'equipo_id_equipo'     => $equipoActual->id_equipo,
                'empleado_id_empleado' => $empleado->id_empleado,
                // Técnico que realiza la baja = empleado del usuario logueado
                'tecnico_id_empleado'  => Auth::user()?->empleado_id_empleado,
                'problema'             => $request->problema,
                'detalle'              => $request->detalle,
                // Años completos y meses restantes entre la fecha de compra y hoy
                'anos_uso'             => $tiempoUso['anos'],
                'mes_uso'              => $tiempoUso['meses'],
                'necesita_reemplazo'   => $request->necesita_reemplazo,
                'fecha_baja'           => $request->fecha_baja,
            ]);

            // El equipo pasa de Activo a Baja (id leído de la tabla estado)
            $equipoActual->update(['estado_id_estado' => Estado::idBaja()]);

            return true;
        });

        if (!$resultado) {
            $estadoActual = Equipo::with('estado')->find($equipo->id_equipo);

            $mensaje = Estado::esBaja($estadoActual?->estado_id_estado)
                ? 'El equipo con código ' . $equipo->codigo_contable . ' ya fue dado de baja.'
                : 'El equipo con código ' . $equipo->codigo_contable . ' está en estado "' .
                    ($estadoActual?->estado?->nombre_estado ?? 'desconocido') .
                    '"; solo se puede dar de baja un equipo en estado "' . Estado::ACTIVO . '".';

            return back()->withInput()->withErrors(['codigo_equipo' => $mensaje]);
        }

        return redirect()->route('bajas.index')
            ->with('success', 'Baja registrada. El equipo ' . $equipo->codigo_contable . ' cambió su estado a Baja.');
    }

    // Ver el detalle de una baja
    public function show($id)
    {
        $baja = Baja::with(self::RELACIONES)->findOrFail($id);

        return view('baja.show', compact('baja'));
    }

    // Vista de impresión de la baja (firmas: técnico y coordinador de informática)
    public function imprimir($id)
    {
        $baja = Baja::with(self::RELACIONES)->findOrFail($id);

        // Técnico guardado al registrar la baja. Si es un registro anterior
        // sin técnico, se usa el empleado del usuario logueado.
        $tecnico = $baja->tecnico;

        if (!$tecnico) {
            $usuario = Auth::user();
            $usuario?->loadMissing(['empleado.afiliado']);
            $tecnico = $usuario?->empleado;
        }

        // Coordinador de informática (Luis Dardon)
        $coordinador = Empleado::where('apellido_empleado', 'like', '%' . self::COORDINADOR_APELLIDO . '%')->first();
        $nombreCoordinador = $coordinador
            ? trim($coordinador->nombre_empleado . ' ' . $coordinador->apellido_empleado)
            : self::COORDINADOR_NOMBRE_RESPALDO;

        return view('baja.print', compact('baja', 'tecnico', 'nombreCoordinador'));
    }

    // Años completos y meses restantes (0 a 11) entre la fecha de compra y la fecha actual
    private function calcularTiempoUso($fechaCompra): array
    {
        if (empty($fechaCompra)) {
            return ['anos' => 0, 'meses' => 0];
        }

        $diferencia = (new \DateTime($fechaCompra))->diff(new \DateTime('today'));

        // Si la fecha de compra es futura, no hay tiempo de uso
        if ($diferencia->invert) {
            return ['anos' => 0, 'meses' => 0];
        }

        return ['anos' => (int) $diferencia->y, 'meses' => (int) $diferencia->m];
    }
}
