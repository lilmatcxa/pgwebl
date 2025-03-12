<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointsModel extends Model
{
    protected $table = 'points';

    protected $guarded = ['id'];

    protected $fillable = [
        'geom',
        'name',
        'description',
    ];

}


