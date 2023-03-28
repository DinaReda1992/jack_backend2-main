<?php

namespace App\Http\Controllers\Panel;

use FCM;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Menus;
use App\Http\Requests;
use App\Models\Orders;
use App\Models\Balance;
use App\Models\Messages;
use App\Models\Packages;
use App\Models\Projects;
use App\Models\Addresses;
use App\Models\Countries;
use App\Models\Main_menus;
use App\Exports\UsersExport;
use App\Models\BankTransfer;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\UserServices;
use App\Models\UsersRegions;
use Illuminate\Http\Request;
use App\Exports\BalanceExport;
use App\Models\ActivationCodes;
use App\Models\SupplierCategory;
use App\Models\ServicesCategories;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use LaravelFCM\Message\OptionsBuilder;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class AllUsersController extends Controller
{

    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }
    public function getUserPage($id)
    {
        $object = User::whereId($id)->with('orders')
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" OR ISNULL(photo) THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->when(auth()->user()->user_type_id != 1, function ($query) {
                $query->whereIn('region_id', function ($query) {
                    $query->select('region_id')
                        ->from(with(new UsersRegions())->getTable())
                        ->where('user_id', auth()->id());
                });
            })
            ->first();
        $balance = \App\Models\Balance::where('user_id', $object->id)->sum('price');
        if (!$object) abort(404);
        return view('admin.users.userPage', compact('object', 'balance'));
    }
    public function transactions($id)
    {
        $objects = Balance::where('status', 1)->where('user_id', $id)
            ->get();
        $balance = \App\Models\Balance::where('user_id', $id)->sum('price');
        return view('admin.users.transactions', compact('objects', 'balance'));
    }
    public function getUserOrders($id)
    {

        $status = '';
        $type = __('dashboard.all_orders');
        if (request()->status != '') {
            $types = [
                0 => __('dashboard.new_orders'),
                1 => __('dashboard.accepted_orders'),
                2 => __('dashboard.orders_in_finance'),
                3 => __('dashboard.orders_in_warehouse'),
                4 => __('dashboard.new_orders'),
                5 => __('dashboard.cancelled_orders'),
                6 => __('dashboard.delivering_orders'),
                7 => __('dashboard.completed_orders'),
            ];
            $status = intval(request()->status);
            $type = $types[$status];
        } else {
            $status = 8;
        }

        $objects = Orders::where('user_id', $id)
            ->when(auth()->user()->user_type_id != 1, function ($query) {
                $query->whereHas('user', function (Builder $query) {
                    return $query->whereIn('region_id', function ($query) {
                        $query->select('region_id')
                            ->from(with(new UsersRegions())->getTable())
                            ->where('user_id', auth()->id());
                    });
                });
            })
            ->where(function ($query) use ($status) {
                //                if($status==0)$query->where('status',0);

                switch ($status) {
                    case 0: {
                            $query->where('status', 0)
                                ->where('payment_method', '<>', 0);
                            break;
                        }
                    case 1: {
                            $query->where('status', 1);
                            break;
                        }
                    case 2: {
                            $query->where('status', 1)
                                ->where('marketed_date', '!=', null)
                                ->where('financial_date', '!=', null);
                            break;
                        }
                    case 3: {
                            $query->where('status', $status);
                            break;
                        }
                    case 5: {
                            $query->where('status', $status);
                            break;
                        }
                    case 6: {
                            $query->where('status', $status);
                            break;
                        }
                    case 7: {
                            $query->where('status', $status);
                            break;
                        }
                    default: {
                            $query->where('payment_method', '<>', 0);
                        }
                }
            })
            ->orderBy('id', 'desc')
            ->paginate(50);
        $user = User::whereId($id)
            ->select('*')
            ->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . \Illuminate\Support\Facades\URL::to('/') . '/uploads/", photo)) END) AS photo')
            ->first();

        return view('admin.users.orders', compact('objects', 'user', 'type'));
    }

    public function adv_user_package($user_id = 0, $package_id)
    {
        $package = Packages::find($package_id);
        $user = User::find($user_id);
        if ($user) {
            $user->package_id = $package->id;
            $user->user_type_id = 4;
            $user->days = $package->days;
            $user->date_of_package = date('Y-m-d');
            $user->save();
        }

        $notification55 = new Notification();
        $notification55->sender_id = 1;
        $notification55->reciever_id = $user->id;
        $notification55->type = 1;
        $notification55->message = "قامت الادارة بتمييز عضويتك ";
        $notification55->save();

        $notification_title = "تمييز عضويتك";
        $notification_message = $notification55->message;


        if (@$notification55->getReciever->notification == 1) {
            $this->send_fcm_notification($notification_title, $notification_message, $notification55, null, 'default');
        }

        return redirect()->back()->with('success', 'تم الاشتراك في الباقة بنجاح .');
    }

    public function cancel_package($user_id = 0)
    {

        $user = User::find($user_id);
        if ($user) {
            $user->package_id = 0;
            $user->days = 0;
            $user->date_of_package = "0000-00-00";
            $user->user_type_id = 3;
            $user->save();
        }
        return redirect()->back()->with('success', 'تم الغاء الباقة بنجاح .');
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
        //        foreach (User::where('user_type_id',3)->get() as $user ){
        //            $greater_date =  date("Y-m-d",strtotime(date("Y-m-d", strtotime($user->date_of_package)) . " +".$user->days." days"));
        //            if(date('Y-m-d')>=$greater_date){
        //                $user->project_activate=0;
        //                $user->package_id=0;
        //                $user->save();
        //            }
        //        }
        $objects = User::whereNotIn('user_type_id', [1, 2])->where('is_archived', 0)
            ->when(auth()->user()->user_type_id != 1, function ($query) {
                $query->whereIn('region_id', function ($query) {
                    $query->select('region_id')
                        ->from(with(new UsersRegions())->getTable())
                        ->where('user_id', auth()->id());
                });
            })->get();

        return view('admin.users.all', ['objects' => $objects]);
    }

    public function edit_profile()
    {
        return view('admin.users.edit_profile', ['object' => User::find(Auth::user()->id)]);
    }

    /**
     * @return string
     */
    public function edit_profile_post(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $this->validate($request, [
            'state_id' => 'required',
            'username' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
            'country_id' => 'required',
            'state_id' => 'required',
            'phone' => 'required',
            'password' => 'same:password_confirmation|min:6',
            'password_confirmation' => 'same:password'
        ]);
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->country_id = $request->input('country_id');
        $user->gender = $request->gender ?  $request->gender : 0;
        $user->state_id = $request->input('state_id');
        if ($request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/' . $user->photo;
            if (is_file($old_file))    unlink($old_file);
            $fileName = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $user->photo = $fileName;
        }

        $user->save();

        return redirect()->back()->with('success', 'تم تعديل بياناتك بنجاح .');
    }



    public function clients_users()
    {
        // $objects = User::where('user_type_id', 5)
        //     ->where(['is_archived' => 0, 'approved' => 1])
        //     ->when(auth()->user()->user_type_id != 1, function ($query) {
        //         $query->whereIn('region_id', function ($query) {
        //             $query->select('region_id')
        //                 ->from(with(new UsersRegions())->getTable())
        //                 ->where('user_id', auth()->id());
        //         });
        //     })
        //     ->orderBy('id', 'DESC')->get();
        return view('admin.users.normal');
    }

    public function clientsData(Request $request)
    {
        $users = User::orderBy('id', 'DESC')
            ->where(['is_archived' => 0, 'approved' => 1])
            ->where('user_type_id', 5);
            // ->when(auth()->user()->user_type_id != 1, function ($query) {
            //     $query->whereIn('region_id', function ($query) {
            //         $query->select('region_id')
            //             ->from(with(new UsersRegions())->getTable())
            //             ->where('user_id', auth()->id());
            //     });
            // });
        return DataTables::of($users)
            // ->editColumn('phone', function ($user) {
            //     return  $user->phone;
            // })
            ->addColumn('region', function ($user) {
                return @$user->region->name;
            })
            ->addColumn('created_at', function ($user) {
                return @$user->created_at->format('Y-m-d h:i A');
            })
            ->addColumn('products_count', function ($user) {

                return '<a href="/admin-panel/all-users/user-page/' . $user->id . '">' . __('dashboard.count_orders') . '
                (' . $user->orders()->count() . ')</a>';
            })
            // ->addColumn('phone', function ($user) {
            //     return '+' . ($user->phonecode) . $user->phone;
            // })
            ->addColumn('activation_code', function ($user) {
                return @\App\Models\ActivationCodes::whereIn('phone', [$user->phone, '0' . $user->phone])->first()->activation_code;
            })
            ->addColumn('block', function ($user) {
                return '<a href="/admin-panel/all-users/block_user/' . $user->id . '">
                                    <button type="button" name="button"
                                            class="btn ' . ($user->block == 1 ? 'btn-success' : 'btn-danger') . '">' . ($user->block == 1 ? __('dashboard.unblock') : __('dashboard.block')) . '</button>
                                </a>';
            })
            ->addColumn('addresses', function ($user) {
                if ($user->user_type_id == 5) {
                    return '<a type="button" class="text-white btn btn-success"
                href="/admin-panel/all-users/addresses/' . $user->id . '">' . __('dashboard.add_address') . '</a>';
                } else {
                    return;
                }
            })
            ->addColumn('actions', function ($user) {
                $ul = '<ul class="icons-list">';
                $ul .= '<li class="text-primary-600"><a href="/admin-panel/all-users/' . $user->id . '/edit"><i class="icon-pencil7"></i></a></li>';
                $ul .= '<li class="text-danger-600"><a onclick="return false;" object_id="' . $user->id
                    . '" delete_url="/admin-panel/all-users/' . $user->id
                    . '"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>';
                $ul .= '</ul>';
                return $ul;
            })
            ->make(true);
    }

    public function clients_users_excel()
    {
        $date = Carbon::today();
        return Excel::download(new UsersExport(), 'users-sheet-' . $date . '.xlsx');

        return view('admin.users.normal', ['objects' => $objects]);
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

    public function supervisor_users()
    {
        return view('admin.users.supervisor_users', ['objects' => User::where('user_type_id', 4)->where('is_archived', 0)->orderBy('id', 'DESC')->get()]);
    }

    public function representative_users()
    {
        return view('admin.users.representatives', ['objects' => User::where('user_type_id', 4)->where('is_archived', 0)->orderBy('id', 'DESC')->get()]);
    }

    public function normal_users()
    {
        return view('admin.users.normal', ['objects' => User::where('user_type_id', 4)->where('is_archived', 0)->with('activation_code')->get()]);
    }

    public function seller_users()
    {
        return view('admin.users.adv', ['objects' => User::where('user_type_id', 3)->where('is_archived', 0)->get()]);
    }
    public function both_users()
    {
        return view('admin.users.normal', ['objects' => User::where('user_type_id', 5)->where('is_archived', 0)->get()]);
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
    public function adv_user($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'لا يوجد عضو بهذا العنوان');
        }
        if ($user->adv == 0) {
            $user->adv = 1;
            $user->save();
            return redirect()->back()->with('success', 'تم تميز المستخدمية بنجاح .');
        } else {
            $user->adv = 0;
            $user->save();
            return redirect()->back()->with('success', 'تم ازالة تمييز المستخدمية بنجاح .');
        }
    }

    public function change_drag_name($user_id = 0, $vals = "")
    {
        $user = User::find($user_id);
        if (!$user) {
            return redirect()->back()->with('error', 'لا يوجد عضو بهذا العنوان');
        }
        $user->drag_name = $vals;
        $user->save();
        return redirect()->back()->with('success', 'تم تغيير حالة الدراج بنجاح .');
    }

    public function supervisor($id = 0)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('error', 'لا يوجد عضو بهذا العنوان');
        }
        if ($user->supervisor == 0) {
            $user->supervisor = 1;
            $user->save();
            return redirect()->back()->with('success', 'تم تعيين المستخدم كمشرف المستخدم بنجاح');
        } else {
            $user->supervisor = 0;
            $user->save();
            return redirect()->back()->with('success', 'تم ازالة الاشراف عن المستخدم بنجاح .');
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.add', ['countries' => Countries::all()]);
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
            'phone' => 'required|unique:users,phone|digits:9',
            'username' => 'required',
            //            'password' => 'required|min:6',
            //            'password_confirmation' => 'required|same:password',
            //            'state_id' =>'required',
            //            'currency_id' =>'required',
            'user_type_id' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'client_type' => 'required',
            'country_id' => 'required',
            'region_id' => 'required',
            'state_id' => 'required',
            'photo' => $request->photo ?  'required|mimes:jpeg,png,jpg,gif,svg|max:4048' : '',

        ]);
        $object = new User;
        $object->username = $request->username;
        $object->email = $request->email;
        $object->phone = ltrim($request->phone, '0');
        $object->country_id = $request->country_id ?: 188;
        $object->state_id = $request->state_id ?: '';
        $object->region_id = $request->region_id ?: '';

        $object->currency_id = $request->currency_id ?: 1;
        $object->phonecode = $request->phonecode ?: 966;
        $object->address = $request->address ?: '';
        $object->state_id = $request->state_id;
        $object->longitude = $request->longitude ?: '';
        $object->latitude = $request->latitude ?: '';
        $object->activate = 1;
        $object->approved = 1;

        $object->user_type_id = 5;
        $object->profit_rate = $request->profit_rate ? $request->profit_rate : '';
        $object->device_type = $request->device_type ? $request->device_type : '';
        $object->accept_pricing = $request->accept_pricing ? 1 : 0;
        $object->accept_estimate = $request->accept_estimate ? 1 : 0;
        $object->add_product = $request->add_product ? 1 : 0;
        $object->tax_number = $request->tax_number == '' ? '' : $request->tax_number;

        $object->shop_type = $request->shop_type ?: 0;
        $object->client_type = $request->client_type ?: 0;

        $object->shipment_id = 1;
        $object->shipment_days = 3;

        //        $object->password = bcrypt($request->password);

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
        return redirect('/admin-panel/all-users/addresses/' . $object->id)->with('success', 'تم اضافة الحساب بنجاح قم باضافة عنوان.');
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

        return view('admin.users.add', ['object' => User::where('id', $id)->whereIn('user_type_id', [3, 4, 5])->first()]);
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
        $object = User::where('id', $id)->whereIn('user_type_id', [3, 4, 5])->first();
        $this->validate($request, [
            'username' => 'required',
            'phone' => 'required|regex:/[0-9]/|min:9|unique:users,phone,' . $object->id . ',id',
            'email' => 'required|email|unique:users,email,' . $object->id . ',id',
            'country_id' => 'required',
            'region_id' => 'required',
            'state_id' => 'required',
            //            'currency_id' => 'required',
            'client_type' => 'required',
            //            'address' => 'required',
            'photo' => $request->photo ?  'required|image' : '',
            //            'password' => $request->password ? 'same:password_confirmation|min:6' : '',
            //            'password_confirmation' => $request->password ? 'same:password' : '',
        ]);

        $object->username = $request->username;
        $object->email = $request->email;
        $object->phone = $request->phone;
        $object->country_id = $request->country_id ?: 188;
        $object->state_id = $request->state_id ?: '';
        $object->region_id = $request->region_id ?: '';
        $object->currency_id = $request->currency_id ?: 1;
        $object->phonecode = $request->phonecode ?: 966;
        $object->address = $request->address ?: '';
        $object->longitude = $request->longitude ?: '';
        $object->latitude = $request->latitude ?: '';
        $object->accept_pricing = $request->accept_pricing ? 1 : 0;
        $object->accept_estimate = $request->accept_estimate ? 1 : 0;
        $object->add_product = $request->add_product ? 1 : 0;

        $object->shop_type = $request->shop_type ?: 1;
        $object->client_type = $request->client_type ?: 1;
        $object->tax_number = $request->tax_number == '' ? '' : $request->tax_number;

        $object->user_type_id = 5;
        $object->profit_rate = $request->profit_rate ? $request->profit_rate : '';

        //        $object->gender = $request->gender;
        /* if($request->password) {
            $object->password = bcrypt($request->password);
        }*/
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
        $object = User::where('id', $id)->whereNotIn('user_type_id', [1, 2, 5])->first();
        $object->is_archived = 0;
        $object->save();
        //        $old_file = 'uploads/'.$object->photo;
        //        if(is_file($old_file))	unlink($old_file);
        //        $object->delete();
    }



    /**/


    public function addresses($id)
    {
        $user = User::find($id);
        $addresses = Addresses::where(['user_id' => $id, 'is_archived' => 0])->get();
        $country = Countries::where('id', $user->country_id)->select('id', 'name')->with('getRegions.getStates:id,name,country_id,region_id')
            ->with('getRegions:id,name,country_id')->get();

        return view('admin.users.addresses', compact('addresses', 'country', 'user'));
    }
    public function store_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $input = $request->all();
        $input['user_id'] = $request->user_id;
        $addresses_count = Addresses::where('user_id', auth()->id())->count();

        if ($addresses_count == 0) {
            $input['is_home'] = 1;
        }
        Addresses::create($input);
        $addresses = Addresses::where(['user_id' => $request->user_id, 'is_archived' => 0])->get();
        return response()->json([
            'message' => 'تم اضافة العنوان بنجاح',
            'addresses' => $addresses,
        ]);
    }
    public function update_address(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $input = $request->all();
        $input['user_id'] = $request->user_id;
        $address = Addresses::where('id', $id)->where('user_id', $request->user_id)->first();
        $address->update($input);
        $addresses = Addresses::where(['user_id' => $request->user_id, 'is_archived' => 0])->get();
        return response()->json([
            'message' => 'تم تعديل العنوان بنجاح',
            'addresses' => $addresses,
        ]);
    }
    public function delete_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $input['user_id'] = \auth()->id();
        $address = Addresses::where('id', $request->id)->where('user_id', $request->user_id)->first();
        $address->update(['is_archived' => 1]);
        $addresses = Addresses::where(['user_id' => $request->user_id, 'is_archived' => 0])->get();
        return response()->json([
            'message' => 'تم حذف العنوان بنجاح',
            'addresses' => $addresses,
        ]);
    }
    public function is_home(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $address = Addresses::where('id', $request->id)->where('user_id', $request->user_id)->first();
        if ($address) {
            $addresses =  Addresses::where('user_id', $request->user_id)->update(['is_home' => 0]);
            $address->is_home = 1;
            $address->save();
        }

        $addresses = Addresses::where(['user_id' => $request->user_id, 'is_archived' => 0])->get();
        return response()->json([
            'message' => 'تم تغيير العنوان للرئيسي بنجاح',
            'addresses' => $addresses,
            'address' => $address,
        ]);
    }
}
