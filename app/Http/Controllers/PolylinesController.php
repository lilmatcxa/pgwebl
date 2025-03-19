<?php

namespace App\Http\Controllers;

use App\Models\PolylinesModel;
use Illuminate\Http\Request;

class PolylinesController extends Controller
{
    public function __construct()
    {
        $this->polylines = new PolylinesModel();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi request
        $request->validate(
            [
                'name' => 'required|unique:polylines,name',
                'description' => 'required',
                'geom_polyline' => 'required',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exist',
                'description.required' => 'Description is required',
                'geom_polyline.required' => 'Geometry is required',
            ]
        );

        // Simpan data
        $data = [
            'geom' => $request->geom_polyline, // Perbaikan dari geom_point ke geom_polyline
            'name' => $request->name,
            'description' => $request->description,
        ];

        // Simpan ke database
        if (!$this->polylines->create($data)) { // Perbaikan dari $this->points ke $this->polylines
            return redirect()->route('map')->with('error', 'Polyline failed to add');
        }

        // Redirect ke halaman peta
        return redirect()->route('map')->with('success', 'Polyline has been added');
    }
}
