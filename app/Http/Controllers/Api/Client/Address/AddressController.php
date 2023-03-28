<?php

namespace App\Http\Controllers\Api\Client\Address;

use App\Models\Addresses;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\Client\Controller;

class AddressController extends Controller
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->middleware('auth:api');
    }

    public function index(Request $request)
    {
        $user = auth('api')->user();

        $addresses = Addresses::with(['region', 'state'])->where('is_archived', 0)->where('user_id', $user->id)->get();

        return response()->json(['addresses' => AddressResource::collection($addresses)]);
    }

    public function store(AddressRequest $request)
    {
        $user = auth('api')->user();
        $addresses_count = Addresses::where('user_id', $user->id)->count();
        Addresses::create($request->validated() + ['user_id' => $user->id, 'is_home' => $addresses_count == 0 ? 1 : 0]);
        return response()->json(['message' => __('messages.The address has been added successfully')]);
    }

    public function update(AddressRequest $request, $address_id)
    {
        $user = auth('api')->user();
        $address = Addresses::where('id', $address_id)->where('user_id', $user->id)->first();
        if (!$address) {
            return response()->json(['message' => __('messages.There is no address with this number'), 'status' => 400,], 400);
        }
        $address->update($request->validated());
        return response()->json(['message' => __('messages.The address has been updated successfully')]);
    }

    public function destroy(Request $request)
    {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()], 400);
        }

        $address = Addresses::where('id', $request->address_id)->where('user_id', $user->id)->first();
        if (!$address) {
            return response()->json(['message' => __('messages.There is no address with this number'), 'status' => 400,], 400);
        }
        $address->is_archived = 1;
        $address->save();
        return response()->json(['message' => __('messages.The address has been deleted successfully')]);
    }

    public function setDefaultAddress(Request $request)
    {
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()], 400);
        }

        $address = Addresses::where('id', $request->address_id)->where('user_id', $user->id)->first();
        if ($address) {
            Addresses::where('user_id', $user->id)->update(['is_home' => 0]);
            $address->is_home = 1;
            $address->save();
        }

        return response()->json(['message' => __('messages.The address has been set as default successfully')]);
    }
}
