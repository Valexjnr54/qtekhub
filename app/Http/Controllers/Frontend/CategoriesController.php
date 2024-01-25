<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        if ($categories == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Categories Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'All Categories Found',
                'categories' => $categories
            ],200);
        }
    }

    public function singleCategory()
    {
        $id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
        if(!$id){
            return response()->json([
                'status' => 'Request Failed',
                'message' => 'Category Id Required',
            ],422);
        }
        $category = Category::where('id',  "$id")
                    ->orWhere('slug', "$id")
                    ->first();
        if ($category == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Category Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'Category Found',
                'category' => $category
            ],200);
        }
    }

    public function searchCategory(Request $request)
    {
        $this->validate($request,[
            'query' => 'required',
        ]);
        $searchTerm = $request->input('query');
        $categorysearch = Category::where(function ($query) use ($searchTerm) {
                                $keywords = explode(' ', $searchTerm);
                                
                                foreach ($keywords as $keyword) {
                                    $query->orWhere('category_name', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('description', 'LIKE', '%' . $keyword . '%');
                                }
                            })->get();
        if (count($categorysearch) == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Category Search Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'Searched Category Found',
                'category' => $categorysearch
            ],200);
        }
    }

    public function productByCategory()
    {
        $id = isset($_GET['category_item']) ? $_GET['category_item'] : '';
        if(!$id){
            return response()->json([
                'status' => 'Request Failed',
                'message' => 'Category Id Required',
            ],422);
        }
        $category = Category::where('id',  "$id")
                            ->orWhere('slug', "$id")
                            ->first();
        $categoryId = $category->id;
        $products = DB::select('SELECT * FROM `products` INNER JOIN `category_product` ON `category_product`.`category_id` = ? WHERE `category_product`.`product_id` = `products`.`id`', [$categoryId]);
        if ($products == null) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Products Under this Category Not Found',
            ],404);
        } else {
            return response()->json([
                'status' => 'Request Successful',
                'message' => 'Products Under this Category Found',
                'products' => $products
            ],200);
        }
    }
}
