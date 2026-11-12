<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empleado\Empleado;
use App\Models\Puesto\Puesto;
use App\Models\Afiliado\Afiliado;
use App\Models\Estado\Estado;
use App\Models\Rol\Rol;
use App\Models\Traslado\Traslado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmpleadoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $empleados = Empleado::with(['puesto', 'afiliado', 'estado', 'rol'])
            ->when($search, function ($query, $search) {
                return $query->where('nombre_empleado', 'like', "%{$search}%")
                             ->orWhere('apellido_empleado', 'like', "%{$search}%")
                             ->orWhere('cod_l4', 'like', "%{$search}%");
            })
            ->get(); // O puedes usar paginate(10) si prefieres paginación

        return view('Empleado.index', compact('empleados', 'search'));
    }



   // Método para mostrar el formulario de creación
    public function create()
    {
        $puestos = Puesto::all();
        $afiliados = Afiliado::all();
        $roles = Rol::all();
        
        // Estado Activo leído de la tabla estado
        $estadoActivo = Estado::findOrFail(Estado::idActivo());

        return view('Empleado.create', compact('puestos', 'afiliados', 'roles', 'estadoActivo'));
    }

    // Método para almacenar el nuevo registro
    public function store(Request $request)
    {
        // Forzamos el estado activo si el campo está bloqueado/deshabilitado en el request
        $request->merge(['estado_id_estado' => Estado::idActivo()]); // ID del estado Activo desde la tabla estado

        $request->validate([
            'nombre_empleado' => 'required|string|max:100',
            'apellido_empleado' => 'required|string|max:100',
            'cod_l4' => 'nullable|string|max:50',
            'puesto_id_puesto' => 'required|exists:puesto,id_puesto',
            'afiliado_id_afiliado' => 'required|exists:afiliado,id_afiliado',
            'rol_id_rol' => 'required|exists:rol,id_rol',
            'estado_id_estado' => 'required|exists:estado,id_estado',
        ]);

        Empleado::create($request->all());

        return redirect()->route('empleados.index')->with('success', 'Empleado creado exitosamente.');
    }

    // Mostrar el formulario de edición
    public function edit($id)
    {
        $empleado = Empleado::findOrFail($id);
        $puestos = Puesto::all();
        $afiliados = Afiliado::all();
        $roles = Rol::all();
        $estados = Estado::all(); // Opcional si permites cambiar estado en edición

        return view('Empleado.edit', compact('empleado', 'puestos', 'afiliados', 'roles', 'estados'));
    }

    // Actualizar los datos del empleado en la base de datos
    public function update(Request $request, $id)
    {
        $empleado = Empleado::findOrFail($id);

        $request->validate([
            'nombre_empleado' => 'required|string|max:100',
            'apellido_empleado' => 'required|string|max:100',
            'cod_l4' => 'nullable|string|max:50',
            'puesto_id_puesto' => 'required|exists:puesto,id_puesto',
            'afiliado_id_afiliado' => 'required|exists:afiliado,id_afiliado',
            'rol_id_rol' => 'required|exists:rol,id_rol',
            'estado_id_estado' => 'required|exists:estado,id_estado',
        ]);

        $datos = $request->except('fecha_baja');

        // La fecha de baja se lleva sola: se fija al pasar a Baja y se limpia si vuelve a otro estado
        if (Estado::esBaja($request->estado_id_estado)) {
            $datos['fecha_baja'] = $empleado->fecha_baja ?? now()->toDateString();
        } else {
            $datos['fecha_baja'] = null;
        }

        $empleado->update($datos);

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    // Eliminación lógica (Cambiar estado a Baja)
    public function destroy($id)
    {

      $empleado = Empleado::findOrFail($id);
        //$equipo->delete();

        // Al darse de baja el empleado, el equipo que tenía asignado se libera:
        // el traslado queda finalizado y el equipo disponible para asignar a otro empleado.
        $equiposLiberados = DB::transaction(function () use ($empleado) {
            $vigentes = Traslado::vigentes()
                ->where('empleado_id_empleado', $empleado->id_empleado)
                ->with('equipo')
                ->get();

            foreach ($vigentes as $traslado) {
                $traslado->update([
                    'fecha_devolucion' => now()->toDateString(),
                    'motivo_devolucion' => 'Empleado dado de baja',
                    'causa_devolucion' => Traslado::CAUSA_BAJA_EMPLEADO,
                    'tecnico_devolucion_id_empleado' => Auth::user()?->empleado_id_empleado,
                ]);
            }

            $empleado->update([
                'estado_id_estado' => Estado::idBaja(), // Estado "Baja" leído de la tabla estado
                'fecha_baja' => now()->toDateString(),
            ]);

            return $vigentes;
        });

        $mensaje = 'Empleado dado de baja exitosamente.';

        if ($equiposLiberados->isNotEmpty()) {
            $mensaje .= ' El equipo ' . $equiposLiberados->pluck('equipo.codigo_contable')->filter()->implode(', ') .
                ' quedó disponible para asignar.';
        }

        return redirect()->route('empleados.index')->with('success', $mensaje);
    }
}