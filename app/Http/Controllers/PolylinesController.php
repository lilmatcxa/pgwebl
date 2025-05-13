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
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:1024',

            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exist',
                'description.required' => 'Description is required',
                'geom_polyline.required' => 'Geometry is required',
            ]
        );

        //CREATE  IMAGE DIRECTOR IF NOT EXIST -PGWEBL 7
        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777);
        }

        //GET IMAGE FILE - PGWEBL 7
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
            //$image->storeAs('public/images', $name_image);

        } else {
            $name_image = null;
        }


        // Simpan data
        $data = [
            'geom' => $request->geom_polyline,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
        ];
        // Simpan ke database
        if (!$this->polylines->create($data)) {
            return redirect()->route('map')->with('error', 'Polyline failed to add');
        }
        // Redirect ke halaman peta
        return redirect()->route('map')->with('success', 'Polyline has been added');

    }

    public function destroy(string $id)
    {
        $imagefile = $this->polylines->find($id)->image;

        if (!$this->polylines->destroy($id)) {
            return redirect()->route('map')->with('error', 'Polyline failed to delete');
        }

        //Delete Image File
        if ($imagefile !=null) {
            unlink('storage/images/'.$imagefile);
        }

        return redirect()->route('map')->with('success', 'Polyline has been deleted');

    }
}
