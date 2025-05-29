<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PolylinesModel extends Model
{
    protected $table = 'polylines';
    protected $primaryKey = 'id';
    public $timestamps = true;

    // Mendapatkan semua polylines dalam format GeoJSON
    public function geojson_polylines()
    {
        $polylines = $this
            ->select(DB::raw('
        polylines.id,
        ST_AsGeoJSON(polylines.geom) AS geom,
        polylines.name,
        polylines.description,
        polylines.image,
        ST_Length(polylines.geom, true) AS length_m,
        CAST(ST_Length(polylines.geom, true) / 1000 AS DOUBLE PRECISION) AS length_km,
        polylines.created_at,
        polylines.updated_at,
        polylines.user_id,
        users.name AS user_created
    '))
            ->leftJoin('users', 'polylines.user_id', '=', 'users.id')
            ->get();


        // $polylines = DB::table($this->table)
        //     ->selectRaw("id,
        //         ST_AsGeoJSON(geom) AS geom,
        //         name,
        //         description, image,
        //         ST_Length(geom, true) AS length_m,
        //         CAST(ST_Length(geom, true) / 1000 AS DOUBLE PRECISION) AS length_km,
        //         created_at,
        //         updated_at
        //     ")
        //     ->get();

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($polylines as $polyline) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($polyline->geom),
                'properties' => [
                    'id' => $polyline->id,
                    'name' => $polyline->name,
                    'description' => $polyline->description,
                    'image' => $polyline->image,
                    'user_id' => $polyline->user_id,
                    'user_created' => $polyline->user_created,
                    'length_m' => $polyline->length_m,
                    'length_km' => round((float) $polyline->length_km, 2),
                    'created_at' => $polyline->created_at,
                    'updated_at' => $polyline->updated_at,
                ]

            ];
            array_push($geojson['features'], $feature);
        }

        return $geojson;
    }

    // Mendapatkan satu polyline berdasarkan ID dalam format GeoJSON
    public function geojson_polyline($id)
    {
        $polyline = DB::table($this->table)
            ->selectRaw("
                id,
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
            ->first();

        if (!$polyline) {
            return null;
        }

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        $feature = [
            'type' => 'Feature',
            'geometry' => json_decode($polyline->geom),
            'properties' => [
                'id' => $polyline->id,
                'name' => $polyline->name,
                'description' => $polyline->description,
                'image' => $polyline->image,
                'length_m' => $polyline->length_m,
                'length_km' => $polyline->length_km,
                'created_at' => $polyline->created_at,
                'updated_at' => $polyline->updated_at,
            ]
        ];

        array_push($geojson['features'], $feature);

        return $geojson;
    }




    // public function geojson_polyline($id)
    // {
    //     $polyline = DB::table($this->table)
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

    //     $geojson = [
    //         'type' => 'FeatureCollection',
    //         'features' => [],
    //     ];

    //     foreach ($polyline as $polyline) {
    //         $feature = [
    //             'type' => 'Feature',
    //             'geometry' => json_decode($polyline->geom),
    //             'properties' => [
    //                 'id' => $polyline->id,
    //                 'name' => $polyline->name,
    //                 'description' => $polyline->description,
    //                 'image' => $polyline->image,
    //                 'length_km' => round((float) $polyline->length_km, 2), // Menampilkan panjang dalam km dengan pembulatan
    //                 'created_at' => $polyline->created_at,
    //                 'updated_at' => $polyline->updated_at,
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
