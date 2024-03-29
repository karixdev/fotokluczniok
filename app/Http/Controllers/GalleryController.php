<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gallery;
use \Validator;
use App\Http\Resources\GalleriesAvailableToAssign;
use App\Http\Resources\IndexGallery as GalleryResource;
use App\Http\Resources\IndexGalleryCollection;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getPublic']]);
    }

    public function getPublic(Gallery $gallery)
    {
        return response()->json([
            'gallery' => new GalleryResource($gallery),
            'galleries' => new IndexGalleryCollection(Gallery::where('private', '=', '0')->get()),
        ]);
    }

    public function getAll()
    {
        return response()->json(Gallery::all());
    }

    public function store(Request $request)
    {
        $validator = $this->getValidator($request->all());
        if ($validator->fails()) return response()->json([$validator->errors()], 406);

        $group = new Gallery();
        $group->name = $request->input('name');
        $group->private = $request->input('private');
        $group->save();

        return response()->json($group);
    }

    public function getValidator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string'],
            'private' => ['required', 'boolean'],
        ]);

        return $validator;
    }

    public function get(Gallery $gallery)
    {
        $gallery['images'] = $gallery->images;
        $gallery['files'] = $gallery->files;
        return response()->json($gallery);
    }

    public function deassign(Gallery $gallery)
    {
        $gallery->photography_id = null;
        $gallery->save();

        return [
            'id' => $gallery->id,
            'to_assign' => new GalleriesAvailableToAssign($gallery),
        ];
    }

    public function delete(Gallery $gallery)
    {
        if (! $gallery->delete()) return response()->json(['error' => 'Could not delete gallery'], 500);

        return response()->json(['message' => 'success']);
    }
}
