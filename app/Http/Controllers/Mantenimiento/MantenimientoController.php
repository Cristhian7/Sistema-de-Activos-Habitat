<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Mantenimiento\Mantenimiento;
use App\Models\TipoEquipo\TipoEquipo;
use App\Models\Equipo\Equipo;
use App\Models\Empleado\Empleado;
use App\Models\Marca\Marca;
use App\Models\Modelo\Modelo;
use App\Models\Estado\Estado;

class MantenimientoController extends Controller
{
    // Listado de mantenimientos
    public function index()
    {
        $mantenimientos = Mantenimiento::with(['equipo', 'empleado', 'tecnico'])->paginate(10);
        return view('mantenimiento.index', compact('mantenimientos'));

       // $mantenimientos = Mantenimiento::with(['equipo', 'empleado'])->get();
        //return view('mantenimiento.index', compact('mantenimientos'));
    }

    // Formulario de creación
    public function create()
    {
        return view('mantenimiento.create');
    }

    // Guardar mantenimiento
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_equipo'          => 'required|string',
            'cod_l4'                 => 'required|exists:empleado,cod_l4',
            'descripcion'            => 'required|string|max:1000',
            'fecha_mantenimiento'    => 'required|date',
            // Fotos propias del mantenimiento (opcionales)
            'imagen_mantenimiento_1' => 'nullable|image|max:5120',
            'imagen_mantenimiento_2' => 'nullable|image|max:5120',
        ], [
            'codigo_equipo.required'          => 'El código del equipo es obligatorio.',
            'cod_l4.required'                 => 'Debe ingresar el código L4 del empleado responsable.',
            'cod_l4.exists'                   => 'No existe un empleado con ese código L4.',
            'descripcion.required'            => 'La descripción del mantenimiento es obligatoria.',
            'descripcion.max'                 => 'La descripción no puede superar los 1000 caracteres.',
            'fecha_mantenimiento.required'    => 'La fecha de mantenimiento es obligatoria.',
            'imagen_mantenimiento_1.image'    => 'La primera fotografía debe ser un archivo de imagen válido.',
            'imagen_mantenimiento_1.max'      => 'La primera fotografía no debe superar los 5MB.',
            'imagen_mantenimiento_2.image'    => 'La segunda fotografía debe ser un archivo de imagen válido.',
            'imagen_mantenimiento_2.max'      => 'La segunda fotografía no debe superar los 5MB.',
        ]);

        if ($validator->fails()) {
            return $this->volverConImagenes($request, $validator->errors());
        }

        // Acepta el código completo (CE-04728) o solo los últimos dígitos (4728)
        $codigo = trim($request->codigo_equipo);
        $equipo = Equipo::where('codigo_contable', $codigo)->first()
            ?? Equipo::where('codigo_contable', 'LIKE', '%' . $codigo)->first();

        if (!$equipo) {
            return $this->volverConImagenes($request, [
                'codigo_equipo' => 'No existe un equipo con ese código contable.',
            ]);
        }

        $empleado = Empleado::where('cod_l4', $request->cod_l4)->first();

        $data = [
            'equipo_id_equipo'     => $equipo->id_equipo,
            'empleado_id_empleado' => $empleado->id_empleado,
            // Técnico que realiza el mantenimiento = empleado del usuario logueado
            'tecnico_id_empleado'  => Auth::user()?->empleado_id_empleado,
            'descripcion'          => $request->descripcion,
            'fecha_mantenimiento'  => $request->fecha_mantenimiento,
        ];

        // Fotos del mantenimiento: archivo nuevo o respaldo temporal de un intento fallido.
        // Se guardan en la tabla mantenimiento; las imágenes del equipo no se tocan.
        foreach ([1, 2] as $n) {
            $campo = "imagen_mantenimiento_$n";
            $temp  = "temp_imagen_mant_$n";

            if ($request->hasFile($campo)) {
                $data[$campo] = file_get_contents($request->file($campo)->getRealPath());
            } elseif ($request->filled($temp)) {
                $data[$campo] = base64_decode($request->input($temp));
            }
        }

        Mantenimiento::create($data);

        return redirect()->route('mantenimientos.index')->with('success', 'Mantenimiento registrado exitosamente.');
    }

    // Relaciones que necesitan las vistas de detalle e impresión
    private function cargarMantenimiento($id): Mantenimiento
    {
        return Mantenimiento::with([
            'equipo.tipoEquipo',
            'equipo.modelo',
            'equipo.marca',
            'equipo.estado',
            'empleado.puesto',
            'empleado.afiliado',
            'tecnico.puesto',
            'tecnico.afiliado',
        ])->findOrFail($id);
    }

    // Ver el detalle de un mantenimiento
    public function show($id)
    {
        $mantenimiento = $this->cargarMantenimiento($id);

        return view('mantenimiento.show', compact('mantenimiento'));
    }

    // Vista de impresión del mantenimiento (incluye firmas)
    public function imprimir($id)
    {
        $mantenimiento = $this->cargarMantenimiento($id);

        // Técnico guardado al registrar el mantenimiento. Si es un registro
        // anterior sin técnico, se usa el empleado del usuario logueado.
        $tecnico = $mantenimiento->tecnico;

        if (!$tecnico) {
            $usuario = Auth::user();
            $usuario?->loadMissing(['empleado.afiliado']);
            $tecnico = $usuario?->empleado;
        }

        return view('mantenimiento.print', compact('mantenimiento', 'tecnico'));
    }

    // Regresa al formulario conservando los datos y las fotos ya cargadas
    private function volverConImagenes(Request $request, $errors)
    {
        $input = $request->except(['imagen_mantenimiento_1', 'imagen_mantenimiento_2']);

        foreach ([1, 2] as $n) {
            $campo = "imagen_mantenimiento_$n";
            $temp  = "temp_imagen_mant_$n";
            $archivo = $request->file($campo);

            if ($archivo && $archivo->isValid() && str_starts_with((string) $archivo->getMimeType(), 'image/')) {
                $input[$temp] = base64_encode(file_get_contents($archivo->getRealPath()));
            }
        }

        return redirect()->back()->withErrors($errors)->withInput($input);
    }
}