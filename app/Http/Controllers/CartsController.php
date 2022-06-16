<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\Vat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartsController extends Controller
{
    // Add To Cart Function
    public function addToCart(Request $request)
    {
        // Validation Rules
        $validator = Validator::make(
            $request->all(),
            [
                'product_id' => ['required', 'exists:products,id'],
                'quantity' => ['required', 'integer']
            ]
        );

        // if validator fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix errors.',
                'errors' => $validator->errors()
            ]);
        }

        // if no validations errors
        $product = Product::where('id', $request->product_id)->first();
        if($product->store->isVatEnabled()){
            $vat = Vat::where('store_id', $product->store->id)->first();
            $price = $product->price - ($product->price * ($vat->value/100));
        } else {
            $price = $product->price;
        }
        // check if product is added before to the cart increase the quantity to the same record
        $cartCheck = Cart::where('product_id', $request->product_id)->where('user_id', auth()->id())->first();
        if ($cartCheck){
            Cart::where('id', $cartCheck->id)->update([
                'quantity' => $cartCheck->quantity + $request->quantity
            ]);
        } else {
            Cart::create([
                'product_id' => $request->product_id,
                'user_id'   => auth()->id(),
                'quantity' => $request->quantity,
                'total_price' => $price
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Added to Cart Successfully!.'
        ], 200);
    }

    // Calculate Cart Total
    public function calculateCart()
    {
        $carts = Cart::where('user_id', auth()->id())->get();
        $total = 0;
        foreach ($carts as $cart)
        {
            $total += $cart->total_price * $cart->quantity;
        }
        $shipping = Shipping::where('store_id', 1)->first();
        if($shipping){
            $total = $total + $shipping->cost;
        }
        return response()->json([
            'success' => true,
            'totalPrice' => $total
        ], 200);
    }
}
