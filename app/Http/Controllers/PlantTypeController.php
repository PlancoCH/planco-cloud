<?php

namespace App\Http\Controllers;

use App\Models\PlantType;
use Illuminate\Http\Request;
use App\Http\Resources\PlantTypeResource;

class PlantTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PlantType::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('common_name', 'like', '%' . $search . '%')
                  ->orWhere('scientific_name', 'like', '%' . $search . '%');
        }

        return PlantTypeResource::collection($query->paginate());
    }

    /**
     * Display the specified resource.
     */
    public function show(PlantType $plantType)
    {
        return new PlantTypeResource($plantType);
    }

    /**
     * Display the image of the specified resource.
     */
    public function image(PlantType $plantType)
    {
        if (!$plantType->standard_image) {
            abort(404, 'No image found for this plant type.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->buffer($plantType->standard_image);

        if (!$mime_type || $mime_type === 'application/x-empty') {
            $mime_type = 'image/jpeg';
        }

        return response($plantType->standard_image)->header('Content-Type', $mime_type);
    }
}
