<?php

namespace App\Models\Estado;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estado';
    protected $primaryKey = 'id_estado';
    public $timestamps = false;

    protected $fillable = ['nombre_estado'];

    // Nombres de los estados tal como están en la tabla
    public const ACTIVO = 'Activo';
    public const BAJA = 'Baja';

    // Caché en memoria para no consultar la tabla en cada llamada
    private static array $cacheIds = [];

    /**
     * Devuelve el id del estado por su nombre, leyéndolo de la tabla.
     * Así los ids pueden cambiar o agregarse nuevos estados sin tocar el código.
     */
    public static function idPorNombre(string $nombre): ?int
    {
        if (array_key_exists($nombre, self::$cacheIds)) {
            return self::$cacheIds[$nombre];
        }

        $id = static::where('nombre_estado', $nombre)->value('id_estado');

        return self::$cacheIds[$nombre] = $id !== null ? (int) $id : null;
    }

    // Id requerido: si el estado no existe en la tabla, falla con un mensaje claro
    private static function idRequerido(string $nombre): int
    {
        $id = static::idPorNombre($nombre);

        if ($id === null) {
            throw new \RuntimeException("No existe el estado '{$nombre}' en la tabla estado.");
        }

        return $id;
    }

    // Ids que usa la lógica al GUARDAR estados
    public static function idActivo(): int
    {
        return static::idRequerido(self::ACTIVO);
    }

    public static function idBaja(): int
    {
        return static::idRequerido(self::BAJA);
    }

    // Comparaciones seguras para las vistas y validaciones (no lanzan error)
    public static function esActivo($idEstado): bool
    {
        $id = static::idPorNombre(self::ACTIVO);

        return $idEstado !== null && $id !== null && (int) $idEstado === $id;
    }

    public static function esBaja($idEstado): bool
    {
        $id = static::idPorNombre(self::BAJA);

        return $idEstado !== null && $id !== null && (int) $idEstado === $id;
    }

    // Color de la insignia: verde = Activo, rojo = Baja, gris = cualquier otro estado nuevo
    public function claseBadge(): string
    {
        if (static::esActivo($this->id_estado)) {
            return 'bg-success';
        }

        if (static::esBaja($this->id_estado)) {
            return 'bg-danger';
        }

        return 'bg-secondary';
    }

    // Relación con empleados
    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'estado_id_estado', 'id_estado');
    }
}

