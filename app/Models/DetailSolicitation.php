<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailSolicitation extends Model
{
    protected $table = "detail_solicitations";

    protected $primaryKey = 'id';

    protected $fillable = [
        'solicitation_id',
        'name',
        'last_name',
        'department',
        'registration',
        'photo'
    ];
}
