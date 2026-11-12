<?php

namespace App\Http\Controllers\Documento;

use App\Http\Controllers\Controller;
use App\Models\Baja\Baja;
use App\Models\Traslado\Traslado;
use App\Support\DocumentoFirmado;
use App\Support\Notificaciones;
use App\Support\Permisos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Subida y consulta del documento firmado de un traslado o de una baja.
 * Las rutas reciben el tipo ("traslados" o "bajas") como valor fijo de la ruta.
 */
class DocumentoFirmadoController extends Controller
{
    private const TIPOS = [
        'traslados' => ['modelo' => Traslado::class, 'permiso' => 'traslados', 'show' => 'traslados.show', 'nombre' => 'del traslado'],
        'bajas'     => ['modelo' => Baja::class,     'permiso' => 'bajas',     'show' => 'bajas.show',     'nombre' => 'de la baja'],
    ];

    // Subir el documento firmado (si ya había uno, lo reemplaza).
    // Ojo: Laravel pasa los parámetros de la ruta por posición, en el orden de la URI; $tipo viene de ->defaults() y va al final.
    public function subir(Request $request, $id, string $tipo)
    {
        $config = $this->config($tipo);
        $registro = $config['modelo']::findOrFail($id);

        // Subirlo por primera vez = crear. Reemplazar uno existente = editar.
        $permiso = $registro->tiene_documento ? $config['permiso'] . '.editar' : $config['permiso'] . '.crear';
        abort_unless(Permisos::puede($permiso), 403, 'Tu rol no tiene permiso para esta acción.');

        $request->validate([
            'documento' => [
                'required',
                'file',
                'mimes:' . implode(',', DocumentoFirmado::EXTENSIONES),
                'max:' . DocumentoFirmado::MAX_KB,
            ],
        ], [
            'documento.required' => 'Seleccione el archivo firmado.',
            'documento.file'     => 'El documento no se pudo cargar. Intente de nuevo.',
            'documento.mimes'    => 'El documento debe ser un PDF o una imagen (JPG o PNG).',
            'documento.max'      => 'El documento no debe superar los 10 MB.',
            'documento.uploaded' => 'El documento no se pudo cargar. Revise que no supere los 10 MB.',
        ]);

        $anterior = $registro->documento_firmado;

        $registro->update(DocumentoFirmado::guardar($request->file('documento'), $tipo, (int) $id) + [
            'documento_cargado_por' => Auth::user()?->empleado_id_empleado,
        ]);

        // Avisa a quien revisa (Técnico) que hay un documento nuevo para ver
        Notificaciones::documentoSubido($registro->fresh(), $anterior !== null && $anterior !== '');

        // El archivo anterior se borra solo después de guardar bien el nuevo
        if ($anterior && $anterior !== $registro->documento_firmado) {
            DocumentoFirmado::borrarArchivo($anterior);
        }

        return redirect()->route($config['show'], $id)
            ->with('success', 'Documento firmado ' . $config['nombre'] . ' guardado correctamente.');
    }

    // Ver el documento en el navegador (PDF o imagen); con ?descargar=1 lo descarga
    public function ver(Request $request, $id, string $tipo)
    {
        $config = $this->config($tipo);
        $registro = $config['modelo']::findOrFail($id);

        abort_unless(DocumentoFirmado::existe($registro->documento_firmado), 404, 'Este registro no tiene documento firmado.');

        $ruta = DocumentoFirmado::rutaAbsoluta($registro->documento_firmado);
        $extension = pathinfo($ruta, PATHINFO_EXTENSION);
        $nombre = $this->nombreDescarga($config, $tipo, $id, $extension);

        $mimes = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
        $mime = $mimes[strtolower($extension)] ?? 'application/octet-stream';

        $disposicion = $request->boolean('descargar') ? 'attachment' : 'inline';

        return response()->file($ruta, [
            'Content-Type'           => $mime,
            'Content-Disposition'    => $disposicion . '; filename="' . $nombre . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control'          => 'private, max-age=0, must-revalidate',
        ]);
    }

    private function config(string $tipo): array
    {
        abort_unless(isset(self::TIPOS[$tipo]), 404);

        return self::TIPOS[$tipo];
    }

    // Nombre del archivo al descargar: sin caracteres raros, con el id del registro
    private function nombreDescarga(array $config, string $tipo, $id, string $extension): string
    {
        return sprintf('%s-%d-firmado.%s', rtrim($tipo, 's'), (int) $id, preg_replace('/[^a-z0-9]/i', '', $extension));
    }
}
