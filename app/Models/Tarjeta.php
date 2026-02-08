<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    protected $table = 'tarjeta';

    protected $primaryKey = 'id_tarjeta';

    public $timestamps = false; // si tu tabla no tiene timestamps

    protected $fillable = [
        'id_tarjeta',
        'saldo',
        'id_estado'
    ];
}

