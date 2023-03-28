<?php

namespace App\Http\Controllers\Panel;

use FCM;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Menus;
use App\Http\Requests;
use App\Models\Orders;
use App\Models\Messages;
use App\Models\Packages;
use App\Models\Products;
use App\Models\Projects;
use App\Models\Countries;
use App\Models\Main_menus;
use App\Models\BankTransfer;
use App\Models\DeviceTokens;
use App\Models\MainSupplier;
use App\Models\Notification;
use App\Models\SupplierData;
use App\Models\UserServices;
use Illuminate\Http\Request;
use App\Exports\SupplierExport;
use App\Models\SupplierCategory;
use App\Models\ServicesCategories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class SuppliersController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }



    public function send_fcm_notification($notification_title, $notification_message, $notification55, $object_in_push, $sound = "default")
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody($notification_message)
            ->setSound('default');
        $notificationBuilder->setClickAction('FLUTTER_NOTIFICATION_CLICK');
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'data' => [
                'notification_type' => (int)$notification55->type,
                'notification_title' => $notification_title,
                'notification_message' => $notification_message,
                'notification_data' => $object_in_push
            ]
        ]);
        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        $token = @$notification55->getReciever->devices->count();
        $tokens = DeviceTokens::where('user_id', $notification55->reciever_id)->pluck('device_token')->toArray();
        $notification_ = @$notification55->getReciever->notification;
        if ($token > 0 && $notification_) {
            $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
            $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
        }
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $objects = User::where('user_type_id', 3)->when(request('main_supplier_id'), function ($query) {
            $query->where('main_supplier_id', request('main_supplier_id'));
        })->where('is_archived', 0)->get();
        return view('admin.suppliers.all', ['objects' =>  $objects]);
    }


    public function provider_users(Request $request)
    {
        $type = $request->type;
        $objects = User::where('user_type_id', 3)->where(function ($query) use ($type) {
            if ($type == 'deleted') {
                $query->where('is_archived', 1);
            } else {
                $query->where('is_archived', 0);
            }
        })->orderBy('id', 'DESC')->get();
        return view('admin.users.adv', ['objects' => $objects]);
    }



    public function block_user($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'لا يوجد عضو بهذا العنوان');
        }
        if ($user->block == 0) {
            $user->block = 1;
            $user->save();
            return redirect()->back()->with('success', 'تم حظر المستخدم بنجاح');
        } else {
            $user->block = 0;
            $user->save();
            return redirect()->back()->with('success', 'تم فك الحظر عن المستخدم بنجاح .');
        }
    }

    public function active_user($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'لا يوجد عضو بهذا العنوان');
        }
        if ($user->activate == 0) {
            $user->activate = 1;
            $user->save();
            return redirect()->back()->with('success', 'تم تفعيل المستخدم بنجاح');
        } else {
            $user->activate = 0;
            $user->save();
            return redirect()->back()->with('success', 'تم الغاء التفعيل عن المستخدم بنجاح .');
        }
    }
    public function all_suppliers()
    {
        $objects = User::select('users.id', 'users.username', 'users.photo', 'users.phone', 'supplier_data.sort')->join('supplier_data', 'supplier_data.user_id', 'users.id')
            ->with('supplier')->where('user_type_id', 3)->orderBy('supplier_data.sort', 'ASC')->get();
        return view(
            'admin.suppliers.sort',
            ['objects' => $objects]
        );
    }
    public function change_sort(Request $request)
    {
        if ($request->single) {
            $privilege = SupplierData::where('user_id', $request->id)->first();
            $privilege->sort = $request->sort;
            $privilege->save();
        } else {
            foreach ($request->position as $key => $value) {
                $privilege = SupplierData::where('user_id', $value)->first();
                if ($privilege) {
                    $privilege->sort = $key;
                    $privilege->save();
                }
            }
        }
    }
    public function active_payment($id = 0, $package_id = 0)
    {
        $package = Packages::find($package_id);
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'لا يوجد عضو بهذا العنوان');
        }
        if ($user->project_activate == 0) {
            $user->project_activate = 1;
            $user->package_id = $package->id;
            $user->days = $package->days;
            $user->date_of_package = date('Y-m-d');
            $user->save();
            return redirect()->back()->with('success', 'تم تفعيل باقة المستخدم بنجاح');
        } else {
            $user->project_activate = 0;
            $user->save();
            return redirect()->back()->with('success', 'تم الغاء تفعيل باقة المستخدم بنجاح .');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $main_suppliers = MainSupplier::all();
        return view('admin.suppliers.add', ['countries' => Countries::all(), 'main_suppliers' => $main_suppliers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password',
            'main_supplier_id' => 'nullable',
            //            'state_id' =>'required',
            //            'currency_id' =>'required',
            'user_type_id' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            //            'phonecode'=>'required',
            //            'photo'=> $request->photo?  'required|image':'',

        ]);
        $object = new User;
        $object->username = $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
        $object->country_id = $request->country_id ?: 188;
        $object->state_id = $request->state_id ?: '';
        $object->region_id = $request->region_id ?: '';
        $object->main_supplier_id = $request->main_supplier_id;
        $object->currency_id = $request->currency_id ?: 1;
        $object->phonecode = $request->phonecode ?: 966;
        $object->address = $request->address ?: '';
        $object->state_id = $request->state_id;
        $object->longitude = $request->longitude ?: '';
        $object->latitude = $request->latitude ?: '';
        $object->activate = 1;
        $object->approved = 1;

        $object->user_type_id = 3;
        $object->profit_rate = 0;
        $object->accept_pricing = $request->accept_pricing ? 1 : 0;
        $object->accept_estimate = $request->accept_estimate ? 1 : 0;
        $object->add_product = $request->add_product ? 1 : 0;

        $object->shop_type = $request->shop_type ?: 1;
        $object->shipment_id = 1;
        $object->shipment_days = 3;

        $object->password = bcrypt($request->password);

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo = $fileName;
        }

        $object->save();
        if ($request->categories) {
            foreach ($request->categories as $key => $value) {
                $supplierCategory = new SupplierCategory();
                $supplierCategory->user_id = $object->id;
                $supplierCategory->category_id = $value;
                $supplierCategory->save();
            }
        }
        return redirect('/admin-panel/supplier-data/' . $object->id)->with('success', 'تم اضافة الحساب بنجاح .');
        //        return redirect()->back()->with('success','تم اضافة الحساب بنجاح .');
    }
    public function addSupplierData($id = 0)
    {
        $user = User::where('id', $id)->where('user_type_id', 3)->first();
        if (!$user) return abort(404);
        $supplier_data = SupplierData::where('user_id', $user->id)->first();
        return view('admin.suppliers.add_data', ['user' => $user, 'supplier_data' => $supplier_data]);
    }
    public function postSupplierData(Request $request)
    {
        $user = User::where('id', $request->user_id)->where('user_type_id', 3)->first();
        if (!$user) return abort(404);
        $this->validate($request, [
            'supplier_name' => 'required',
            'supplier_name_en' => 'required',
            'phone' => 'required|regex:/[0-9]/|min:9',
            'photo' => $request->photo ?  'required|mimes:jpeg,png,jpg,gif,svg|max:4048' : '',
        ]);
        $supplier_data = SupplierData::where('user_id', $request->user_id)->first();
        if (!$supplier_data) {
            $supplier_data = new SupplierData();
        }
        $supplier_data->user_id = $user->id;
        $supplier_data->supplier_name = $request->supplier_name;
        $supplier_data->supplier_name_en = $request->supplier_name_en;
        $supplier_data->commercial_no = $request->commercial_no ?: '';
        $supplier_data->tax_no = $request->tax_no ?: '';
        $supplier_data->email = $request->email ?: '';
        $supplier_data->phone = $request->phone;
        $supplier_data->maroof_no = $request->maroof_no ?: '';
        $supplier_data->stop = $request->stop ? 0 : 1;
        $supplier_data->bio = $request->bio ?: '';

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            if ($supplier_data && $supplier_data->photo) {
                $old_file = 'uploads/' . $supplier_data->photo;
                if (is_file($old_file))    unlink($old_file);
            }

            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $supplier_data->photo = $fileName;
        }
        $supplier_data->save();
        return redirect()->back()->with('success', 'تم اضافة وتعديل بيانات المنشأة .');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supplier_types = SupplierCategory::where('user_id', $id)->pluck('category_id')->toArray();
        $main_suppliers = MainSupplier::all();
        return view('admin.suppliers.add', ['object' => User::where('id', $id)->whereIn('user_type_id', [3, 4])->first(), 'supplier_types' => $supplier_types, 'main_suppliers' => $main_suppliers]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $object = User::where('id', $id)->whereIn('user_type_id', [3, 4])->first();
        $this->validate($request, [
            'username' => 'required|unique:users,username,' . $object->id . ',id',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,' . $object->id . ',id',
            'email' => 'required|email|unique:users,email,' . $object->id . ',id',
            'main_supplier_id' => 'nullable',
            //            'state_id' => 'required',
            //            'currency_id' => 'required',
            //            'phonecode' => 'required',
            //            'address' => 'required',
            'photo' => $request->photo ?  'required|image' : '',
            'password' => $request->password ? 'same:password_confirmation|min:6' : '',
            'password_confirmation' => $request->password ? 'same:password' : '',
        ]);

        $object->username = $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
        $object->country_id = $request->country_id ?: 188;
        $object->state_id = $request->state_id ?: '';
        $object->region_id = $request->region_id ?: '';
        $object->main_supplier_id = $request->main_supplier_id;
        $object->currency_id = $request->currency_id ?: 1;
        $object->phonecode = $request->phonecode ?: 966;
        $object->address = $request->address ?: '';
        $object->longitude = $request->longitude ?: '';
        $object->latitude = $request->latitude ?: '';
        $object->accept_pricing = $request->accept_pricing ? 1 : 0;
        $object->accept_estimate = $request->accept_estimate ? 1 : 0;
        $object->add_product = $request->add_product ? 1 : 0;

        $object->shop_type = $request->shop_type ?: 1;

        $object->user_type_id = 3;
        $object->profit_rate = $request->profit_rate ?: '';

        //        $object->gender = $request->gender;
        if ($request->password) {
            $object->password = bcrypt($request->password);
        }
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/' . $object->photo;
            if (is_file($old_file))    unlink($old_file);
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo = $fileName;
        }

        $object->save();
        SupplierCategory::where('user_id', $object->id)->delete();
        if ($request->categories) {
            foreach ($request->categories as $key => $value) {
                $supplierCategory = new SupplierCategory();
                $supplierCategory->user_id = $object->id;
                $supplierCategory->category_id = $value;
                $supplierCategory->save();
            }
        }

        return redirect()->back()->with('success', 'تم تعديل حساب المتجر بنجاح .');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = User::where('id', $id)->whereNotIn('user_type_id', [1, 2])->first();
        $object->is_archived = 1;
        $object->save();
        //        $old_file = 'uploads/'.$object->photo;
        //        if(is_file($old_file))	unlink($old_file);
        //        $object->delete();
    }
    public function provider_archived_restore($id)
    {
        $object = User::where('id', $id)->whereNotIn('user_type_id', [1, 2])->first();
        $object->is_archived = 0;
        $object->save();
        //        $old_file = 'uploads/'.$object->photo;
        //        if(is_file($old_file))	unlink($old_file);
        //        $object->delete();
    }
    public function stop_supplier($id)
    {
        $object = SupplierData::where('user_id', $id)->first();
        $object->stop = !$object->stop;
        $object->save();
        return 1;
    }

    public function exportExcel()
    {
        $date = Carbon::today();
        return Excel::download(new SupplierExport(), 'suppliers-sheet-' . $date . '.xlsx');
    }
}
