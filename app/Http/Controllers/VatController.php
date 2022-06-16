<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Vat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VatController extends Controller
{
    // Set VAT value for store
    public function setVat(Request $request)
    {
        // Validation Rules
        $validator = Validator::make(
            $request->all(),
            [
                'store_id' => ['required', 'exists:stores,id'],
                'type' => ['required', 'in:0,1'],
                'value' => ['required', 'between:0,100']
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
            if($store->isVatEnabled()){
                $vatCheck = Vat::where('store_id', $request->store_id)->first();
                if ($vatCheck){
                    Vat::where('id', $vatCheck->id)->update([
                        'type'          => $request->type,
                        'value'         => $request->value
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'VAT updated successfully.'
                    ], 200);
                } else {
                    Vat::create([
                        'store_id'      => $request->store_id,
                        'type'          => $request->type,
                        'value'         => $request->value
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'VAT added successfully.'
                    ], 200);
                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You must enable VAT first',
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
