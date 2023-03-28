<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use App\Models\Make;
use App\Models\MakeYear;
use App\Models\MeasurementUnit;
use App\Models\MeasurementUnitsCategories;
use App\Models\Models;
use App\Models\Notification;
use App\Models\OrderShipments;
use App\Models\Shipment as Shipmentt;
use App\Models\Subcategories;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{


    public function __construct()
    {
        App::setLocale('ar');
        \Carbon\Carbon::setLocale(App::getLocale());


    }

public function getIndex()
	{
        $objects = OrderShipments::select('*')-> where(function ($query) {
            $query->where('shop_id', auth()->id())
                ->orWhere('shop_id', auth()->user()->main_provider);
        })
            ->selectRaw('(SELECT sum(price)*quantity FROM cart_items WHERE cart_items.shipment_id =order_shipments.id and cart_items.status !=5) as items_price')
            ->orderBy('created_at','desc')
            ->paginate(15);
        $shipment = Shipmentt::where('id', auth()->user()->provider->shipment_id)->first();
        $shop=User::select('users.*')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . '	) as all_orders')

            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=1	) as new_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=2	) as preparing_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=3	) as shipping_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=4	) as completed_orders')
            ->selectRaw('(SELECT count(*) FROM order_shipments WHERE order_shipments.shop_id =' . auth()->user()->provider->id . ' AND order_shipments.status=5	) as canceled_orders')

            ->where('users.id',auth()->user()->provider->id)
            ->first();

        return view('providers.index',['orders'=>$objects,'shipment'=>$shipment,'shop'=>$shop]);
	}
    public function getMakeYears($id = 0)
    {
        $model=Models::find($id);
        $model_year=$model->year;
        $year_make=$model_year->make;
        $years=MakeYear::where('make_id',$year_make->id)->get();
        echo "<option value=''>اختر سنة الصنع</option>";
        foreach ($years as $year) {
            echo "<option value='" . $year->id . "'>" .  $year->year . "</option>";
        }
    }
    public function getMakeModels($id = 0)
    {

        echo "<option value=''>اختر الموديل</option>";
        $models=Models::whereIn('makeyear_id', function ($query) use ($id) {
            $query->select('id')
                ->from(with(new MakeYear())->getTable())
                ->where('make_id', $id);
        })->where('is_archived',0)->groupBy('name_en')->get();
        foreach ($models as $model) {
            echo "<option value='" . $model->id . "'>" .  $model->name . "</option>";
        }
    }
    public function getSubCategories($id = 0)
    {
        $objects=Subcategories::where('category_id',$id)->get();
        echo "<option value=''>اختر القسم الفرعى</option>";
        foreach ($objects as $object) {
            echo "<option value='" . $object->id . "'>" .  $object->name . "</option>";
        }
    }
    public function getCategoryMeasurement($id = 0)
    {
        $objects=MeasurementUnit::whereIn('id', function ($query) use ($id) {
            $query->select('measurement_id')
                ->from(with(new MeasurementUnitsCategories())->getTable())
                ->where('category_id', $id);
        })->get();
        echo "<option value=''>اختر وحدة القياس</option>";
        foreach ($objects as $object) {
            echo "<option value='" . $object->id . "'>" .  $object->name . "</option>";
        }
    }

    public function clear_notifications(){
        Notification::where('reciever_id',auth()->user()->provider->id)
            ->where("status", 0)
            ->update(['status' => 1]);
        return 1;

    }

}