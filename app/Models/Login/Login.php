<?php

namespace App\Models\Login;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Login extends Authenticatable
{
    use Notifiable;

    protected $table = 'login';
    protected $primaryKey = 'id_login';
    
    public $timestamps = false;

    protected $fillable = [
        'usuario',
        'password',
        'empleado_id_empleado',
    ];

    protected $hidden = [
        'password',
    ];

    // Relación con empleado
    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class, 'empleado_id_empleado', 'id_empleado');
    }
}