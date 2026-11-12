<?php

namespace App\Http\Controllers\Traslado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traslado\Traslado;
use App\Models\Baja\Baja;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;
use App\Models\Estado\Estado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrasladoController extends Controller
{
    public function index(Request $request)
    {
        $traslados = Traslado::with(['equipo.estado', 'empleado', 'tecnico', 'tecnicoDevolucion'])->get();

        // Fecha en que se dio de baja cada equipo (si hay registro de baja), para la columna Situación
        $bajasPorEquipo = Baja::orderBy('fecha_baja')->get()->groupBy('equipo_id_equipo')->map->last();

        return view('traslado.index', compact('traslados', 'bajasPorEquipo'));
    }

    public function create()
    {
        $equipos = Equipo::all();
        $empleados = Empleado::all();

        //$tipos = TipoEquipo::all();
        //$marcas = Marca::all();
        //$modelos = Modelo::all();
        //$estados = Estado::all();

        return view('traslado.create', compact('equipos', 'empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_equipo' => 'required|exists:equipo,codigo_contable', // Valida que el código del equipo exista
            'cod_l4' => 'required|exists:empleado,cod_l4',               // Valida que el código L4 del empleado exista
            'fecha_envio' => 'required|date',
            'motivo_traslado' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ], [
            'codigo_equipo.required' => 'Debe ingresar el código del equipo.',
            'codigo_equipo.exists' => 'No existe un equipo con ese código contable.',
            'cod_l4.required' => 'Debe ingresar el código L4 del empleado.',
            'cod_l4.exists' => 'No existe un empleado con ese código L4.',
            'fecha_envio.required' => 'Debe ingresar la fecha de envío.',
            'motivo_traslado.required' => 'Debe ingresar el motivo del traslado.',
            'descripcion.required' => 'Debe ingresar la descripción del traslado.',
        ]);

        $equipo = Equipo::where('codigo_contable', $request->codigo_equipo)->first();
        $empleado = Empleado::where('cod_l4', $request->cod_l4)->first();

        // Regla 0: solo se pueden asignar equipos en estado Activo (según la tabla estado).
        if (!Estado::esActivo($equipo->estado_id_estado)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'codigo_equipo' => 'El equipo con código ' . $equipo->codigo_contable .
                        ' está en estado "' . ($equipo->estado?->nombre_estado ?? 'desconocido') .
                        '" y no se puede asignar a un empleado.',
                ]);
        }

        $nombreEmpleado = trim(($empleado->nombre_empleado ?? '') . ' ' . ($empleado->apellido_empleado ?? ''));

        // Regla 1: si el equipo ya está asignado a OTRO empleado, se le quita (queda registrada su devolución).
        // Si ya es de este mismo empleado, no tiene sentido volver a asignarlo.
        $asignacionActual = Traslado::vigenteDelEquipo($equipo->id_equipo);

        if ($asignacionActual && (int) $asignacionActual->empleado_id_empleado === (int) $empleado->id_empleado) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'codigo_equipo' => 'El equipo ' . $equipo->codigo_contable . ' ya está asignado a ' . $nombreEmpleado . '.',
                ]);
        }

        // Traslados del equipo que siguen abiertos (incluye los de empleados ya dados de baja)
        $trasladosAbiertos = Traslado::vigentes()
            ->where('equipo_id_equipo', $equipo->id_equipo)
            ->with('empleado')
            ->get();

        $ultimoEnvio = $trasladosAbiertos->max('fecha_envio');

        if ($ultimoEnvio && Carbon::parse($request->fecha_envio)->lt(Carbon::parse($ultimoEnvio))) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'fecha_envio' => 'La fecha de envío no puede ser anterior a la del traslado actual del equipo (' .
                        Carbon::parse($ultimoEnvio)->format('d/m/Y') . ').',
                ]);
        }

        // Regla 2: un empleado solo puede tener un equipo asignado a la vez.
        // Para darle otro, primero se le devuelve (quita) el que tiene.
        $equipoDelEmpleado = Traslado::vigenteDelEmpleado($empleado->id_empleado);

        if ($equipoDelEmpleado) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'cod_l4' => 'El empleado ' . $nombreEmpleado . ' ya tiene asignado el equipo ' .
                        ($equipoDelEmpleado->equipo->codigo_contable ?? '') .
                        ' y no puede tener otro. Primero devuelva ese equipo con el botón "Devolver" del listado de traslados.',
                ]);
        }

        $tecnico = Auth::user()?->empleado_id_empleado;

        DB::transaction(function () use ($request, $equipo, $empleado, $trasladosAbiertos, $nombreEmpleado, $tecnico) {
            // Al empleado anterior se le quita el equipo: queda su devolución en la bitácora.
            // Si ese empleado ya estaba dado de baja, la causa es su baja y no una reasignación.
            foreach ($trasladosAbiertos as $abierto) {
                $empleadoDeBaja = Estado::esBaja($abierto->empleado->estado_id_estado ?? null);

                $abierto->update([
                    'fecha_devolucion' => $request->fecha_envio,
                    'motivo_devolucion' => $empleadoDeBaja ? 'Empleado dado de baja' : 'Reasignado a ' . $nombreEmpleado,
                    'causa_devolucion' => $empleadoDeBaja ? Traslado::CAUSA_BAJA_EMPLEADO : Traslado::CAUSA_REASIGNACION,
                    'tecnico_devolucion_id_empleado' => $tecnico,
                ]);
            }

            // Crear el traslado asignando los IDs encontrados
            Traslado::create([
                'equipo_id_equipo' => $equipo->id_equipo,
                'empleado_id_empleado' => $empleado->id_empleado,
                // Técnico que realiza el traslado = empleado del usuario logueado
                'tecnico_id_empleado' => $tecnico,
                'fecha_envio' => $request->fecha_envio,
                'motivo_traslado' => $request->motivo_traslado,
                'descripcion' => $request->descripcion,
            ]);
        });

        $mensaje = 'Traslado registrado exitosamente.';

        if ($asignacionActual) {
            $anterior = $asignacionActual->empleado;
            $mensaje .= ' El equipo se quitó a ' .
                trim(($anterior->nombre_empleado ?? '') . ' ' . ($anterior->apellido_empleado ?? '')) .
                ' y quedó registrada su devolución.';
        }

        return redirect()->route('traslados.index')->with('success', $mensaje);
    }

    // Ver el detalle de un traslado
    public function show($id)
    {
        $traslado = Traslado::with([
            'equipo.tipoEquipo',
            'equipo.modelo',
            'equipo.marca',
            'equipo.estado',
            'empleado.puesto',
            'empleado.afiliado',
            'tecnico.puesto',
            'tecnico.afiliado',
        ])->findOrFail($id);

        return view('traslado.show', compact('traslado'));
    }

    // Vista de impresión del traslado (incluye firmas)
    public function imprimir($id)
    {
        $traslado = Traslado::with([
            'equipo.tipoEquipo',
            'equipo.modelo',
            'equipo.marca',
            'equipo.estado',
            'empleado.puesto',
            'empleado.afiliado',
            'tecnico.puesto',
            'tecnico.afiliado',
        ])->findOrFail($id);

        // Técnico guardado al registrar el traslado. Si es un registro anterior
        // sin técnico, se usa el empleado del usuario logueado.
        $tecnico = $traslado->tecnico;

        if (!$tecnico) {
            $usuario = Auth::user();
            $usuario?->loadMissing(['empleado.afiliado']);
            $tecnico = $usuario?->empleado;
        }

        return view('traslado.print', compact('traslado', 'tecnico'));
    }

    // Formulario de edición de traslado
    public function edit($id)
    {
        $traslado = Traslado::with(['equipo', 'empleado'])->findOrFail($id);

        // Un traslado finalizado es parte del historial y no se edita
        if (!$traslado->vigente) {
            return redirect()->route('traslados.index')
                ->with('error', 'Ese traslado ya fue finalizado (el equipo fue devuelto) y no se puede editar.');
        }

        return view('traslado.edit', compact('traslado'));
    }

    // Devolver el equipo: finaliza el traslado y el equipo queda disponible para asignar
    public function devolver(Request $request, $id)
    {
        $traslado = Traslado::with(['equipo', 'empleado'])->findOrFail($id);

        if (!$traslado->vigente) {
            return redirect()->route('traslados.index')
                ->with('error', 'Ese traslado ya fue finalizado anteriormente.');
        }

        $request->validate([
            'fecha_devolucion' => [
                'required', 'date',
                'after_or_equal:' . Carbon::parse($traslado->fecha_envio)->format('Y-m-d'),
                'before_or_equal:today',
            ],
            'motivo_devolucion' => 'required|string|max:255',
        ], [
            'fecha_devolucion.required' => 'Debe ingresar la fecha de devolución.',
            'fecha_devolucion.date' => 'La fecha de devolución no es válida.',
            'fecha_devolucion.after_or_equal' => 'La fecha de devolución no puede ser anterior a la fecha de envío (' .
                Carbon::parse($traslado->fecha_envio)->format('d/m/Y') . ').',
            'fecha_devolucion.before_or_equal' => 'La fecha de devolución no puede ser futura.',
            'motivo_devolucion.required' => 'Debe indicar el motivo de la devolución.',
            'motivo_devolucion.max' => 'El motivo no puede superar los 255 caracteres.',
        ]);

        // Si el empleado ya estaba dado de baja, la causa es su baja; si no, una devolución normal
        $empleadoDeBaja = Estado::esBaja($traslado->empleado->estado_id_estado ?? null);

        $traslado->update([
            'fecha_devolucion' => $request->fecha_devolucion,
            'motivo_devolucion' => $request->motivo_devolucion,
            'causa_devolucion' => $empleadoDeBaja ? Traslado::CAUSA_BAJA_EMPLEADO : Traslado::CAUSA_DEVOLUCION,
            'tecnico_devolucion_id_empleado' => Auth::user()?->empleado_id_empleado,
        ]);

        $nombre = trim(($traslado->empleado->nombre_empleado ?? '') . ' ' . ($traslado->empleado->apellido_empleado ?? ''));

        return redirect()->route('traslados.index')->with(
            'success',
            'Equipo ' . ($traslado->equipo->codigo_contable ?? '') . ' devuelto por ' . $nombre . '. Ahora está disponible para asignar.'
        );
    }

    // Actualizar traslado
    public function update(Request $request, $id)
    {
        $traslado = Traslado::findOrFail($id);

        if (!$traslado->vigente) {
            return redirect()->route('traslados.index')
                ->with('error', 'Ese traslado ya fue finalizado (el equipo fue devuelto) y no se puede editar.');
        }

        $request->validate([
            'codigo_equipo' => 'required|exists:equipo,codigo_contable',
            'cod_l4' => 'required|exists:empleado,cod_l4',
            'fecha_envio' => 'required|date',
            'motivo_traslado' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ], [
            'codigo_equipo.required' => 'Debe ingresar el código del equipo.',
            'codigo_equipo.exists' => 'No existe un equipo con ese código contable.',
            'cod_l4.required' => 'Debe ingresar el código L4 del empleado.',
            'cod_l4.exists' => 'No existe un empleado con ese código L4.',
            'fecha_envio.required' => 'Debe ingresar la fecha de envío.',
            'motivo_traslado.required' => 'Debe ingresar el motivo del traslado.',
            'descripcion.required' => 'Debe ingresar la descripción del traslado.',
        ]);

        $equipo = Equipo::where('codigo_contable', $request->codigo_equipo)->first();
        $empleado = Empleado::where('cod_l4', $request->cod_l4)->first();

        // Regla 0: solo se pueden asignar equipos en estado Activo (salvo que este mismo
        // traslado ya lo tuviera, para no bloquear la edición de datos antiguos).
        if (!Estado::esActivo($equipo->estado_id_estado) && (int) $equipo->id_equipo !== (int) $traslado->equipo_id_equipo) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'codigo_equipo' => 'El equipo con código ' . $equipo->codigo_contable .
                        ' está en estado "' . ($equipo->estado?->nombre_estado ?? 'desconocido') .
                        '" y no se puede asignar a un empleado.',
                ]);
        }

        // Regla 1: el equipo no puede estar vigente en OTRO traslado.
        // Para pasarlo a otro empleado se registra un traslado nuevo (se le quita al anterior).
        $otraAsignacion = Traslado::vigenteDelEquipo($equipo->id_equipo, $traslado->id_traslado);

        if ($otraAsignacion) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'codigo_equipo' => 'El equipo ' . $equipo->codigo_contable .
                        ' ya está asignado a otro empleado. Para pasarlo a un empleado distinto registre un traslado nuevo.',
                ]);
        }

        // Regla 2: el empleado no puede tener otro equipo vigente en OTRO traslado.
        $otroEquipoDelEmpleado = Traslado::vigenteDelEmpleado($empleado->id_empleado, $traslado->id_traslado);

        if ($otroEquipoDelEmpleado) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'cod_l4' => 'El empleado ' . trim(($empleado->nombre_empleado ?? '') . ' ' . ($empleado->apellido_empleado ?? '')) .
                        ' ya tiene asignado el equipo ' . ($otroEquipoDelEmpleado->equipo->codigo_contable ?? '') .
                        ' y no puede tener otro. Primero devuelva ese equipo con el botón "Devolver" del listado de traslados.',
                ]);
        }

        $traslado->update([
            'equipo_id_equipo' => $equipo->id_equipo,
            'empleado_id_empleado' => $empleado->id_empleado,
            'fecha_envio' => $request->fecha_envio,
            'motivo_traslado' => $request->motivo_traslado,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('traslados.index')->with('success', 'Traslado actualizado exitosamente.');
    }

    // Eliminar traslado
    public function destroy($id)
    {
        $traslado = Traslado::findOrFail($id);
        $documento = $traslado->documento_firmado;
        $traslado->delete();

        // Si se elimina el traslado, su documento firmado ya no tiene a qué pertenecer
        \App\Support\DocumentoFirmado::borrarArchivo($documento);
        \App\Support\Notificaciones::eliminarDe('traslado', $id);

        return redirect()->route('traslados.index')->with('success', 'Traslado eliminado exitosamente.');
    }

    // Método AJAX para buscar equipo por código contable
    public function getEquipoByCodigo(Request $request)
    {
        try {
            $codigo = trim($request->get('codigo'));
            if (empty($codigo)) {
                return response()->json(['success' => false]);
            }

            // Búsqueda por prioridad:
            // 1) Coincidencia exacta con lo ingresado (ej. "CE-04728")
            // 2) Coincidencia por el final del código (ej. "4728" -> "...4728")
            // 3) Coincidencia parcial en cualquier parte
            $equipo = Equipo::with(['tipoEquipo', 'modelo', 'marca', 'estado'])
                        ->where('codigo_contable', $codigo)
                        ->first();

            if (!$equipo) {
                $equipo = Equipo::with(['tipoEquipo', 'modelo', 'marca', 'estado'])
                            ->where('codigo_contable', 'LIKE', "%{$codigo}")
                            ->first();
            }

            if (!$equipo) {
                $equipo = Equipo::with(['tipoEquipo', 'modelo', 'marca', 'estado'])
                            ->where('codigo_contable', 'LIKE', "%{$codigo}%")
                            ->first();
            }

                            if ($equipo) {
                                $nombreMarca = $equipo->marca?->nombre_marca ?? 'N/A';
                                $nombreEstado = $equipo->estado?->nombre_estado ?? 'N/A';
                                $valorLibros = $equipo->valor_libros ?? 'N/A';

                                // Empleado que tiene asignado el equipo (último traslado), para precargar la baja
                                $asignado = Traslado::with(['empleado.puesto', 'empleado.afiliado'])
                                    ->where('equipo_id_equipo', $equipo->id_equipo)
                                    ->orderByDesc('id_traslado')
                                    ->first();

                                // A quién está asignado el equipo HOY (traslado vigente); null = no está asignado.
                                // Un equipo dado de baja no cuenta como asignado aunque su traslado siga abierto.
                                $enBaja = Estado::esBaja($equipo->estado_id_estado);
                                $vigente = $enBaja ? null : Traslado::vigenteDelEquipo($equipo->id_equipo);
                                $vigente?->loadMissing('empleado');

                                // Convertimos las imágenes binarias (BLOB) a base64 para enviarlas al navegador
                                $imagenEquipo = $equipo->imagen_equipo
                                    ? 'data:image/jpeg;base64,' . base64_encode($equipo->imagen_equipo)
                                    : null;
                                $imagenCodigo = $equipo->imagen_codigo
                                    ? 'data:image/jpeg;base64,' . base64_encode($equipo->imagen_codigo)
                                    : null;

                                return response()->json([
                                    'success' => true,
                                    'codigo_contable' => $equipo->codigo_contable,
                                    'imagen_equipo' => $imagenEquipo,
                                    'imagen_codigo' => $imagenCodigo,
                                    'nombre_equipo' => $equipo->nombre_equipo ?? 'N/A',
                                    'tipo' => $equipo->tipoEquipo?->nombre_tipo ?? 'N/A',
                                    'marca' => $nombreMarca,
                                    'modelo' => $equipo->modelo?->nombre_modelo ?? 'N/A',
                                    'procesador' => $equipo->procesador ?? 'N/A',
                                    'ram' => $equipo->ram ?? 'N/A',
                                    'tipo_ram' => $equipo->tipo_ram ?? 'N/A',
                                    'ssd' => $equipo->ssd ?? 'N/A',
                                    'st' => $equipo->st ?? 'N/A',
                                    'ex' => $equipo->ex ?? 'N/A',
                                    'fecha_compra' => $equipo->fecha_compra ?? 'N/A',
                                    'precio_compra' => $equipo->precio_compra ?? 'N/A',
                                    'balor_libros' => $valorLibros,
                                    'estado' => $nombreEstado,
                                    'asignado_vigente' => $vigente !== null,
                                    // Disponible = no está de baja y nadie lo tiene asignado
                                    'disponible' => !$enBaja && $vigente === null,
                                    'asignado_a' => $vigente
                                        ? trim(($vigente->empleado->nombre_empleado ?? '') . ' ' . ($vigente->empleado->apellido_empleado ?? ''))
                                        : null,
                                    'empleado_cod_l4' => $asignado?->empleado?->cod_l4,
                                    'empleado_nombre' => $asignado?->empleado?->nombre_empleado,
                                    'empleado_apellido' => $asignado?->empleado?->apellido_empleado,
                                    'empleado_puesto' => $asignado?->empleado?->puesto?->nombre_puesto,
                                    'empleado_afiliado' => $asignado?->empleado?->afiliado?->nombre_afiliado,
                                    'notas_adicionales' => $equipo->notas_adicionales ?? 'Ninguna',
                                    ]);
                        }

                        // Si no lo encuentra, traemos una muestra rápida de la BD para ver cómo están guardados
                        $muestras = Equipo::limit(5)->pluck('codigo_contable')->toArray();
                        $listado = implode(', ', $muestras);

                        return response()->json([
                            'success' => false,'error' => "No se encontró '{$codigo}'. Códigos de muestra en BD: [{$listado}]"
                        ]);
                        
                        } catch (\Exception $e) {
                            return response()->json(['success' => false,'error' => $e->getMessage()], 500);
                        }
}
// Método AJAX para buscar empleado por código L4
    public function getEmpleadoByL4(Request $request)
    {
        $codL4 = $request->get('cod_l4');
        $empleado = Empleado::with(['puesto', 'afiliado'])
                        ->where('cod_l4', $codL4)
                        ->first();

        if ($empleado) {
            return response()->json([
                'success' => true,
                'nombre_completo' => $empleado->nombre_empleado . ' ' . $empleado->apellido_empleado,
                'puesto' => $empleado->puesto->nombre_puesto ?? 'N/A',
                'afiliado' => $empleado->afiliado->nombre_afiliado ?? 'N/A',
            ]);
        }

        return response()->json(['success' => false]);
    }
}