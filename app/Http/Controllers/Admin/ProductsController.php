<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
        /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::all();
        return view('admin.products.product')->with('product',$product);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('admin.products.create-product')->with(['categories'=>$categories,'brands'=>$brands]);
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
            'sku' => 'required',
            'content' => 'required',
            'description' => 'required',
            'price' => 'required',
            'sales_price' => 'nullable',
            'stock' => 'required',
            'product_category' => 'required',
            'product_brand' => 'required',
            'status' => 'required',
            'image' => 'image|nullable'
        ]);



        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder = 'sales_app/images/product_images';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }else{
            $fileNameToStore = 'noImage.jpg';
        }
        $slug = Str::slug($request->input('title'));


        $product = new Product;
        $product->product_name = $request->input('title');
        $product->description = $request->input('description');
        $product->content = $request->input('content');
        $product->slug = $slug;
        $product->sku = $request->input('sku');
        $product->price = $request->input('price');
        $product->sales_price = $request->input('sales_price');
        $product->stock_status = $request->input('stock');
        $product->status = $request->input('status');
        $product->brand_id= $request->input('product_brand');
        $product->product_image = $fileNameToStore;
        $product->save();

        // $productDetail = Product::latest()->first();
        $productId = Product::all()->last()->id;

        if ($request->has('product_category')) {
            $input_array = $request->input('product_category');
            foreach ($input_array as $value) {
                $categoryProduct = new CategoryProduct();
                $categoryProduct->product_id = $productId;
                $categoryProduct->category_id = $value;
                $categoryProduct->save();
            }
        }

        $galleries = $request->file('galleries');
        foreach ($galleries as $gallery) {
            $file = $gallery;
            $folder = 'sales_app/images/product_galleries';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);
            $imageURL = $uploadedFile->getSecurePath();
            $productImage = new ProductImage();
            $productImage->product_id = $productId;
            $productImage->product_image = $imageURL;
            $productImage->save();
        }

        return redirect('./admin/dashboard/product')->with(['success' =>'Product has been created Successfully','title'=>'Create Product']);
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

        $product = Product::find($id);
        $categories = Category::all();
        $brands = Brand::all();
        $cate_products = CategoryProduct::where('product_id',$id)->get();
        $cates = [];
        $cates_id = [];
        foreach ($cate_products as $cate_product) {
            $cat = Category::where('id',$cate_product['category_id'])->first();
            $cate_name = $cat->category_name;
            $cates[]=$cate_name;
            $cates_id[] = $cate_product['category_id'];
        }

        $combinedCates = implode(', ', $cates);
        $combinedCatesId = implode(', ', $cates_id);
        return view('admin.products.edit-product')->with(['product'=>$product,'categories'=>$categories,'brands'=>$brands,'cates'=>$combinedCates, 'cates_id' => $combinedCatesId]);
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
            'sku' => 'required',
            'content' => 'required',
            'description' => 'required',
            'price' => 'required',
            'sales_price' => 'nullable',
            'stock' => 'required',
            'product_category' => 'required',
            'product_brand' => 'required',
            'status' => 'required'
        ]);

        if($request->hasFile('image')){
            $file = $request->file('image');
            $folder = 'sales_app/images/product_images';
            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                'folder' => $folder
            ]);

            $fileNameToStore = $uploadedFile->getSecurePath();
        }
        $slug = Str::slug($request->input('title'));
        $product = Product::find($id);
        $product->product_name = $request->input('title');
        $product->description = $request->input('description');
        $product->content = $request->input('content');
        $product->slug = $slug;
        $product->sku = $request->input('sku');
        $product->price = $request->input('price');
        $product->sales_price = $request->input('sales_price');
        $product->stock_status = $request->input('stock');
        $product->status = $request->input('status');
        $product->brand_id = $request->input('product_brand');
        if($request->hasFile('image')){
            $product->product_image = $fileNameToStore;
        }
        $product->save();

        $categoryProduct = CategoryProduct::where('product_id',$id)->first();
        $categoryProductProductId = $categoryProduct->product_id;
        $categoryProductId = $categoryProduct->id;

        $productImage = ProductImage::where('product_id',$id)->first();
        $productImageProductId =  $productImage->product_id;
        $productImageId = $productImage->id;


        if ($request->has('product_category')) {
            if ($id == $categoryProductProductId) {
                $input_array = $request->input('product_category');
                foreach ($input_array as $value) {
                    if (CategoryProduct::where('category_id','=',$value)->exists()) {
                    } else {
                        $catego = CategoryProduct::find($categoryProductId);
                        $catego->category_id = $value;
                        $catego->save();
                    }
                }
            }
        }

        // if ($request->hasFile('galleries')) {
        //     if ($id == $productImageProductId) {
        //         $galleries = $request->file('galleries');
        //         foreach ($galleries as $gallery) {
        //             # code...
        //         }
        //     }
        // }

        return redirect('./admin/dashboard/product')->with(['success'=>'Product has been updated Successfully','title'=>'Update Product']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        $cate_products = CategoryProduct::where('product_id',$id)->get();
        foreach ($cate_products as $cate_product) {
            $cate_product->delete();
        }
        $image_products = ProductImage::where('product_id',$id)->get();
        foreach ($image_products as $image_product) {
            $image_product->delete();
        }
        return redirect('./admin/dashboard/product')->with(['success'=>'Product has been deleted Successfully','title'=>'Delete Product']);
    }

    public function exportProductByCategory()
    {
        $role = Auth::user()->role;
        if($role == '1' || $role == '2'){
            $category = Category::all();
            return view('admin.products.export')->with('categories',$category);
        }
    }
    
    public function fetchProductByCategory()
    {
        $role = Auth::user()->role;
        if($role == '1' || $role == '2'){
            $category_id = (int)$_GET['category_id'];
            $categoryProducts = CategoryProduct::with('product')->where('category_id', $category_id)->get();
            $products = $categoryProducts->pluck('product')->all();
            /*return $products;*/
            return view('admin.products.products-export')->with('products',$products);
        }
    }
}
