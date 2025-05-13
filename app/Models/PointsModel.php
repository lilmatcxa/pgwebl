<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PointsModel extends Model
{
    protected $table = 'points';



    public function geojson_points()
    {
        $points = $this
            ->select(DB::raw('id, ST_AsGeoJSON(geom) AS geom, name, description, image, created_at, updated_at'))
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
                        'image' => $point->image
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
                        'image' => $point->image
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
    ];

}
