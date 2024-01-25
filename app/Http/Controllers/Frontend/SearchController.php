<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchAll(Request $request){
        $this->validate($request,[
            'query' => 'required',
        ]);
        $searchTerm = $request->input('query');
        
        // Perform the search query on multiple tables
        $results = [];

        $categorysearch = Category::where(function ($query) use ($searchTerm) {
            $keywords = explode(' ', $searchTerm);
            
            foreach ($keywords as $keyword) {
                $query->orWhere('category_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('description', 'LIKE', '%' . $keyword . '%');
            }
        })->get();
$results = array_merge($results, $categorysearch->toArray());

$brandsearch = Brand::where(function ($query) use ($searchTerm) {
    $keywords = explode(' ', $searchTerm);
    
    foreach ($keywords as $keyword) {
        $query->orWhere('brand_name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
            ->orWhere('description', 'LIKE', '%' . $keyword . '%');
    }
})->get();
$results = array_merge($results, $brandsearch->toArray());
        
        $productsearch = Product::where(function ($query) use ($searchTerm) {
                                $keywords = explode(' ', $searchTerm);
                                foreach ($keywords as $keyword) {
                                    $query->orWhere('product_name', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('slug', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('description', 'LIKE', '%' . $keyword . '%');
                                }
                            })->get();
        $results = array_merge($results, $productsearch->toArray());
        
        return response()->json(['products' => $productsearch,'categories' => $categorysearch, 'brand' => $brandsearch],200);
        
    }
}
