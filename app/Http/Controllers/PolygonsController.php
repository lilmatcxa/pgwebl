<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolygonsModel;
use Illuminate\Support\Facades\File;

class PolygonsController extends Controller
{
    protected $polygons;
    public function __construct()
    {
        $this->polygons = new PolygonsModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Map'
        ];
        return view('map', $data);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        // Validasi request
        $request->validate(
            [
                'name' => 'required|unique:polygons,name',
                'description' => 'required',
                'geom_polygon' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:5000',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exist',
                'description.required' => 'Description is required',
                'geom_polygon.required' => 'Geometry is required',
            ]
        );

        // Buat folder jika belum ada
        $imageDirectory = public_path('storage/images');
        if (!File::exists($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true);
        }

        // Ambil gambar jika ada
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->move($imageDirectory, $name_image);
        } else {
            $name_image = null;
        }

        // Simpan data
        $data = [
            'geom' => $request->geom_polygon,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
            'user_id' => auth()->user()->id,
        ];

        if (!$this->polygons->create($data)) {
            return redirect()->route('map')->with('error', 'Polygon failed to add');
        }

        return redirect()->route('map')->with('success', 'Polygon has been added');
    }

    public function getPolygon($id)
    {
        $polygon = $this->polygons->find($id);
        return response()->json($polygon);
    }

    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Polygon',
            'id' => $id,
        ];

        return view('edit-polygon', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:polygons,name,' . $id,
            'description' => 'nullable|string',
            'geom_polygon' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        $polygon = $this->polygons->find($id);

        if (!$polygon) {
            return redirect()->route('map')->with('error', 'Polygon not found');
        }

        $oldImage = $polygon->image;

        $polygon->name = $request->name;
        $polygon->description = $request->description;
        $polygon->geom = $request->geom_polygon;

        // Jika ada gambar baru
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . "_polygon." . strtolower($image->getClientOriginalExtension());
            $image->move(public_path('storage/images'), $imageName);
            $polygon->image = $imageName;

            // Hapus gambar lama jika ada
            if ($oldImage && file_exists(public_path('storage/images/' . $oldImage))) {
                unlink(public_path('storage/images/' . $oldImage));
            }
        }

        if (!$polygon->save()) {
            return redirect()->route('map')->with('error', 'Polygon failed to update');
        }

        return redirect()->route('map')->with('success', 'Polygon updated successfully');
    }

    public function destroy(string $id)
    {
        $polygon = $this->polygons->find($id);
        $imagefile = $polygon ? $polygon->image : null;

        if (!$this->polygons->destroy($id)) {
            return redirect()->route('map')->with('error', 'Polygon failed to delete');
        }

        if ($imagefile && file_exists(public_path('storage/images/' . $imagefile))) {
            unlink(public_path('storage/images/' . $imagefile));
        }

        return redirect()->route('map')->with('success', 'Polygon has been deleted');
    }

    public function apiPolygon($id)
    {
        $polygon = $this->polygons->find($id);

        if (!$polygon) {
            // Jika tidak ditemukan, kembalikan feature collection kosong
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => []
            ]);
        }

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
            ]
        ];

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => [$feature]
        ]);
    }
}
