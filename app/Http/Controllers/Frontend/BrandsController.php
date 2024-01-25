<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        if ($brands == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Brands Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Brands Found',
                'brands' => $brands
            ],200);
        }
    }

    public function singleBrand()
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
        if ($brand == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Brand Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'Brand Found',
                'brand' => $brand
            ],200);
        }
    }

    public function searchBrand(Request $request)
    {
        $this->validate($request,[
            'query' => 'required',
        ]);
        $searchTerm = $request->input('query');
        $brandsearch = Brand::where(function ($query) use ($searchTerm) {
                                $keywords = explode(' ', $searchTerm);
                                
                                foreach ($keywords as $keyword) {
                                    $query->orWhere('brand_name', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('description', 'LIKE', '%' . $keyword . '%');
                                }
                            })->get();
        if (count($brandsearch) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Brand Search Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'Searched Brand Found',
                'brand' => $brandsearch
            ],200);
        }
    }
}
