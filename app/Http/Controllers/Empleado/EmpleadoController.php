<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empleado\Empleado;
use App\Models\Puesto\Puesto;
use App\Models\Afiliado\Afiliado;
use App\Models\Estado\Estado;
use App\Models\Rol\Rol;

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
        
        // Buscamos el estado Activo (asumiendo que se llama 'Activo' o puedes usar su ID fijo, por ejemplo 1)
        $estadoActivo = Estado::where('nombre_estado', 'like', '%Activo%')->first();

        return view('Empleado.create', compact('puestos', 'afiliados', 'roles', 'estadoActivo'));
    }

    // Método para almacenar el nuevo registro
    public function store(Request $request)
    {
        // Forzamos el estado activo si el campo está bloqueado/deshabilitado en el request
        $request->merge(['estado_id_estado' => 1]); // Cambia el '1' por el ID real de tu estado Activo

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

        $empleado->update($request->all());

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    // Eliminación lógica (Cambiar estado a Baja)
    public function destroy($id)
    {

      $empleado = Empleado::findOrFail($id);
        //$equipo->delete();

        $empleado->update([ 'estado_id_estado' => 2 ]); // Cambiar el estado a "Inactivo" (id_estado = 2)

        return redirect()->route('empleados.index')->with('success', 'Empleado dado de baja exitosamente.');
    }
}