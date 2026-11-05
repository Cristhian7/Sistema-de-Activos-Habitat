<?php

namespace App\Http\Controllers\Traslado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traslado\Traslado;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;

class TrasladoController extends Controller
{
    public function index(Request $request)
    {
        $traslados = Traslado::with(['equipo_id_equipo', 'empleado_id_empleado'])->get();
        return view('traslado.index', compact('traslados'));
    }

    public function create()
    {
        $equipos = Equipo::all();
        $empleados = Empleado::all();

        return view('Traslado.create', compact('equipos', 'empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipo_id_equipo' => 'required|exists:equipo,id_equipo',
            'empleado_destino_id' => 'required|exists:empleado,id_empleado',
            'fecha_traslado' => 'required|date',
            'observaciones' => 'nullable|string|max:255',
        ]);

        Traslado::create($request->all());

        return redirect()->route('traslados.index')->with('success', 'Traslado registrado exitosamente.');
    }
}