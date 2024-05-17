<?php

namespace App\Http\Controllers;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Image as Images;
use Intervention\Image\Facades\Image;


class FrontEndController extends Controller
{
    public function uploadImage(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240', 
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422);
    }

    $images = $request->file('images');
    $uploadedImages = [];

    foreach ($images as $image) {
        list($width, $height) = getimagesize($image);
        if ($width !== $height) {
            return response()->json([
                'status' => 'error',
                'message' => 'One or more images are not square.'
            ], 422);
        }
        $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads'), $fileName);
        $imageRecord = Images::create([
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'extension' => $image->getClientOriginalExtension(),
        ]);
        $uploadedImages[] = [
            'path' => 'uploads/' . $fileName,
            'name' => $imageRecord->name,
            'extension' => $imageRecord->extension,
        ];
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Images uploaded successfully',
        'data' => $uploadedImages
    ], 200);
}


}
