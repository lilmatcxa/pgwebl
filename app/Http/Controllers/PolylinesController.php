<?php

namespace App\Http\Controllers;

use App\Models\PolylinesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class PolylinesController extends Controller
{
    protected $polylines;

    public function __construct()
    {
        $this->polylines = new PolylinesModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Map'
        ];
        return view('map', $data);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function create()
    {
        //
    }
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

        // CREATE IMAGE DIRECTORY IF NOT EXIST
        $imageDirectory = public_path('storage/images');
        if (!File::exists($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true);
        }

        // GET IMAGE FILE
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->move($imageDirectory, $name_image);
        } else {
            $name_image = null;
        }

        // 🔥 Konversi GeoJSON ke geometry
        $geom = DB::selectOne("SELECT ST_SetSRID(ST_GeomFromGeoJSON(?), 4326) AS geom", [
            $request->geom_polyline
        ]);
        // Simpan data
        $data = [
            'geom' => $request->geom_polyline,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
            'user_id' => auth()->user()->id,
        ];

        if (!$this->polylines->create($data)) {
            return redirect()->route('map')->with('error', 'Polyline failed to add');
        }

        return redirect()->route('map')->with('success', 'Polyline has been added');
    }

    public function getPolyline($id)
    {
        $polyline = $this->polylines->find($id);
        return response()->json($polyline);
    }


    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polyline',
            'id' => $id,
        ];

        return view('edit-polyline', $data);
    }

    public function update(Request $request, $id)
    {
        // Validasi request
        $request->validate([
            'name' => 'required|string|unique:polylines,name,' . $id,
            'description' => 'nullable|string',
            'geom_polyline' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        // Cari polyline berdasarkan ID
        $polyline = $this->polylines->find($id);

        if (!$polyline) {
            return redirect()->route('map')->with('error', 'Polyline not found');
        }

        // Simpan nama gambar lama
        $oldImage = $polyline->image;

        // Update data
        $polyline->name = $request->name;
        $polyline->description = $request->description;
        $polyline->geom = $request->geom_polyline;

        // Proses gambar baru jika ada
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . "_polyline." . strtolower($image->getClientOriginalExtension());
            $image->move(public_path('storage/images'), $imageName);
            $polyline->image = $imageName;

            // Hapus gambar lama jika ada
            if ($oldImage && file_exists(public_path('storage/images/' . $oldImage))) {
                unlink(public_path('storage/images/' . $oldImage));
            }
        }


        if (!$polyline->save()) {
            return redirect()->route('map')->with('error', 'Failed to update polyline');
        }

        return redirect()->route('map')->with('success', 'Polyline updated successfully');
    }

    public function destroy(string $id)
    {
        $polyline = $this->polylines->find($id);
        $imagefile = $polyline ? $polyline->image : null;

        if (!$this->polylines->destroy($id)) {
            return redirect()->route('map')->with('error', 'Polyline failed to delete');
        }

        if ($imagefile && file_exists(public_path('storage/images/' . $imagefile))) {
            unlink(public_path('storage/images/' . $imagefile));
        }

        return redirect()->route('map')->with('success', 'Polyline has been deleted');
    }
}
