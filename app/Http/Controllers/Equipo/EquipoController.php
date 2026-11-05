<?php

namespace App\Http\Controllers\Equipo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipo\Equipo;
use App\Models\TipoEquipo\TipoEquipo;
use App\Models\Marca\Marca;
use App\Models\Modelo\Modelo;
use App\Models\Estado\Estado;
use Illuminate\Support\Facades\Validator;

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
    $validator = Validator::make($request->all(), [
        'codigo_contable' => 'required|string|max:45|unique:equipo',
        'nombre_equipo' => 'required|string|max:45',
        'tipo_equipo_id_tipo_equipo' => 'required|integer',
        'marca_id_marca' => 'required|integer', // Asegúrate que coincida con el name de tu select (marca_id_marca o marca_id)
        'modelo_id_modelo' => 'required|integer',
        'procesador' => 'required|string|max:45',
        'ram' => 'required|string|max:45',
        'tipo_ram' => 'required|string|max:45',
        'ssd' => 'required|string|max:45',
        'st' => 'required|string|max:45',
        'ex' => 'required|string|max:45',
        'fecha_compra' => 'required|date',
        'precio_compra' => 'required|numeric|min:0',
        'valor_libros' => 'required|numeric|min:0',
        'estado_id_estado' => 'required|integer',
        'notas_adicionales' => 'required|string|max:1000',
        // Si ya hay una imagen temporal guardada, permitimos que el archivo sea opcional en este intento fallido
        'imagen_equipo' => 'required_without:temp_imagen_equipo|image|max:5120',
        'imagen_codigo' => 'required_without:temp_imagen_codigo|image|max:5120',
    ], [
        // Mensajes personalizados
        'codigo_contable.required' => 'El código contable es obligatorio.',
        'codigo_contable.unique' => 'Este código contable ya está registrado en el sistema.',
        'nombre_equipo.required' => 'El nombre del equipo es obligatorio.',
        'tipo_equipo_id_tipo_equipo.required' => 'Debe seleccionar un tipo de equipo.',
        'marca_id_marca.required' => 'Debe seleccionar una marca.',
        'modelo_id_modelo.required' => 'Debe seleccionar un modelo.',
        'procesador.required' => 'Debe ingresar el procesador del equipo.',
        'ram.required' => 'Debe ingresar la RAM del equipo.',
        'tipo_ram.required' => 'Debe ingresar el tipo de RAM del equipo.',
        'ssd.required' => 'Debe ingresar la capacidad de almacenamiento del disco del equipo.',
        'st.required' => 'Debe ingresar el service tag del equipo.',
        'ex.required' => 'Debe ingresar el express code del equipo.',
        'fecha_compra.required' => 'Debe ingresar la fecha de compra del equipo.',
        'precio_compra.required' => 'Debe ingresar el precio de compra del equipo.',
        'valor_libros.required' => 'Debe ingresar el valor en libros actual del equipo.',
        'notas_adicionales.required' => 'Debe ingresar el motivo del cual se configuró este equipo.',
        'imagen_equipo.required' => 'Debe ingresar la fotografía del equipo completo',
        'imagen_equipo.required_without' => 'Debe ingresar la fotografía del equipo completo',
        'imagen_codigo.required' => 'Debe ingresar la fotografía del código contable del equipo',
        'imagen_codigo.required_without' => 'Debe ingresar la fotografía del código contable del equipo',
    ]);

    // Si la validación falla, retenemos las imágenes convirtiéndolas a Base64 temporalmente
    if ($validator->fails()) {
        $inputData = $request->all();

        if ($request->hasFile('imagen_equipo')) {
            $file = $request->file('imagen_equipo');
            $inputData['temp_imagen_equipo'] = base64_encode(file_get_contents($file->getRealPath()));
        }

        if ($request->hasFile('imagen_codigo')) {
            $file = $request->file('imagen_codigo');
            $inputData['temp_imagen_codigo'] = base64_encode(file_get_contents($file->getRealPath()));
        }

        return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputData);
    }

    $data = $request->all();
    $data['estado_id_estado'] = 1;

    // Procesar imagen del equipo (si se subió archivo nuevo o si se recupera del respaldo temporal)
    if ($request->hasFile('imagen_equipo')) {
        $data['imagen_equipo'] = file_get_contents($request->file('imagen_equipo')->getRealPath());
    } elseif ($request->filled('temp_imagen_equipo')) {
        $data['imagen_equipo'] = base64_decode($request->input('temp_imagen_equipo'));
    }

    // Procesar imagen del código (si se subió archivo nuevo o si se recupera del respaldo temporal)
    if ($request->hasFile('imagen_codigo')) {
        $data['imagen_codigo'] = file_get_contents($request->file('imagen_codigo')->getRealPath());
    } elseif ($request->filled('temp_imagen_codigo')) {
        $data['imagen_codigo'] = base64_decode($request->input('temp_imagen_codigo'));
    }

    // Limpiamos los campos temporales para que Eloquent no intente guardarlos en columnas que no existen en la tabla
    unset($data['temp_imagen_equipo'], $data['temp_imagen_codigo']);

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
        'codigo_contable'            => 'required|string|max:45|unique:equipo,codigo_contable,' . $id . ',id_equipo',
        'nombre_equipo'              => 'required|string|max:45',
        'tipo_equipo_id_tipo_equipo' => 'required|integer',
        'marca_id'                   => 'required|integer', // <--- ¡Incluido y validado correctamente![cite: 11]
        'modelo_id_modelo'           => 'required|integer',
        'estado_id_estado'           => 'required|integer',
        'imagen_equipo'              => 'nullable|image|max:2048',
        'imagen_codigo'              => 'nullable|image|max:2048',
    ], [
        // Mensajes personalizados en español
        'codigo_contable.required'            => 'El código contable es obligatorio.',
        'codigo_contable.unique'              => 'Este código contable ya se encuentra registrado.',
        'nombre_equipo.required'              => 'El nombre del equipo es obligatorio.',
        'tipo_equipo_id_tipo_equipo.required' => 'Debe seleccionar un tipo de equipo.',
        'marca_id.required'                   => 'Debe seleccionar una marca.',
        'modelo_id_modelo.required'           => 'Debe seleccionar un modelo.',
        'imagen_equipo.image'                 => 'La fotografía del equipo debe ser un archivo de imagen válido.',
        'imagen_equipo.max'                   => 'La fotografía del equipo no debe superar los 2MB.',
        'imagen_codigo.image'                 => 'La fotografía del código contable debe ser un archivo de imagen válido.',
        'imagen_codigo.max'                   => 'La fotografía del código contable no debe superar los 2MB.',
    ]);

    $data = $request->except(['_token', '_method']);

    // Manejo de la imagen del equipo
    if ($request->hasFile('imagen_equipo')) {
        $data['imagen_equipo'] = file_get_contents($request->file('imagen_equipo')->getRealPath());
    } else {
        unset($data['imagen_equipo']);
    }

    // Manejo de la imagen del código contable
    if ($request->hasFile('imagen_codigo')) {
        $data['imagen_codigo'] = file_get_contents($request->file('imagen_codigo')->getRealPath());
    } else {
        unset($data['imagen_codigo']);
    }

    $equipo->update($data);

    return redirect()->route('equipos.index')->with('success', 'Equipo actualizado exitosamente.');
}

    // Eliminar equipo
    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);
        //$equipo->delete();

        $equipo->update([ 'estado_id_estado' => 2 ]); // Cambiar el estado a "Inactivo" (id_estado = 2)

        return redirect()->route('equipos.index')->with('success', 'Equipo dado de baja exitosamente.');
    }
}