<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Mantenimiento\Mantenimiento;
use App\Models\Mantenimiento\MantenimientoPieza;
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
        // Se traen todos (sin paginar) para que el buscador del listado filtre sobre el total, como en los otros módulos.
        // Las fotos (campos binarios) no se cargan: el listado no las usa.
        $mantenimientos = Mantenimiento::select([
                'id_mantenimiento', 'equipo_id_equipo', 'empleado_id_empleado', 'tecnico_id_empleado',
                'descripcion', 'fecha_mantenimiento',
            ])
            ->with(['equipo', 'empleado', 'tecnico', 'piezas'])
            ->orderByDesc('fecha_mantenimiento')
            ->orderByDesc('id_mantenimiento')
            ->get();

        return view('Mantenimiento.index', compact('mantenimientos'));
    }

    // Formulario de creación
    public function create()
    {
        // Estado que se muestra por defecto (un equipo a atender normalmente está Activo), leído de la tabla estado
        $estadoActivo = Estado::findOrFail(Estado::idActivo());

        return view('Mantenimiento.create', compact('estadoActivo'));
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
            // Piezas cambiadas o agregadas (opcionales; cada una exige su fotografía)
            'piezas'                    => 'nullable|array|max:' . MantenimientoPieza::MAX_POR_MANTENIMIENTO,
            'piezas.*.tipo_pieza'       => ['required', Rule::in(MantenimientoPieza::TIPOS)],
            'piezas.*.accion'           => ['required', Rule::in(array_keys(MantenimientoPieza::ACCIONES))],
            'piezas.*.descripcion'      => 'nullable|string|max:255',
            'piezas.*.imagen'           => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:' . MantenimientoPieza::MAX_KB],
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
            'piezas.max'                      => 'Solo se pueden registrar hasta ' . MantenimientoPieza::MAX_POR_MANTENIMIENTO . ' piezas por mantenimiento.',
            'piezas.*.tipo_pieza.required'    => 'Pieza :position: seleccione qué pieza es.',
            'piezas.*.tipo_pieza.in'          => 'Pieza :position: el tipo de pieza no es válido.',
            'piezas.*.accion.required'        => 'Pieza :position: indique si se cambió o se agregó.',
            'piezas.*.accion.in'              => 'Pieza :position: la acción no es válida.',
            'piezas.*.descripcion.max'        => 'Pieza :position: el detalle no puede superar los 255 caracteres.',
            'piezas.*.imagen.image'           => 'Pieza :position: la fotografía debe ser una imagen válida (JPG, PNG o WEBP).',
            'piezas.*.imagen.mimes'           => 'Pieza :position: la fotografía debe ser JPG, PNG o WEBP.',
            'piezas.*.imagen.max'             => 'Pieza :position: la fotografía no debe superar los 5MB.',
            'piezas.*.imagen.uploaded'        => 'Pieza :position: la fotografía no se pudo cargar (revise que no supere los 5MB).',
        ]);

        // Reglas que dependen de otros campos: la foto de cada pieza es obligatoria y "Otra" exige detalle
        $validator->after(function ($validator) use ($request) {
            foreach ((array) $request->input('piezas', []) as $i => $fila) {
                $n = (int) $i + 1;

                if (($fila['tipo_pieza'] ?? null) === MantenimientoPieza::TIPO_OTRA && trim((string) ($fila['descripcion'] ?? '')) === '') {
                    $validator->errors()->add("piezas.$i.descripcion", "Pieza $n: describa qué pieza es (tipo \"Otra\").");
                }

                if ($validator->errors()->has("piezas.$i.imagen")) {
                    continue; // ya tiene un error de archivo
                }

                if ($this->imagenDePieza($request, $i) === null) {
                    $validator->errors()->add("piezas.$i.imagen", "Pieza $n: la fotografía de la pieza es obligatoria.");
                }
            }
        });

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

        // El mantenimiento y sus piezas se guardan juntos: o queda todo o no queda nada
        DB::transaction(function () use ($data, $request) {
            $mantenimiento = Mantenimiento::create($data);

            foreach ((array) $request->input('piezas', []) as $i => $fila) {
                [$binario, $mime] = $this->imagenDePieza($request, $i);

                MantenimientoPieza::create([
                    'mantenimiento_id_mantenimiento' => $mantenimiento->id_mantenimiento,
                    'tipo_pieza'  => $fila['tipo_pieza'],
                    'accion'      => $fila['accion'],
                    'descripcion' => isset($fila['descripcion']) && trim($fila['descripcion']) !== '' ? trim($fila['descripcion']) : null,
                    'imagen'      => $binario,
                    'imagen_mime' => $mime,
                ]);
            }
        });

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
            'piezas',
        ])->findOrFail($id);
    }

    // Ver el detalle de un mantenimiento
    public function show($id)
    {
        $mantenimiento = $this->cargarMantenimiento($id);

        return view('Mantenimiento.show', compact('mantenimiento'));
    }

    // Fotografía de una pieza (se entrega por aquí, no dentro de la página, para no cargar la vista con binarios)
    public function imagenPieza($id, $pieza)
    {
        $registro = MantenimientoPieza::where('id_mantenimiento_pieza', $pieza)
            ->where('mantenimiento_id_mantenimiento', $id)
            ->firstOrFail();

        return response($registro->imagen, 200, [
            'Content-Type'           => $registro->imagen_mime,
            'Content-Disposition'    => 'inline; filename="pieza-' . (int) $registro->id_mantenimiento_pieza . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, max-age=3600',
        ]);
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

        return view('Mantenimiento.print', compact('mantenimiento', 'tecnico'));
    }

    /**
     * Fotografía de la fila $i de piezas: archivo nuevo o respaldo temporal de un intento anterior.
     * Devuelve [contenido binario, mime] o null si no hay una imagen válida (JPG, PNG o WEBP, hasta 5 MB).
     * El respaldo temporal viene del navegador, por eso se vuelve a comprobar su contenido real.
     */
    private function imagenDePieza(Request $request, $i): ?array
    {
        $archivo = $request->file("piezas.$i.imagen");

        if ($archivo && $archivo->isValid()) {
            $binario = file_get_contents($archivo->getRealPath());
        } else {
            $temp = $request->input("piezas.$i.temp_imagen");
            $binario = is_string($temp) && $temp !== '' ? base64_decode($temp, true) : false;
        }

        if ($binario === false || $binario === null || strlen($binario) > MantenimientoPieza::MAX_KB * 1024) {
            return null;
        }

        $mime = MantenimientoPieza::mimeDeImagen($binario);

        return $mime ? [$binario, $mime] : null;
    }

    // Regresa al formulario conservando los datos y las fotos ya cargadas
    private function volverConImagenes(Request $request, $errors)
    {
        $input = $request->except(['imagen_mantenimiento_1', 'imagen_mantenimiento_2', 'piezas']);

        foreach ([1, 2] as $n) {
            $campo = "imagen_mantenimiento_$n";
            $temp  = "temp_imagen_mant_$n";
            $archivo = $request->file($campo);

            if ($archivo && $archivo->isValid() && str_starts_with((string) $archivo->getMimeType(), 'image/')) {
                $input[$temp] = base64_encode(file_get_contents($archivo->getRealPath()));
            }
        }

        // Piezas: se conservan sus datos y la foto (archivo nuevo o la que ya se había respaldado)
        $piezas = [];
        foreach ((array) $request->input('piezas', []) as $i => $fila) {
            $imagen = $this->imagenDePieza($request, $i);

            $piezas[$i] = [
                'tipo_pieza'  => $fila['tipo_pieza'] ?? '',
                'accion'      => $fila['accion'] ?? '',
                'descripcion' => $fila['descripcion'] ?? '',
                'temp_imagen' => $imagen ? base64_encode($imagen[0]) : '',
            ];
        }
        $input['piezas'] = $piezas;

        return redirect()->back()->withErrors($errors)->withInput($input);
    }
}
