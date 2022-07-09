<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;

class ImageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $images = Image::all();
        return response()->json([
            'status' => 'success',
            'todos' => $images,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            '$image' => 'required|string',
        ]);

        $imagePathWithName='';
        if ($request->hasFile('image')){
            $rand_val           = date('YMDHIS') . rand(11111, 99999);
            $image_file_name    = md5($rand_val);
            $file               = $request->file('image');
            $fileName           = $image_file_name.'.'.$file->getClientOriginalExtension();
            $destinationPath    = public_path().'/images';
            $file->move($destinationPath,$fileName);
            $imagePathWithName    = $fileName ;
        }
        

        $image = Image::create([
            'title' => $request->title,
            'image' => $imagePathWithName,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Image created successfully',
            '$image' => $image,
        ]);
    }

    public function show($id)
    {
        $image = Image::find($id);
        return response()->json([
            'status' => 'success',
            'image' => $image,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'image' => 'required|string',
        ]);
        $imagePathWithName='';
        if ($request->hasFile('image')){
            $rand_val           = date('YMDHIS') . rand(11111, 99999);
            $image_file_name    = md5($rand_val);
            $file               = $request->file('image');
            $fileName           = $image_file_name.'.'.$file->getClientOriginalExtension();
            $destinationPath    = public_path().'/images';
            $file->move($destinationPath,$fileName);
            $imagePathWithName    = $fileName ;
        }
        $image = Image::find($id);
        if($image)
        {
            $filename = public_path().'/images/'.$image->image;
            $image->title = $request->title;
            $image->image =$imagePathWithName;
            $image->save();

            
            if (file_exists($filename)) {
                unlink($filename);
            }
    
            return response()->json([
                'status' => 'success',
                'message' => 'Image updated successfully',
                'image' => $image,
            ]);
        }
        
    }

    public function destroy($id)
    {
        $image = Image::find($id);
        if($image)
        {
            $filename = public_path().'/images/'.$image->image;
            if (file_exists($filename)) {
                unlink($filename);
            }
            $image->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Image deleted successfully'
            ]);
        }
        

        
    }
}
