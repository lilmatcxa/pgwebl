<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PolygonsModel extends Model
{
    protected $table = 'polygons';
    protected $guarded = ['id'];


    public function geojson_polygons()
    {
        $polygons = $this
            ->select(DB::raw('
            polygons.id,
            ST_AsGeoJSON(polygons.geom) AS geom,
            polygons.name,
            polygons.description,
            polygons.image,
            ST_Area(polygons.geom, true) AS area_m2,
            CAST(ST_Area(polygons.geom, true) / 10000 AS DOUBLE PRECISION) AS area_ha,
            polygons.created_at,
            polygons.updated_at,
            polygons.user_id,
            users.name AS user_created
        '))
            ->leftJoin('users', 'polygons.user_id', '=', 'users.id')
            ->get();

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($polygons as $polygon) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($polygon->geom),
                'properties' => [
                    'id' => $polygon->id,
                    'name' => $polygon->name,
                    'description' => $polygon->description,
                    'image' => $polygon->image,
                    'user_id' => $polygon->user_id,
                    'user_created' => $polygon->user_created,
                    'area_m2' => $polygon->area_m2,
                    'area_ha' => $polygon->area_ha, // Konversi ke hektar
                    'created_at' => $polygon->created_at,
                    'updated_at' => $polygon->updated_at
                ],
            ];

            array_push($geojson['features'], $feature);
        }

        return $geojson;
    }

    public function geojson_polygon($id)
{
    $polygon = DB::table($this->table)
        ->selectRaw("id,
            ST_AsGeoJSON(geom) AS geom,
            name,
            description,
            image,
            ST_Length(geom, true) AS length_m,
            CAST(ST_Length(geom, true) / 1000 AS DOUBLE PRECISION) AS length_km,
            created_at,
            updated_at
        ")
        ->where('id', $id)
        ->first();  // Menggunakan first() untuk mendapatkan satu record

    if (!$polygon) {
        return null;
    }

    $geojson = [
        'type' => 'FeatureCollection',
        'features' => [],
    ];

    $feature = [
        'type' => 'Feature',
        'geometry' => json_decode($polygon->geom),
        'properties' => [
            'id' => $polygon->id,
            'name' => $polygon->name,
            'description' => $polygon->description,
            'image' => $polygon->image,
            'luas' => round((float) $polygon->length_km, 2),
            'created_at' => $polygon->created_at,
            'updated_at' => $polygon->updated_at,
        ],
    ];

    array_push($geojson['features'], $feature);

    return $geojson;
}

    // public function geojson_polygon($id)
    // {
    //     $polygon = DB::table($this->table)
    //         ->selectRaw("id,
    //             ST_AsGeoJSON(geom) AS geom,
    //             name,
    //             description, image,
    //             ST_Length(geom, true) AS length_m,
    //             CAST(ST_Length(geom, true) / 1000 AS DOUBLE PRECISION) AS length_km,
    //             created_at,
    //             updated_at
    //         ")
    //         ->where('id', $id)
    //         ->first();  // Menggunakan first() untuk mendapatkan satu record

    //     if (!$polygon) {
    //         return null;
    //     }
    //     $geojson = [
    //         'type' => 'FeatureCollection',
    //         'features' => [],
    //     ];

    //     foreach ($polygon as $polygon) {
    //         $feature = [
    //             'type' => 'Feature',
    //             'geometry' => json_decode($polygon->geom),
    //             'properties' => [
    //                 'id' => $polygon->id,
    //                 'name' => $polygon->name,
    //                 'description' => $polygon->description,
    //                 'image' => $polygon->image,
    //                 'luas' => round((float) $polygon->length_km, 2),
    //                 'created_at' => $polygon->created_at,
    //                 'updated_at' => $polygon->updated_at,
    //             ]
    //         ];


    //         array_push($geojson['features'], $feature);
    //     }

    //     return $geojson;
    // }

    protected $fillable = [
        'geom',
        'name',
        'description',
        'image',
        'user_id',
        'user_created',
    ];
}
