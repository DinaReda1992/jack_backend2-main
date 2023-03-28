<?php

namespace App\Http\Controllers\Website;

use App\Models\States;
use App\Models\Regions;
use App\Models\Addresses;
use App\Models\Countries;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client')->except('getRegions', 'getRegionStates');
    }

    public function index()
    {
        $addresses = Addresses::where(['user_id' => auth('client')->id(), 'is_archived' => 0])->get();
        $country = Countries::where('id', auth('client')->user()->country_id)
            ->select('id', 'name')
            ->with('getRegions.getStates:id,name,country_id,region_id')
            ->with('getRegions:id,name,country_id')
            ->get();
        return view('website.addresses', compact('addresses', 'country'));
    }

    public function store(AddressRequest $request)
    {
        $user = auth('client')->user();
        $addresses_count = Addresses::where('user_id', $user->id)->count();
        Addresses::create($request->validated() + ['user_id' => $user->id, 'is_home' => $addresses_count == 0 ? 1 : 0]);
        return response()->json(['message' => __('messages.The address has been added successfully')]);
    }

    public function update(AddressRequest $request, $id)
    {
        $user = auth('client')->user();
        $address = Addresses::where('id', $id)->where('user_id', $user->id)->first();
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
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()], 400);
        }

        $address = Addresses::where('id', $request->id)->where('user_id', $user->id)->first();
        if (!$address) {
            return response()->json(['message' => __('messages.There is no address with this number'), 'status' => 400,], 400);
        }
        $address->is_archived = 1;
        $address->save();
        return response()->json(['message' => __('messages.The address has been deleted successfully')]);
    }

    public function setDefaultAddress(Request $request)
    {
        $user = auth('client')->user();

        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'message' => $validator->errors()->first()], 400);
        }

        $address = Addresses::where('id', $request->id)->where('user_id', $user->id)->first();
        if ($address) {
            Addresses::where('user_id', $user->id)->update(['is_home' => 0]);
            $address->is_home = 1;
            $address->save();
        }

        $addresses = Addresses::where(['user_id' => auth('client')->id(), 'is_archived' => 0])->get();

        return response()->json([
            'message' => __('messages.The address has been set as default successfully'),
            'addresses' => $addresses,
            'address' => $address,
        ]);
    }

    public function getRegions($id = 0)
    {
        echo "<option value=''>اختر المنطقة</option>";
        foreach (Regions::where('country_id', $id)->get() as $state) {
            echo "<option value='" . $state->id . "'>" . $state->name . "</option>";
        }
    }

    public function getRegionStates($id = 0)
    {
        echo "<option value=''>اختر المدينة</option>";
        foreach (States::where('region_id', $id)->get() as $state) {
            echo "<option value='" . $state->id . "'>" . $state->name . "</option>";
        }
    }
}
