<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolygonsModel;

class PolygonsController extends Controller
{
    public function __construct()
    {
        $this->polygons = new PolygonsModel();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi request
        $request->validate(
            [
                'name' => 'required|unique:polygons,name',
                'description' => 'required',
                'geom_polygon' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exist',
                'description.required' => 'Description is required',
                'geom_polygon.required' => 'Geometry is required',
            ]
        );


        //CREATE  IMAGE DIRECTOR IF NOT EXIST -PGWEBL 7
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);
        }

        //GET IMAGE FILE - PGWEBL 7
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygons." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
            //$image->storeAs('public/images', $name_image);

        } else {
            $name_image = null;
        }



        // Simpan data
        $data = [
            'geom' => $request->geom_polygon, // Perbaikan dari geom_point ke geom_polyline
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
        ];

        // Simpan ke database
        if (!$this->polygons->create($data)) { // Perbaikan dari $this->points ke $this->polylines
            return redirect()->route('map')->with('error', 'Polygon failed to add');
        }

        // Redirect ke halaman peta
        return redirect()->route('map')->with('success', 'Polygon has been added');
    }

    public function destroy(string $id)
    {
        $imagefile = $this->polygons->find($id)->image;

        if (!$this->polygons->destroy($id)) {
            return redirect()->route('map')->with('error', 'Polygons failed to delete');
        }

        //Delete Image File
        if ($imagefile !=null) {
            unlink('storage/images/'.$imagefile);
        }

        return redirect()->route('map')->with('success', 'Polygons has been deleted');

    }
}
