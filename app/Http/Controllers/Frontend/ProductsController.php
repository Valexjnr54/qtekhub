<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::all();
        if (count($products) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Products Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Products Found',
                'products' => $products
            ],200);
        }
    }

    public function singleProduct()
    {
        $id = isset($_GET['product_id']) ? $_GET['product_id'] : '';
        if(!$id){
            return response()->json([
                'status' => 'Request Failed',
                'message' => 'Product Id Required',
            ],422);
        }
        $product = Product::where('id',  "$id")
                    ->orWhere('slug', "$id")
                    ->get();
        if (count($product) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Product Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Product Found',
                'products' => $product
            ],200);
        }
    }

    public function searchProduct(Request $request)
    {
        $this->validate($request,[
            'query' => 'required',
        ]);
        $searchTerm = $request->input('query');
        $productsearch = Product::where(function ($query) use ($searchTerm) {
                                $keywords = explode(' ', $searchTerm);
                                
                                foreach ($keywords as $keyword) {
                                    $query->orWhere('product_name', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('description', 'LIKE', '%' . $keyword . '%');
                                }
                            })->get();
        if (count($productsearch) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Product Search Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'Searched Product Found',
                'product' => $productsearch
            ],200);
        }
    }

    public function productByBrand()
    {
        $id = isset($_GET['brand_id']) ? $_GET['brand_id'] : '';
        if(!$id){
            return response()->json([
                'status' => 'Request Failed',
                'message' => 'Brand Id Required',
            ],422);
        }

        $brand = Brand::where('id',  "$id")
                            ->orWhere('slug', "$id")
                            ->first();
        $brandId = $brand->id;
        $products = Product::where('brand_id',$brandId)->get();
        if (count($products) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Products By Brand Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Products By Brand Found',
                'products' => $products
            ],200);
        }
    }

    public function priceRange(Request $request)
    {
        $query = Product::query();

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        $products = $query->get();

        if (count($products) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Products Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'Price Range',
                'data' => $products
            ],200);
        }
    }

    public function sortPrice()
    {
        if (request()->sort_order == 'low_high') {
            $products = Product::orderBy('price')->get();
        } elseif (request()->sort_order == 'high_low') {
            $products = Product::orderBy('price','desc')->get();
        }
        // return response()->json(['data' => $products]);
        if (count($products) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Products Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Sorted Products Found',
                'products' => $products
            ],200);
        }
    }

    public function brandFilter(Request $request)
    {
        $brandIds = $request->input('brand_ids');
        $products = Product::whereIn('brand_id', $brandIds)->get();
        if (count($products) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Products Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Products Filtered by Brand Found',
                'products' => $products
            ],200);
        }
    }
    
    public function latestProducts(Request $request)
    {
        $product = Product::latest()->inRandomOrder()->take(6)->get();
        // return response()->json($product);
        if (count($product) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Products Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Latest Products Found',
                'product' => $product
            ],200);
        }
    }
    
    public function recommendProducts(Request $request)
    {
        $product = Product::inRandomOrder()->take(6)->get();
        // return response()->json($product);
        if (count($product) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Products Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Recommended Products Found',
                'product' => $product
            ],200);
        }
    }
}
