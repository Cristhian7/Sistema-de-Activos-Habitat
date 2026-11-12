<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Permisos según el rol del usuario con sesión.
 *
 * Rol del usuario = login -> empleado -> rol. La matriz de permisos está en config/permisos.php.
 */
class Permisos
{
    // Clave del rol del usuario actual ("administrador", "analista", "tecnico") o null si no tiene
    public static function rol(): ?string
    {
        $usuario = Auth::user();

        if (!$usuario) {
            return null;
        }

        $nombre = $usuario->empleado?->rol?->nombre_rol;

        return $nombre ? Str::lower(Str::ascii(trim($nombre))) : null;
    }

    // ¿El usuario actual tiene este permiso? ("equipos.editar", "reportes.ver", ...)
    public static function puede(string $permiso): bool
    {
        $lista = config('permisos.roles.' . self::rol(), []);

        return in_array('*', $lista, true) || in_array($permiso, $lista, true);
    }

    // ¿Tiene al menos uno de estos permisos?
    public static function puedeAlguno(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            if (self::puede($permiso)) {
                return true;
            }
        }

        return false;
    }

    // Nombre de la ruta a la que se envía al usuario al entrar (la primera pantalla que puede ver)
    public static function rutaInicio(): ?string
    {
        foreach (config('permisos.inicio', []) as $permiso => $ruta) {
            if (self::puede($permiso)) {
                return $ruta;
            }
        }

        return null;
    }
}
