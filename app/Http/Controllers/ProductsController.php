<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shipping;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    // Set Shipping value for store
    public function setProduct(Request $request)
    {
        // Validation Rules
        $validator = Validator::make(
            $request->all(),
            [
                'en_name' => ['required'],
                'ar_name' => ['required'],
                'en_description' => ['required'],
                'ar_description' => ['required'],
                'store_id' => ['required', 'exists:stores,id'],
                'price' => ['required', 'integer']
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

        // check if the user own the target store
        $userStores = auth()->user()->stores->pluck('id')->toArray();
        if (in_array($request->store_id, $userStores)) {
            // if there is no validation errors
            Product::create([
                'en_name'          => $request->en_name,
                'ar_name'          => $request->ar_name,
                'en_description'   => $request->en_description,
                'ar_description'   => $request->ar_description,
                'price'            => $request->price,
                'store_id'         => $request->store_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added successfully.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You Can Add Products to your stores only.',
            ]);
        }
    }

}
