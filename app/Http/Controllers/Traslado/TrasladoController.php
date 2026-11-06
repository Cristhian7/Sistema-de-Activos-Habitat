<?php

namespace App\Http\Controllers\Traslado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traslado\Traslado;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;
use Illuminate\Support\Facades\Auth;

class TrasladoController extends Controller
{
    public function index(Request $request)
    {
        $traslados = Traslado::with(['equipo', 'empleado'])->get();
        return view('traslado.index', compact('traslados'));
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

        // Regla 1: un código contable solo puede estar asignado una vez.
        $equipoYaAsignado = Traslado::where('equipo_id_equipo', $equipo->id_equipo)->exists();

        if ($equipoYaAsignado) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'codigo_equipo' => 'El código ' . $equipo->codigo_contable .
                        ' ya está asignado a un empleado y no se puede asignar otra vez. Para cambiar de equipo primero debe darlo de baja.',
                ]);
        }

        // Regla 2: un empleado solo puede tener un equipo asignado.
        $empleadoYaTieneEquipo = Traslado::where('empleado_id_empleado', $empleado->id_empleado)->exists();

        if ($empleadoYaTieneEquipo) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'cod_l4' => 'El empleado ' . trim(($empleado->nombre_empleado ?? '') . ' ' . ($empleado->apellido_empleado ?? '')) .
                        ' ya tiene un equipo asignado y no puede tener otro. Para asignarle otro equipo primero debe darse de baja el actual.',
                ]);
        }

        // Crear el traslado asignando los IDs encontrados
        Traslado::create([
            'equipo_id_equipo' => $equipo->id_equipo,
            'empleado_id_empleado' => $empleado->id_empleado,
            'fecha_envio' => $request->fecha_envio,
            'motivo_traslado' => $request->motivo_traslado,
            'descripcion' => $request->descripcion,
        ]);

    return redirect()->route('traslados.index')->with('success', 'Traslado registrado exitosamente.');
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
        ])->findOrFail($id);

        // Técnico = usuario logueado (modelo Login con su empleado asociado)
        $tecnico = Auth::user();
        $tecnico?->loadMissing(['empleado.puesto', 'empleado.afiliado']);

        return view('traslado.print', compact('traslado', 'tecnico'));
    }

    // Formulario de edición de traslado
    public function edit($id)
    {
        $traslado = Traslado::with(['equipo', 'empleado'])->findOrFail($id);

        return view('traslado.edit', compact('traslado'));
    }

    // Actualizar traslado
    public function update(Request $request, $id)
    {
        $traslado = Traslado::findOrFail($id);

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

        // Regla 1: el código no puede estar asignado en OTRO traslado.
        $equipoYaAsignado = Traslado::where('equipo_id_equipo', $equipo->id_equipo)
            ->where('id_traslado', '!=', $traslado->id_traslado)
            ->exists();

        if ($equipoYaAsignado) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'codigo_equipo' => 'El código ' . $equipo->codigo_contable .
                        ' ya está asignado a un empleado y no se puede asignar otra vez. Para cambiar de equipo primero debe darlo de baja.',
                ]);
        }

        // Regla 2: el empleado no puede tener otro equipo en OTRO traslado.
        $empleadoYaTieneEquipo = Traslado::where('empleado_id_empleado', $empleado->id_empleado)
            ->where('id_traslado', '!=', $traslado->id_traslado)
            ->exists();

        if ($empleadoYaTieneEquipo) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'cod_l4' => 'El empleado ' . trim(($empleado->nombre_empleado ?? '') . ' ' . ($empleado->apellido_empleado ?? '')) .
                        ' ya tiene un equipo asignado y no puede tener otro. Para asignarle otro equipo primero debe darse de baja el actual.',
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
        $traslado->delete();

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