<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoresController extends Controller
{
    // Add Store For users
    public function addStore(Request $request)
    {
        // Validation Rules
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'min:3', 'max:50'],
                'phone' => ['required', 'regex:/((10)|(11)|(12)|(15)|(010)|(011)|(012)|(015))[0-9]{8}/'],
                'address' => ['required'],
                'VAT' => ['required', 'in:0,1'],
                'shipping' => ['required', 'in:0,1']
            ],
            [
                'phone.regex' => 'the input Phone must be Egyptian number'
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

        // if there is no validation errors
        if(auth()->user()->isVendor()){
            $storeCheck = Store::where('name', $request->name)->where('user_id', auth()->id())->first();
            if($storeCheck){
                Store::where('id', $storeCheck->id)->update([
                    'name'      => $request->name,
                    'VAT'       => $request->VAT,
                    'phone'     => $request->phone,
                    'address'   => $request->address,
                    'shipping'  => $request->shipping
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Store updated succesfully.'
                ], 200);
            } else {
                Store::create([
                    'name'      => $request->name,
                    'user_id'   => auth()->id(),
                    'VAT'       => $request->VAT,
                    'phone'     => $request->phone,
                    'address'   => $request->address,
                    'shipping'  => $request->shipping
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Store added succesfully.'
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Vendors Only can add stores.',
            ]);
        }
    }
}
