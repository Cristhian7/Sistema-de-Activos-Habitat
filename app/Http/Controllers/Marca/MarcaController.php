<?php

namespace App\Http\Controllers\Marca;

use App\Http\Controllers\Controller;
use App\Models\Marca\Marca;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Catálogo de marcas. Solo el Administrador (permisos marcas.* en config/permisos.php).
 */
class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::withCount(['modelos', 'equipos'])->orderBy('nombre_marca')->get();

        return view('Marca.index', compact('marcas'));
    }

    public function create()
    {
        return view('Marca.create');
    }

    public function store(Request $request)
    {
        $request->merge(['nombre_marca' => $this->limpiar($request->input('nombre_marca'))]);

        $request->validate([
            'nombre_marca' => ['required', 'string', 'max:45', Rule::unique('marca', 'nombre_marca')],
        ], $this->mensajes());

        Marca::create(['nombre_marca' => $request->nombre_marca]);

        return redirect()->route('marcas.index')->with('success', 'Marca "' . $request->nombre_marca . '" creada exitosamente.');
    }

    public function edit($id)
    {
        $marca = Marca::withCount(['modelos', 'equipos'])->findOrFail($id);

        return view('Marca.edit', compact('marca'));
    }

    public function update(Request $request, $id)
    {
        $marca = Marca::findOrFail($id);

        $request->merge(['nombre_marca' => $this->limpiar($request->input('nombre_marca'))]);

        $request->validate([
            'nombre_marca' => ['required', 'string', 'max:45', Rule::unique('marca', 'nombre_marca')->ignore($marca->id_marca, 'id_marca')],
        ], $this->mensajes());

        $marca->update(['nombre_marca' => $request->nombre_marca]);

        return redirect()->route('marcas.index')->with('success', 'Marca actualizada exitosamente.');
    }

    // Una marca solo se elimina si no tiene modelos (los modelos y equipos dependen de ella)
    public function destroy($id)
    {
        $marca = Marca::withCount('modelos')->findOrFail($id);

        if ($marca->modelos_count > 0) {
            return redirect()->route('marcas.index')->with('error',
                'No se puede eliminar la marca "' . $marca->nombre_marca . '": tiene ' . $marca->modelos_count .
                ($marca->modelos_count === 1 ? ' modelo' : ' modelos') . '. Elimina o mueve primero sus modelos.');
        }

        $marca->delete();

        return redirect()->route('marcas.index')->with('success', 'Marca "' . $marca->nombre_marca . '" eliminada exitosamente.');
    }

    // Sin espacios de más (inicio, fin y repetidos)
    private function limpiar($valor): ?string
    {
        return is_string($valor) ? trim(preg_replace('/\s+/', ' ', $valor)) : null;
    }

    private function mensajes(): array
    {
        return [
            'nombre_marca.required' => 'El nombre de la marca es obligatorio.',
            'nombre_marca.max'      => 'El nombre de la marca no puede superar los 45 caracteres.',
            'nombre_marca.unique'   => 'Ya existe una marca con ese nombre.',
        ];
    }
}
