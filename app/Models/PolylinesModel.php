<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PolylinesModel extends Model
{
    protected $table = 'polylines';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function geojson_polylines()
    {
        $polylines = DB::table($this->table)
            ->selectRaw("
                ST_AsGeoJSON(geom) AS geom,
                name,
                description,
                ST_Length(geom, true) AS length_m,
                CAST(ST_Length(geom, true) / 1000 AS DOUBLE PRECISION) AS length_km,
                created_at,
                updated_at
            ")
            ->get();

        return [
            'type' => 'FeatureCollection',
            'features' => collect($polylines)->map(function ($polyline) {
                return [
                    'type' => 'Feature',
                    'geometry' => json_decode($polyline->geom),
                    'properties' => [
                        'name' => $polyline->name,
                        'description' => $polyline->description,
'length_km' => round((float) $polyline->length_km, 2),
                        'length_km' => (float) $polyline->length_km,
                        'created_at' => $polyline->created_at,
                        'updated_at' => $polyline->updated_at
                    ],
                ];
            })->toArray(),
        ];
    }

    protected $fillable = [
        'geom',
        'name',
        'description',
    ];
}
