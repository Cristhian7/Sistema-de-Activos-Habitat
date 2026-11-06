<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mantenimiento;
use App\Models\Equipo;
use App\Models\Empleado;

class MantenimientoController extends Controller
{
    // Listado de mantenimientos
    public function index()
    {
        $mantenimientos = Mantenimiento::with(['equipo', 'empleado'])->latest('id_mantenimiento')->paginate(10);
        return view('mantenimiento.index', compact('mantenimientos'));
    }

    // Formulario de creación
    public function create()
    {
        $empleados = Empleado::all();
        return view('mantenimiento.create', compact('empleados'));
    }

    // Guardar mantenimiento
    public function store(Request $request)
    {
        $request->validate([
            'codigo_equipo'        => 'required|exists:equipo,codigo_contable',
            'empleado_id_empleado' => 'required|exists:empleado,id_empleado',
            'descripcion'          => 'required|string',
            'fecha_mantenimiento'  => 'required|date',
        ], [
            'codigo_equipo.required'        => 'El código del equipo es obligatorio.',
            'codigo_equipo.exists'          => 'El equipo seleccionado no existe en la base de datos.',
            'empleado_id_empleado.required' => 'Debe seleccionar un empleado responsable.',
            'descripcion.required'          => 'La descripción del mantenimiento es obligatoria.',
            'fecha_mantenimiento.required'  => 'La fecha de mantenimiento es obligatoria.',
        ]);

        // Buscamos el equipo mediante su código contable para obtener su ID real
        $equipo = Equipo::where('codigo_contable', $request->codigo_equipo)->first();

        Mantenimiento::create([
            'equipo_id_equipo'     => $equipo->id_equipo,
            'empleado_id_empleado' => $request->empleado_id_empleado,
            'descripcion'          => $request->descripcion,
            'fecha_mantenimiento'  => $request->fecha_mantenimiento,
        ]);

        return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento registrado exitosamente.');
    }
}