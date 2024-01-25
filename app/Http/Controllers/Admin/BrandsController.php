<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brand = Brand::all();
        return view('admin.brands.brand')->with('brand',$brand);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.brands.create-brand')->with('');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'title' => 'required',
            'url' => 'required',
            'description' => 'required',
            'status' => 'required',
            //'image' => 'image|nullable|max:9999'
        ]);

        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder = 'sales_app/images/brand_images';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }else{
            $fileNameToStore = 'noImage.jpg';
        }

        $slug = Str::slug($request->input('title'));


        $brand = new Brand;
        $brand->brand_name = $request->input('title');
        $brand->brand_url = $request->input('url');
        $brand->description = $request->input('description');
        $brand->slug = $slug;
        $brand->status = $request->input('status');
        $brand->brand_image = $fileNameToStore;
        $brand->save();
        return redirect('./admin/dashboard/brand')->with(['success' =>'Brand has been created Successfully','title'=>'Create Brand']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function show($id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brands.edit-brand')->with('brand',$brand);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'title' => 'required',
            'url' => 'required',
            'description' => 'required',
            'status' => 'required',
        ]);
        if($request->hasFile('image')){

            $file = $request->file('image');
            $folder = 'sales_app/images/brand_images';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }

        $slug = Str::slug($request->input('title'));

        $brand = Brand::find($id);
        $brand->brand_name = $request->input('title');
        $brand->brand_url = $request->input('url');
        $brand->description = $request->input('description');
        $brand->slug = $slug;
        $brand->status = $request->input('status');
        if($request->hasFile('image')){
            $brand->brand_image = $fileNameToStore;
        }
        $brand->save();
        return redirect('./admin/dashboard/brand')->with(['success'=>'Brand has been updated Successfully','title'=>'Update Brand']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);
        $brand->delete();
        return redirect('./admin/dashboard/brand')->with(['success'=>'Brand has been deleted Successfully','title'=>'Delete Brand']);
    }
}
