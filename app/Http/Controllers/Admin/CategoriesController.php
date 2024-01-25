<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::all();
        return view('admin.categories.category')->with('category',$category);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create-category')->with('');
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
            'description' => 'required',
            'status' => 'required',
            //'image' => 'image|nullable|max:9999'
        ]);

        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder = 'sales_app/images/category_images';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }else{
            $fileNameToStore = 'noImage.jpg';
        }

        $slug = Str::slug($request->input('title'));


        $category = new Category;
        $category->category_name = $request->input('title');
        $category->description = $request->input('description');
        $category->slug = $slug;
        $category->status = $request->input('status');
        $category->category_image = $fileNameToStore;
        $category->save();
        return redirect('./admin/dashboard/category')->with(['success' =>'Category has been created Successfully','title'=>'Create Category']);
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
        $category = Category::find($id);
        return view('admin.categories.edit-category')->with('category',$category);
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
            'description' => 'required',
            'status' => 'required',
        ]);
        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder = 'sales_app/images/category_images';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }

        $slug = Str::slug($request->input('title'));

        $category = Category::find($id);
        $category->category_name = $request->input('title');
        $category->description = $request->input('description');
        $category->slug = $slug;
        $category->status = $request->input('status');
        if($request->hasFile('image')){
            $category->category_image = $fileNameToStore;
        }
        $category->save();
        return redirect('./admin/dashboard/category')->with(['success'=>'Category has been updated Successfully','title'=>'Update Category']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        return redirect('./admin/dashboard/category')->with(['success'=>'Category has been deleted Successfully','title'=>'Delete Category']);
    }
}
