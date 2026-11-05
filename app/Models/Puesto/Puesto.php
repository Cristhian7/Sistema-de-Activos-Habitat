<?php

namespace App\Models\Puesto;

use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    protected $table = 'puesto';
    protected $primaryKey = 'id_puesto';
    public $timestamps = true;
    protected $fillable = ['nombre_puesto'];
}