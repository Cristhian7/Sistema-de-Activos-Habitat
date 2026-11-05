<?php

namespace App\Models\Afiliado;

use Illuminate\Database\Eloquent\Model;

class Afiliado extends Model
{
    protected $table = 'afiliado';
    protected $primaryKey = 'id_afiliado';
    public $timestamps = true;
    protected $fillable = ['nombre_afiliado'];
}