<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use id;

class PointsModel extends Model
{
    protected $table = 'points';



    public function geojson_points()
{
    $points = $this
        ->select(DB::raw('
            points.id,
            ST_AsGeoJSON(points.geom) AS geom,
            points.name,
            points.description,
            points.image,
            points.created_at,
            points.updated_at,
            points.user_id,
            users.name as user_created
        '))
        ->leftJoin('users', 'points.user_id', '=', 'users.id')
        ->get();

    $geojson = [
        'type' => 'FeatureCollection',
        'features' => [],
    ];

    foreach ($points as $point) {
        $feature = [
            'type' => 'Feature',
            'geometry' => json_decode($point->geom),
            'properties' => [
                'id' => $point->id,
                'name' => $point->name,
                'description' => $point->description,
                'created_at' => $point->created_at,
                'updated_at' => $point->updated_at,
                'image' => $point->image,
                'user_id' => $point->user_id,
                'user_created' => $point->user_created,
            ],
        ];

        array_push($geojson['features'], $feature);
    }
    return $geojson;
}

    public function geojson_point($id)
    {
        $points = $this
            ->select(DB::raw('id, ST_AsGeoJSON(geom) AS geom, name, description, image, created_at, updated_at'))
            ->where('id', $id)
            ->get();

        $geojson = [
            'type' => 'FeatureCollection',
            'features' => [],
        ];

        foreach ($points as $point) {
            $feature = [
                'type' => 'Feature',
                'geometry' => json_decode($point->geom),
                'properties' => [
                    'id' => $point->id,
                    'name' => $point->name,
                    'description' => $point->description,
                    'created_at' => $point->created_at,
                    'updated_at' => $point->updated_at,
                    'image' => $point->image,
                ],
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
        'user_id',
        'user_created',
    ];
}
