<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSolicitation extends Model
{
    protected $table = "log_solicitations";

    protected $primaryKey = 'id';

    protected $fillable = [
        'solicitation_id',
        'name',
        'user_id',
        'note',
    ];


}
