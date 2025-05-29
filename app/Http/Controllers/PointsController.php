<?php

namespace App\Http\Controllers;

use App\Models\PointsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PointsController extends Controller
{
    protected $points;

    public function __construct()
    {
        $this->points = new PointsModel();
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
        $request->validate(
            [
                'name' => 'required|unique:points,name',
                'description' => 'required',
                'geom_point' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:51200',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exists',
                'description.required' => 'Description is required',
                'geom_point.required' => 'Geometry is required',
            ]
        );

        $imageDirectory = public_path('storage/images');
        if (!File::exists($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true);
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_image = time() . "_point." . strtolower($image->getClientOriginalExtension());
            $image->move($imageDirectory, $name_image);
        } else {
            $name_image = null;
        }

        $data = [
            'geom' => $request->geom_point,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
            'user_id' => auth()->user()->id,
        ];

        if (!$this->points->create($data)) {
            return redirect()->route('map')->with('error', 'Point failed to add');
        }

        return redirect()->route('map')->with('success', 'Point has been added');
    }


    public function show(string $id)
    {
        //
    }
    public function edit(string $id)
    {
        $data = [
            'title' => 'Edit Point',
            'id' => $id,
        ];

        return view('edit-point', $data);
    }

    public function update(Request $request, string $id)
    {
        $request->validate(
            [
                'name' => 'required|unique:points,name,' . $id,
                'description' => 'required',
                'geom_point' => 'required',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:51200',
            ],
            [
                'name.required' => 'Name is required',
                'name.unique' => 'Name already exists',
                'description.required' => 'Description is required',
                'geom_point.required' => 'Geometry is required',
            ]
        );

        $imageDirectory = public_path('storage/images');
        if (!File::exists($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0777, true);
        }

        $point = $this->points->find($id);
        $old_image = $point->image;

        if ($request->hasFile('image')) {
            if ($old_image && file_exists('storage/images/' . $old_image)) {
                unlink('storage/images/' . $old_image);
            }

            $image = $request->file('image');
            $name_image = time() . "_point." . strtolower($image->getClientOriginalExtension());
            $image->move($imageDirectory, $name_image);

            //Delete old image file
            if ($old_image != null) {
                if (file_exists('./storage/images/' . $old_image)) {
                    unlink('./storage/images/' . $old_image);
                }
            }
        } else {
            $name_image = $old_image;
        }

        $data = [
            'geom' => $request->geom_point,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $name_image,
            'user_id' => auth()->user()->id,
        ];

        if (!$point->update($data)) {
            return redirect()->route('map')->with('error', 'Point failed to update');
        }

        return redirect()->route('map')->with('success', 'Point has been added');
    }

    public function destroy(string $id)
    {
        $imagefile = $this->points->find($id)->image;

        if (!$this->points->destroy($id)) {
            return redirect()->route('map')->with('error', 'Point failed to delete');
        }

        if ($imagefile != null) {
            $imagePath = 'storage/images/' . $imagefile;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        return redirect()->route('map')->with('success', 'Point has been deleted');
    }
}
