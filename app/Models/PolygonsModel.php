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
        $polygons = DB::table($this->table)
            ->selectRaw("id,
                ST_AsGeoJSON(geom) AS geom,
                name,
                description, image,
                ST_Area(geom, true) AS area_m2,
                ST_Area(geom, true) / 10000 AS area_ha,
                created_at,
                updated_at
            ")
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
                description, image,
                ST_Length(geom, true) AS length_m,
                CAST(ST_Length(geom, true) / 1000 AS DOUBLE PRECISION) AS length_km,
                created_at,
                updated_at
            ")
            ->where('id', $id)
            ->first();  // Menggunakan first() untuk mendapatkan satu record

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        if ($polygon) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($polygon->geom),
                'properties' => [
                    'id' => $polygon->id,
                    'name' => $polygon->name,
                    'description' => $polygon->description,
                    'image' => $polygon->image,
                    // HAPUS atau ganti dengan yang lain, misal:
                    'luas' => round((float) $polygon->length_km, 2), // jika kamu punya kolom ini
                    'created_at' => $polygon->created_at,
                    'updated_at' => $polygon->updated_at,
                ]
            ];


            array_push($geojson['features'], $feature);
        }

        return $geojson;
    }

    protected $fillable = [
        'geom',
        'name',
        'description',
        'image',
    ];
}
