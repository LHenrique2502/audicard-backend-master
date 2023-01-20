<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use YourAppRocks\EloquentUuid\Traits\HasUuid;

class Solicitation extends Model
{

    use HasUuid;

    protected $table = 'solicitations';

    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'client_id',
        'user_id',
        'type_card',
        'freight',
        'protocol',
        'note',
        'status',
        'id'
    ];


}
