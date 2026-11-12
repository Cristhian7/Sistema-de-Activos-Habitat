<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Guarda los documentos firmados (traslados y bajas) en el disco privado.
 * Los archivos NO son públicos: solo se entregan por una ruta que exige sesión y permiso.
 */
class DocumentoFirmado
{
    public const DISCO = 'local';                       // storage/app/private
    public const EXTENSIONES = ['pdf', 'jpg', 'jpeg', 'png'];
    public const MAX_KB = 10240;                        // 10 MB

    /**
     * Guarda el archivo y devuelve los datos para la base.
     * $carpeta: "traslados" o "bajas"; $id: id del registro (solo para el nombre del archivo).
     */
    public static function guardar(UploadedFile $archivo, string $carpeta, int $id): array
    {
        // La extensión sale del contenido real del archivo, no del nombre que trae
        $extension = $archivo->guessExtension() ?: 'pdf';
        $nombre = sprintf('%s-%d-%s.%s', rtrim($carpeta, 's'), $id, bin2hex(random_bytes(8)), $extension);

        $ruta = $archivo->storeAs('documentos/' . $carpeta, $nombre, self::DISCO);

        return [
            'documento_firmado' => $ruta,
            'documento_nombre'  => mb_substr($archivo->getClientOriginalName(), 0, 255),
            'documento_fecha'   => now(),
        ];
    }

    public static function existe(?string $ruta): bool
    {
        return !empty($ruta) && Storage::disk(self::DISCO)->exists($ruta);
    }

    public static function rutaAbsoluta(string $ruta): string
    {
        return Storage::disk(self::DISCO)->path($ruta);
    }

    public static function borrarArchivo(?string $ruta): void
    {
        if (!empty($ruta) && Storage::disk(self::DISCO)->exists($ruta)) {
            Storage::disk(self::DISCO)->delete($ruta);
        }
    }
}
