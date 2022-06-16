<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    // Set VAT value for store
    public function setShipping(Request $request)
    {
        // Validation Rules
        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => ['required', 'exists:stores,id'],
                'cost' => ['required']
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
            $store = Store::where('id', $request->store_id)->first();
            if($store->isShippingEnabled()){
                $shippingCheck = Shipping::where('store_id', $request->store_id)->first();
                if ($shippingCheck){
                    Shipping::where('id', $shippingCheck->id)->update([
                        'cost'          => $request->cost
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Shipping updated successfully.'
                    ], 200);
                } else {
                    Shipping::create([
                        'store_id'      => $request->store_id,
                        'cost'          => $request->cost
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Shipping added successfully.'
                    ], 200);
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You must enable Shipping first',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You Can Edit only your stores.',
            ]);
        }
    }

}
