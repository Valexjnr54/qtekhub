<?php

namespace App\Http\Controllers\Customer\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WishlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addToWishlist(Request $request) {
        $customer = Auth::guard('api')->user(); // Change 'user' to 'customer'
        $this->validate($request,[
            'product_id' => 'required'
        ]);

        if(!Product::where(['id' => $request->product_id])->exists()){
            return response()->json(['message' => 'Product not found'], 404);
        }
    
        // Check if the product is already in the customer's wishlist
        if ($customer->wishlistItems()->where('product_id', $request->product_id)->exists()) {
            return response()->json(['message' => 'Product already in wishlist'], 400);
        }
    
        $wishlistItem = new WishlistItem();
        $wishlistItem->customer_id = $customer->id; // Change 'user_id' to 'customer_id'
        $wishlistItem->product_id = $request->product_id;
        $wishlistItem->save();
    
        return response()->json(['message' => 'Product added to wishlist','item' => $wishlistItem]);
    }
    
    public function removeFromWishlist(Request $request) {
        $customer = Auth::guard('api')->user(); // Use 'guard' to specify the 'api' guard
        $this->validate($request,[
            'product_id' => 'required'
        ]);

        // Find the WishlistItem based on the ID
        $wishlistItem = WishlistItem::where(['product_id' => $request->product_id,'customer_id' => $customer->id])->first();

        if (!$wishlistItem) {
            return response()->json(['message' => 'Wishlist not found'], 400);
        }

        // Ensure the customer owns the wishlist item
        if ($wishlistItem->customer_id != $customer->id) {
            return response()->json(['message' => 'Unauthorized'], 400);
        }
    
        $wishlistItem->delete();
    
        return response()->json(['message' => 'Product removed from wishlist']);
    }
    
    public function getWishlist() {
        $customer = Auth::guard('api')->user();
        $wishlist = $customer->wishlistItems()->with('product')->get();
    
        return response()->json(['wishlist' => $wishlist]);
    }
    
}
