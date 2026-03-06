<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMantenimiento extends Model
{
    protected $table = 'tipo_mantenimiento';

    protected $primaryKey = 'id_tipo_mantenimiento';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}
