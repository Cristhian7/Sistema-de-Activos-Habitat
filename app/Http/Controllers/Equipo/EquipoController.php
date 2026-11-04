<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipo\Equipo;
use App\Models\TipoEquipo\TipoEquipo;
use App\Models\Marca\Marca;
use App\Models\Modelo\Modelo;
use App\Models\Estado\Estado;

class EquipoController extends Controller
{
    // Listado de equipos
    public function index()
    {
        $equipos = Equipo::with(['tipoEquipo', 'estado', 'modelo.marca'])->get();
        return view('equipo.index', compact('equipos'));
    }

    // Formulario de creación
    public function create()
    {
        $tipos = TipoEquipo::all();
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $estados = Estado::all();

        return view('equipo.create', compact('tipos', 'marcas', 'modelos', 'estados'));
    }

    // Guardar nuevo equipo
    public function store(Request $request)
    {
        $request->validate([
            'codigo_contable' => 'required|string|max:45|unique:equipo',
            'nombre_equipo' => 'required|string|max:45',
            'tipo_equipo_id_tipo_equipo' => 'required|integer',
            'modelo_id_modelo' => 'required|integer',
            'estado_id_estado' => 'required|integer',
            'imagen_equipo' => 'nullable|image|max:2048',
            'imagen_codigo' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('imagen_equipo')) {
            $data['imagen_equipo'] = file_get_contents($request->file('imagen_equipo')->getRealPath());
        }

        if ($request->hasFile('imagen_codigo')) {
            $data['imagen_codigo'] = file_get_contents($request->file('imagen_codigo')->getRealPath());
        }

        Equipo::create($data);

        return redirect()->route('equipos.index')->with('success', 'Equipo registrado exitosamente.');
    }

    // Formulario de edición
    public function edit($id)
    {
        $equipo = Equipo::with('modelo.marca')->findOrFail($id);
        $tipos = TipoEquipo::all();
        $marcas = Marca::all();
        $modelos = Modelo::all();
        $estados = Estado::all();

        return view('equipo.edit', compact('equipo', 'tipos', 'marcas', 'modelos', 'estados'));
    }

    // Actualizar equipo
    public function update(Request $request, $id)
    {
        $equipo = Equipo::findOrFail($id);

        $request->validate([
            'codigo_contable' => 'required|string|max:45|unique:equipo,codigo_contable,' . $id . ',id_equipo',
            'nombre_equipo' => 'required|string|max:45',
            'tipo_equipo_id_tipo_equipo' => 'required|integer',
            'modelo_id_modelo' => 'required|integer',
            'estado_id_estado' => 'required|integer',
            'imagen_equipo' => 'nullable|image|max:2048',
            'imagen_codigo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['_token', '_method']);

        if ($request->hasFile('imagen_equipo')) {
            $data['imagen_equipo'] = file_get_contents($request->file('imagen_equipo')->getRealPath());
        } else {
            unset($data['imagen_equipo']);
        }

        if ($request->hasFile('imagen_codigo')) {
            $data['imagen_codigo'] = file_get_contents($request->file('imagen_codigo')->getRealPath());
        } else {
            unset($data['imagen_codigo']);
        }

        $equipo->update($data);

        return redirect()->route('equipos.index')->with('success', 'Equipo actualizado exitosamente.');
    } // <-- Llave que cerraba el método update (esta era la que faltaba)

    // Eliminar equipo
    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);
        //$equipo->delete();

        $equipo->update([ 'estado_id_estado' => 2 ]); // Cambiar el estado a "Inactivo" (id_estado = 2)

        return redirect()->route('equipos.index')->with('success', 'Equipo dado de baja exitosamente.');
    }
}