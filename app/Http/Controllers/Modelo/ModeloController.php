<?php

namespace App\Http\Controllers\Modelo;

use App\Http\Controllers\Controller;
use App\Models\Marca\Marca;
use App\Models\Modelo\Modelo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Catálogo de modelos (cada modelo pertenece a una marca). Solo el Administrador (permisos modelos.*).
 */
class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::with('marca')->withCount('equipos')
            ->orderBy('marca_id_marca')->orderBy('nombre_modelo')
            ->get()
            ->sortBy(fn ($m) => mb_strtolower(($m->marca->nombre_marca ?? '') . ' ' . $m->nombre_modelo))
            ->values();

        return view('Modelo.index', compact('modelos'));
    }

    public function create()
    {
        return view('Modelo.create', ['marcas' => Marca::orderBy('nombre_marca')->get()]);
    }

    public function store(Request $request)
    {
        $request->merge(['nombre_modelo' => $this->limpiar($request->input('nombre_modelo'))]);

        $request->validate($this->reglas($request), $this->mensajes());

        Modelo::create($request->only(['nombre_modelo', 'marca_id_marca']));

        return redirect()->route('modelos.index')->with('success', 'Modelo "' . $request->nombre_modelo . '" creado exitosamente.');
    }

    public function edit($id)
    {
        $modelo = Modelo::withCount('equipos')->findOrFail($id);

        return view('Modelo.edit', ['modelo' => $modelo, 'marcas' => Marca::orderBy('nombre_marca')->get()]);
    }

    public function update(Request $request, $id)
    {
        $modelo = Modelo::withCount('equipos')->findOrFail($id);

        $request->merge(['nombre_modelo' => $this->limpiar($request->input('nombre_modelo'))]);

        $request->validate($this->reglas($request, $modelo->id_modelo), $this->mensajes());

        // Cambiar la marca de un modelo en uso cambiaría la marca de sus equipos ya registrados
        if ($modelo->equipos_count > 0 && (int) $request->marca_id_marca !== (int) $modelo->marca_id_marca) {
            return back()->withInput()->withErrors([
                'marca_id_marca' => 'No se puede cambiar la marca: este modelo ya tiene ' . $modelo->equipos_count .
                    ($modelo->equipos_count === 1 ? ' equipo registrado.' : ' equipos registrados.'),
            ]);
        }

        $modelo->update($request->only(['nombre_modelo', 'marca_id_marca']));

        return redirect()->route('modelos.index')->with('success', 'Modelo actualizado exitosamente.');
    }

    // Un modelo solo se elimina si ningún equipo lo usa (incluye equipos dados de baja)
    public function destroy($id)
    {
        $modelo = Modelo::withCount('equipos')->findOrFail($id);

        if ($modelo->equipos_count > 0) {
            return redirect()->route('modelos.index')->with('error',
                'No se puede eliminar el modelo "' . $modelo->nombre_modelo . '": lo usan ' . $modelo->equipos_count .
                ($modelo->equipos_count === 1 ? ' equipo.' : ' equipos.'));
        }

        $modelo->delete();

        return redirect()->route('modelos.index')->with('success', 'Modelo "' . $modelo->nombre_modelo . '" eliminado exitosamente.');
    }

    // El nombre del modelo no se repite dentro de la misma marca
    private function reglas(Request $request, ?int $ignorarId = null): array
    {
        return [
            'nombre_modelo'  => [
                'required', 'string', 'max:45',
                Rule::unique('modelo', 'nombre_modelo')
                    ->where(fn ($q) => $q->where('marca_id_marca', $request->input('marca_id_marca')))
                    ->ignore($ignorarId, 'id_modelo'),
            ],
            'marca_id_marca' => ['required', 'integer', Rule::exists('marca', 'id_marca')],
        ];
    }

    private function limpiar($valor): ?string
    {
        return is_string($valor) ? trim(preg_replace('/\s+/', ' ', $valor)) : null;
    }

    private function mensajes(): array
    {
        return [
            'nombre_modelo.required'  => 'El nombre del modelo es obligatorio.',
            'nombre_modelo.max'       => 'El nombre del modelo no puede superar los 45 caracteres.',
            'nombre_modelo.unique'    => 'Esa marca ya tiene un modelo con ese nombre.',
            'marca_id_marca.required' => 'Debe seleccionar la marca del modelo.',
            'marca_id_marca.integer'  => 'La marca seleccionada no es válida.',
            'marca_id_marca.exists'   => 'La marca seleccionada no existe.',
        ];
    }
}
