<?php

namespace App\Support;

use App\Models\Baja\Baja;
use App\Models\Empleado\Empleado;
use App\Models\Estado\Estado;
use App\Models\Notificacion\Notificacion;
use App\Models\Rol\Rol;
use App\Models\Traslado\Traslado;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Avisos de "documento firmado subido" para que se revise el traslado o la baja.
 * Quién los recibe se define en config/permisos.php (clave "notificar", por rol).
 */
class Notificaciones
{
    // ¿El usuario con sesión es de un rol que recibe notificaciones?
    public static function recibe(): bool
    {
        $rol = Permisos::rol();

        return $rol !== null && in_array($rol, config('permisos.notificar', []), true);
    }

    // Id del empleado del usuario con sesión
    public static function empleadoActual(): ?int
    {
        $id = Auth::user()?->empleado_id_empleado;

        return $id ? (int) $id : null;
    }

    // Empleados activos con un rol que recibe notificaciones
    public static function destinatarios(): Collection
    {
        $roles = Rol::all()
            ->filter(fn ($r) => in_array(Str::lower(Str::ascii(trim($r->nombre_rol))), config('permisos.notificar', []), true))
            ->pluck('id_rol');

        return Empleado::whereIn('rol_id_rol', $roles)
            ->where('estado_id_estado', Estado::idActivo())
            ->pluck('id_empleado');
    }

    /**
     * Avisa que se subió (o se reemplazó) el documento firmado de un traslado o de una baja.
     * Si el destinatario ya tenía ese aviso sin revisar, se actualiza en vez de duplicarlo.
     */
    public static function documentoSubido(Traslado|Baja $registro, bool $reemplazo): void
    {
        $esBaja = $registro instanceof Baja;
        $tipo = $esBaja ? Notificacion::TIPO_BAJA : Notificacion::TIPO_TRASLADO;
        $quien = Auth::user()?->empleado;
        $autor = $quien ? trim($quien->nombre_empleado . ' ' . $quien->apellido_empleado) : 'Un usuario';

        $registro->loadMissing($esBaja ? ['equipo'] : ['equipo', 'empleado']);
        $codigo = $registro->equipo->codigo_contable ?? 'N/A';

        $detalle = $esBaja
            ? 'de la baja del equipo ' . $codigo
            : 'del traslado del equipo ' . $codigo . ' a ' . trim(($registro->empleado->nombre_empleado ?? '') . ' ' . ($registro->empleado->apellido_empleado ?? ''));

        $mensaje = Str::limit($autor . ($reemplazo ? ' reemplazó' : ' subió') . ' el documento firmado ' . $detalle . '.', 250, '');

        foreach (self::destinatarios() as $empleadoId) {
            // Quien sube el documento no necesita que le avisen a sí mismo
            if ($quien && (int) $empleadoId === (int) $quien->id_empleado) {
                continue;
            }

            $existente = Notificacion::de($empleadoId)->pendientes()
                ->where('tipo', $tipo)->where('registro_id', $registro->getKey())->first();

            $datos = ['mensaje' => $mensaje, 'creado_por' => $quien?->id_empleado, 'creado_en' => now()];

            $existente
                ? $existente->update($datos)
                : Notificacion::create($datos + [
                    'empleado_id_empleado' => $empleadoId,
                    'tipo'                 => $tipo,
                    'registro_id'          => $registro->getKey(),
                    'revisado_en'          => null,
                ]);
        }
    }

    // Aviso pendiente del usuario actual para un traslado o baja concretos (o null)
    public static function pendienteDe(string $tipo, $registroId): ?Notificacion
    {
        $empleado = self::empleadoActual();

        if (!$empleado) {
            return null;
        }

        return Notificacion::de($empleado)->pendientes()
            ->where('tipo', $tipo)->where('registro_id', $registroId)
            ->orderByDesc('id_notificacion')->first();
    }

    // Si se elimina un traslado, sus avisos ya no tienen a qué apuntar
    public static function eliminarDe(string $tipo, $registroId): void
    {
        Notificacion::where('tipo', $tipo)->where('registro_id', $registroId)->delete();
    }
}
