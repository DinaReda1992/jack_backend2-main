<?php

namespace App\Http\Controllers\Panel;

use App\Entities\OfferApproved;
use App\Entities\OfferType;
use App\Entities\UserType;
use App\Exports\OffersExport;
use App\Http\Controllers\Controller;
use App\Jobs\SendPushNotification;
use App\Models\Offer_invitation;
use App\Models\Products;
use App\Models\Shop_offer;
use App\Models\Shop_offer_item;
use App\Models\Shop_offer_type;
use App\Models\Shop_product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Image;
use Yajra\DataTables\Facades\DataTables;

class OfferController extends Controller
{
    public function __construct()
    {
        // \Carbon\Carbon::setLocale('ar');
        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
            return $next($request);
        });
    }

    public function downloadOffersExcel(Request $request)
    {
        return \Excel::download(new OffersExport, 'offers_' . date('Y-m-d') . '.xlsx');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.offers.all');
    }

    public function requests(Request $request)
    {
        return view('admin.offers.requests');
    }

    public function company_offers(Request $request)
    {
        return view('admin.offers.company-offers');
    }

    public function getOffersData(Request $request)
    {
        $search = [];

        if (isset($request->type) && !empty($request->type)) {
            $search['type'] = $request->type;
        }

        $offers = Shop_offer::select('*')
            ->where(function ($query) use ($request) {
                if ($request->page_name == 'offers') {
                    $query->where(function ($query) {
                        $query->where(['type' => 4, 'approved' => 1])
                            ->orWhere(function ($query) {
                                $query->where('type', 1);
                            });
                    });
                }
                //                if ($request->page_name == 'company-offers') {
                //                    $query->where('shop_offers.status', 1);
                //                    $query->where(['type' => 2, 'status' => 1])
                //                        ->orWhere(function ($query) use ($request, $provider_id) {
                //                            $query->where(['type' => 3, 'status' => 1])
                //                                ->whereHas('invitations', function (Builder $query) use ($request, $provider_id) {
                //                                    $query->where('user_id', $provider_id);
                //                                    $query->where(['approved' => 0]);
                //
                //                                    /*if($request->status!='all'){
                //                                        if($request->status=='waiting'){
                //                                            $query->where(['approved'=>0]);
                //                                        }elseif ($request->status=='approved'){
                //                                            $query->where(['approved'=>1]);
                //                                        }elseif ($request->status=='refused'){
                //                                            $query->where(['approved'=>2]);
                //                                        }
                //                                    }*/
                //                                })
                //                                ->with('invitations', function ($query) use ($provider_id) {
                //                                    $query->where('user_id', $provider_id);
                //                                    $query->where(['approved' => 0]);
                //
                //                                });
                //                        });
                //                }
                //                if ($request->page_name == 'requests') {
                //                    $query->where('shop_offers.user_id', $provider_id);
                //                    $query->where(['type' => 4]);
                //                    if ($request->status != 'all') {
                //                        if ($request->status == 'waiting') {
                //                            $query->where(['approved' => 0]);
                //                        } elseif ($request->status == 'approved') {
                //                            $query->where(['approved' => 1]);
                //                        } elseif ($request->status == 'refused') {
                //                            $query->where(['approved' => 2]);
                //                        }
                //                    }
                //                }
            })
            ->selectRaw('(SELECT count(*) FROM shop_offer_items WHERE shop_offer_items.offer_id = shop_offers.id and shop_offer_items.group =1) as main_items')
            ->selectRaw('(SELECT count(*) FROM shop_offer_items WHERE shop_offer_items.offer_id = shop_offers.id and shop_offer_items.group =2) as get_items')
            ->with(['items' => function ($query) {
                $query->groupBy('product_id');
            }])
            ->whereHas('items.product')
            ->with(['items.product' => function ($query) {
                $query->select('id', 'title', 'photo', DB::raw('round(client_price,2) as price'));
                $query->selectRaw('(CASE WHEN photo = "" THEN "' . url('/') . "/images/placeholder.png" . '" ELSE (CONCAT ("' . url("/") . '/uploads/", photo)) END) AS photo');
            }])->orderBy('id', 'DESC');
        if (isset($request->status) && !empty($request->status) && $request->status == 'deleted') {
            $offers->onlyTrashed();
        }

        return DataTables::of($offers)
            ->addColumn('status', function ($data) use ($request) {
                if (($data->approved == OfferApproved::Approved && $request->page_name == 'requests') || $request->page_name == 'offers') {
                    return '<div class="checkbox checkbox-switchery switchery-sm switchery-double">
									<input type="checkbox" object_id="' . $data->id . '" delete_url="/admin-panel/stop_offer/' . $data->id . '" class="switchery sweet_switch" ' . ($data->status == 1 ? 'checked' : '') . ' />
								</div>';
                }
            })
            ->addColumn('main_items', function ($data) {
                $btn = '<a data-toggle="modal" data-target="#myModal' . $data->id . '"> ' . '' . __('dashboard.count_products') . '(' . $data->items->groupby('product_id')->count() . ')' . '</a>';
                $btn .= '<div class="modal fade" id="myModal' . $data->id . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
												<h4 class="modal-title" id="myModalLabel">' . __('dashboard.products') . '</h4>
											</div>
											<div class="modal-body">
												<div class="row">
													<div  style="margin-top: 25px;" class="col-xs-12">
														<div class="">
															<table class="table table-responsive table-bordered">
																<thead>
                                                                    <tr>
                                                                        <th>' . __('dashboard.id') . '</th>
                                                                        <th>' . __('dashboard.product_name') . '</th>
                                                                        <th>' . __('dashboard.image') . '</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>';
                foreach ($data->items->where('group', 1) as $item) {
                    $btn .= ' <tr>
                                                                        <td>' . $item->product_id . '</td>
                                                                        <td>' . @$item->product->title . '</td>
                                                                        <td><img src="' . @$item->product->photo . '" width="50"></td>
                                                                        </tr>';
                }
                if ($data->type_id == 4) {
                    $btn .= '<tr><td colspan="4" class="text-center">منتجات سيأخذها العميل</td></tr>';
                }
                foreach ($data->items->where('group', 2) as $item) {
                    $btn .= ' <tr>
                                                                                <td>' . $item->product_id . '</td>
                                                                                <td>' . @$item->product->title . '</td>
                                                                                <td><img src="' . @$item->product->photo . '" width="50"></td>
                                                                                </tr>';
                }

                $btn .= ' </tbody>
															</table>
														</div>
													</div>
													<hr>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">' . __('dashboard.close') . '</button>
											</div>
										</div>
									</div>
								</div>';
                return $btn;
            })
            ->editColumn('type_id', function ($data) {

                if ($data->type_id == 1) {
                    $message = __('dashboard.discount');
                    $message .= $data->price_discount;
                    $message .= __('dashboard.sar');
                } elseif ($data->type_id == 2) {
                    $message = __('dashboard.save_money') . ' ' . $data->percentage . ' %';
                } elseif ($data->type_id == 3) {

                    if ($data->is_free == 1) {
                        $message = __('dashboard.get');
                        $message .= $data->quantity + $data->get_quantity . ' ';
                        $message .= 'بقيمة ';
                        $message .= $data->quantity;
                    } else {
                        if ($data->percentage == 100) {
                            $message = __('dashboard.get');
                            $message .= $data->quantity + $data->get_quantity . ' ';
                            $message .= 'بقيمة ';
                            $message .= $data->quantity;
                        } else {
                            if ($data->quantity == 1) {
                                $message = $data->percentage . ' %';
                                $message .= ' علي الحبة الثانية ';
                            } else {
                                $message = ' عرض خاص ';
                            }
                        }
                    }
                } elseif ($data->type_id == 4) {
                    $message = 'هدية بعد الحبة ';
                    $message .= $data->quantity;
                } elseif ($data->type_id == 5) {
                    $message = 'شحن مجاني ';
                }
                return $message;
            })
            ->addColumn('actions', function ($data) use ($request) {
                $ul = '<ul class="icons-list">';
                if ($request->page_name == 'offers') {
                    if ($data->deleted_at !== null) {
                        $ul .= '<li class="text-teal-600"><a onclick="return false;"
																	 object_id="' . $data->id . '"
																	 method="get"
																	 delete_url="/admin-panel/offer_archived_restore/' . $data->id . '"
																	 class="sweet_warning" method="get" href="#" message="' . __('dashboard.are_you_sure_from_restore') . ' العرض"><i
														class="fa  fa-refresh"></i> استعادة</a></li>';
                    } else {
                        $ul .= '<li class="text-primary-600"><a href="/admin-panel/offers/' . $data->id . '/edit"><i class="icon-pencil7"></i></a></li>';
                        $ul .= '<li class="text-danger-600"><a onclick="return false;" object_id="' . $data->id
                            . '" delete_url="/admin-panel/offers/' . $data->id
                            . '"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>';
                    }
                } elseif ($request->page_name == 'company-offers') {
                    if ($data->approved == OfferApproved::Waiting) {
                        $ul .= '<li class="text-danger-600"><a data-method="POST" data-message="رفض العرض"  object_id="' . $data->id
                            . '" delete_url="/admin-panel/offers/company-offers/refuse/' . $data->id
                            . '"  class="getAlert text-white btn btn-danger" href="#">رفض العرض</a></li>';
                        $ul .= '<li class="text-primary-600"><a data-method="POST" data-message="قبول العرض"  object_id="' . $data->id
                            . '" delete_url="/admin-panel/offers/company-offers/approve/' . $data->id
                            . '"  class="getAlert text-white btn btn-success" href="#">قبول العرض</a></li>';
                    }
                } elseif ($request->page_name == 'requests') {
                    if ($data->approved == OfferApproved::Waiting) {


                        $ul .= '<li class="text-danger-600"><a data-method="POST" data-message="إلغاء الطلب"  object_id="' . $data->id
                            . '" delete_url="/admin-panel/offers/cancel/' . $data->id
                            . '"  class="getAlert text-white btn btn-success" href="#">إلغاء</a></li>';
                    } elseif ($data->approved == OfferApproved::Refused) {

                        $ul .= '<li class="text-primary-600"><a data-method="POST" data-message="اعادة ارسال"  object_id="' . $data->id
                            . '" delete_url="/admin-panel/offers/resend/' . $data->id
                            . '"  class="getAlert text-white btn btn-success" href="#">اعادة ارسال</a></li>';
                    }
                }

                $ul .= '</ul>';
                return $ul;
            })->make(true);
    }

    public function approve($id)
    {
        $provider_id = !Auth::user()->main_provider ? Auth::id() : Auth::user()->main_provider;
        $data = Offer_invitation::where('offer_id', $id)->where('approved', 0)->where('user_id', $provider_id)->first();
        if (!$data) {
            return 0;
        }
        $offer = Shop_offer::find($id);
        //        $from=Carbon::createFromFormat('Y-m-d', $offer->start_date);
        //        $to=Carbon::createFromFormat('Y-m-d', $offer->end_date);
        $from = $offer->start_date;
        $to = $offer->end_date;
        $get_products_ids = Shop_offer_item::where('offer_id', $offer->id)->pluck('product_id')->toArray();
        $ifProductExist = Shop_offer_item::whereIn('product_id', $get_products_ids)
            ->whereHas('offer', function (Builder $query) use ($from, $to, $provider_id) {
                $query->where(function ($query) use ($from, $to, $provider_id) {
                    $query->where('user_id', $provider_id);
                    $query->whereDate('start_date', '>=', $from);
                    $query->whereDate('end_date', '<=', $to);
                });
                $query->orWhere(function ($query) use ($from, $to, $provider_id) {
                    $query->where('user_id', $provider_id);
                    $query->whereDate('start_date', '<=', $to);
                    $query->whereDate('end_date', '>=', $from);
                });
            })->groupBy('product_id')->with('product:id,title')->get();
        if (count($ifProductExist) > 0) {
            $message_products = $ifProductExist->whereIn('product_id', $get_products_ids)->pluck('product.title');
            //            return 0;

            return response()->json(
                [
                    'status' => 401,
                    'message' => 'بعض المنتجات مضافة لعرض فى نفس الفترة الزمنية المختارة',
                    'data' => $message_products,
                    'data1' => $ifProductExist,
                ],
                202
            );
        }


        $data->approved = OfferApproved::Approved;
        $data->save();
        $data = Shop_offer::where('id', $id)->first();
        $new_offer = $data->replicate();
        $new_offer->user_id = $provider_id;
        $new_offer->offer_id = $id;
        $new_offer->type = 1;
        $new_offer->save();
        $items = Shop_offer_item::where('offer_id', $data->id)->get();
        foreach ($items as $item) {
            $item->replicate();
            $shop_product = Shop_product::where('user_id', $provider_id)->where('product_id', $item->product_id)->first();
            $item->shop_product_id = $shop_product->id;
            $item->user_id = $provider_id;
            $item->offer_id = $new_offer->id;
            $item->save();
        }


        return 1;
    }

    public function refuse($id)
    {
        $provider_id = !Auth::user()->main_provider ? Auth::id() : Auth::user()->main_provider;
        $data = Offer_invitation::where('offer_id', $id)->where('approved', 0)->where('user_id', $provider_id)->first();
        if (!$data) {
            return 0;
        }
        $data->approved = OfferApproved::Refused;
        $data->save();
        return 1;
    }

    public function stop_offer($id)
    {
        $data = Shop_offer::withTrashed()->where('id', $id)->first();
        $data->status = !$data->status;
        $data->save();
        return 1;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search_products()
    {
        $from = date(request()->start_date);
        $to = date(request()->end_date);

        $shop_products = Products::where(function ($q) use ($from, $to) {
            //            if (request()->has('is_gift')) {
            //                $q->where(['is_gift' => request()->is_gift]);
            //            }

            $q->where(function ($query) use ($from, $to) {
                $query->whereDoesntHave('offer_products.offer', function (Builder $query) use ($from, $to) {
                    $query->where(function ($query) use ($from, $to) {
                        $query->whereDate('start_date', '>=', $from);
                        $query->whereDate('end_date', '<=', $to);
                    });
                    $query->orWhere(function ($query) use ($from, $to) {
                        $query->whereDate('start_date', '<=', $to);
                        $query->whereDate('end_date', '>=', $from);
                    });
                    if (request()->offer_id) {
                        $query->where('id', '!=', request()->offer_id);
                    }
                });

                $query->orWhereHas('offer_products', function (Builder $query) {
                    $query->where('group', 2);
                });
            });
        })
            ->where([
                'products.is_archived' => 0,
                'products.stop' => 0
            ])
            ->select('products.id', 'products.id as product_id', 'products.title', 'products.photo')
            ->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $shop_products,
            ]
        );
    }

    public function search_users()
    {
        $users = User::where('user_type_id',5)->select('id', 'username','user_type_id')->get();
        return response()->json(
            [
                'status' => 200,
                'data' => $users,
            ]
        );
    }

    public function create()
    {
        $offer_types = Shop_offer_type::where('stop', 0)->get();
        $type = OfferType::Pharmacy_offer;
        return view('admin.offers.add', compact('offer_types', 'type'));
    }


    public function request_offers()
    {
        $offer_types = Shop_offer_type::where('stop', 0)->get();
        $type = OfferType::Request_offer;
        $companies = User::where('user_type_id', UserType::COMPANY_PROVIDER)->where('user_data.stop', 0)
            ->select('users.id', 'user_data.user_name', 'user_data.photo', 'user_data.phone')
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->get();

        return view('admin.offers.request_offer', compact('offer_types', 'type', 'companies'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offer_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'products' => 'required',
            'price_discount' => $request->offer_type == 1 ? 'required|numeric' : '',
            'percentage' => $request->offer_type == 2 || $request->offer_discount_type == 1 ? 'required|numeric|max:100' : '',
            'offer_discount_type' => $request->offer_type == 3 ? 'required|in:1,2' : '',
            'quantity' => $request->offer_type == 3 || $request->offer_type == 4 || $request->offer_type == 5 ? 'required|numeric' : '',
            'get_quantity' => $request->offer_type == 3 || $request->offer_type == 4 ? 'required|numeric' : '',
            'get_products' => $request->offer_type == 4 ? 'required' : '',
            'type' => $request->company_id != null ? 'required' : '',

        ], [
            'start_date.after_or_equal' => 'تاريخ بداية العرض يجب ان يكون قبل نهاية العرض',
            'price_discount.required' => 'الخصم مطلوب',
            'percentage.required' => 'نسبة الخصم مطلوب',
            'quantity.required' => 'الكمية المطلوبة لتفعيل العرض مطلوب',
            'get_quantity.required' => 'الكمية التي يأخذها العميل',
        ]);
        $products = json_decode($request->products);
        $clients = json_decode($request->clients);
        $ids = array_column($products, 'product_id');
        $clients_ids = array_column($clients, 'id');
        $get_products_ids = [];
        if ($request->offer_type == 4) {
            $get_products = json_decode($request->get_products);
            $get_products_ids = array_column($get_products, 'product_id');
        }
        if (count($ids) == 0 || ($request->offer_type == 4 && count($get_products_ids) == 0)) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'يجب اضافة منتجات للعرض',
                ],
                202
            );
        }
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $provider_id = !Auth::user()->main_provider ? Auth::id() : Auth::user()->main_provider;
        $from = Carbon::createFromFormat('Y-m-d', $request->start_date);
        $to = Carbon::createFromFormat('Y-m-d', $request->end_date);

        $ifProductExist = Shop_offer_item::whereIn('product_id', array_merge($ids, $get_products_ids))
            ->whereHas('offer', function (Builder $query) use ($from, $to, $provider_id) {
                $query->where(function ($query) use ($from, $to, $provider_id) {
                    $query->where('user_id', $provider_id);
                    $query->where(function ($query) use ($from, $to, $provider_id) {
                        $query->whereBetween('start_date', [$from, $to]);
                        $query->orWhereBetween('end_date', [$from, $to]);
                    });
                });
                //                $query->orWhere(function($query) use ($from,$to,$provider_id){
                //                    $query->where('user_id',$provider_id);
                //                    $query->whereDate('start_date', '<=',$to);
                //                    $query->whereDate('end_date', '>=',$from);
                //                });
            })->groupBy('product_id')->with('product:id,title')->get();
        if (count($ifProductExist) > 0) {
            $message_products = $ifProductExist->whereIn('product_id', $ids)->pluck('product.title');
            $message_get_products = $ifProductExist->whereIn('product_id', $get_products_ids)->pluck('product.title');

            return response()->json(
                [
                    'status' => 401,
                    'message' => 'بعض المنتجات مضافة لعرض فى نفس الفترة الزمنية المختارة',
                    'data' => $message_products,
                    'data_get_products' => $message_get_products,
                    'data1' => $ifProductExist,
                ],
                202
            );
        }

        /**/

        DB::beginTransaction();

        try {
            $new_offer = new Shop_offer();
            if ($request->type == 4) {
                $new_offer->type = 4;
                $new_offer->approved = 0;
                $new_offer->company_id = $request->company_id;
            }
            $new_offer->start_date = $request->start_date;
            $new_offer->end_date = $request->end_date;
            $new_offer->type_id = $request->offer_type;
            $new_offer->status = $request->status ? 1 : 0;
            $new_offer->number_of_users = $request->number_of_users;
            $new_offer->one_user_use = $request->one_user_use;
            if ($request->offer_type == 1) {
                $new_offer->price_discount = $request->price_discount;
            } elseif ($request->offer_type == 2) {
                $new_offer->percentage = $request->percentage;
            } elseif ($request->offer_type == 3) {
                $new_offer->quantity = $request->quantity;
                $new_offer->get_quantity = $request->get_quantity;
                if ($request->offer_discount_type == 1) {
                    $new_offer->percentage = $request->percentage;
                } elseif ($request->offer_discount_type == 2) {
                    $new_offer->is_free = 1;
                }
            } elseif ($request->offer_type == 4) {
                $new_offer->quantity = $request->quantity;
                $new_offer->get_quantity = $request->get_quantity;
                if ($request->offer_discount_type == 1) {
                    $new_offer->percentage = $request->percentage;
                } elseif ($request->offer_discount_type == 2) {
                    $new_offer->is_free = 1;
                }
            } elseif ($request->offer_type == 5) {
                $new_offer->quantity = $request->quantity;
            }
            $new_offer->save();
            foreach ($products as $product) {
                $item = new Shop_offer_item();
                $item->shop_product_id = $product->id;
                $item->group = 1;
                $item->product_id = $product->product_id;
                $item->user_id = $provider_id;
                $item->offer_id = $new_offer->id;
                $item->percentage = $new_offer->percentage ? $new_offer->percentage : null;
                $item->quantity = $new_offer->quantity ? $new_offer->quantity : null;

                $item->save();
            }
            if ($request->offer_type == 4) {
                foreach ($get_products as $get_product) {

                    $item = new Shop_offer_item();
                    $item->shop_product_id = $product->id;
                    $item->product_id = $get_product->product_id;
                    $item->user_id = $provider_id;
                    $item->offer_id = $new_offer->id;
                    $item->percentage = $new_offer->percentage ? $new_offer->percentage : null;
                    $item->quantity = $new_offer->get_quantity ? $new_offer->get_quantity : null;
                    $item->group = 2;
                    $item->save();
                }
            }

            $new_offer->users()->sync($clients_ids);
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            \Log::alert($e);
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'هناك خطأ',
                ],
                202
            );
        }
        /**/
        $user = Auth::user();
        if ($user->user_type_id == UserType::PHARMACY_SUPERVISOR_PROVIDER) {
            if ($request->type == 4) {
                $notify_message = ' قام' . $user->username . ' بطلب عرض ' . $new_offer->id . ' من المورد ' . $new_offer->company->user_name;
                $notify_message_en = $user->username . ' request offer ' . $new_offer->id . ' from company ' . $new_offer->company->user_name_en;
            } else {
                $notify_message = ' قام' . $user->username . ' بإضافة عرض ' . $new_offer->offer_type . ' على ' . $product->items->count() . ' منتج';
                \App::setLocale('en');
                $notify_message_en = $user->username . ' add offer ' . $new_offer->offer_type . ' on ' . $new_offer->items->count() . ' product';
                // App::setLocale('ar');
            }
            SendPushNotification::dispatch(
                $user->id,
                $user->provider->id,
                'عرض',
                'offer',
                $notify_message,
                $notify_message_en,
                14,
                [],
                ''
            );
        }

        return response()->json([
            'message' => "تم اضافة العرض بنجاح",
            'status' => 200
        ], 200);
    }

    public function createThumbnail($path, $width, $height, $target)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($target);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $offer_types = Shop_offer_type::where('stop', 0)->get();
        $object = Shop_offer::withTrashed()->where('id', $id)
            ->with(['items' => function ($query) {
                $query->groupby('shop_offer_items.product_id');
                $query->where('group', 1);
                $query->select('shop_offer_items.offer_id', 'products.title', 'products.id', 'shop_offer_items.product_id');
                $query->leftJoin("products", "products.id", "=", "shop_offer_items.product_id");
            }])
            ->with(['get_items' => function ($query) {
                $query->select('shop_offer_items.offer_id', 'products.title', 'products.id', 'shop_offer_items.product_id');
                $query->leftJoin("products", "products.id", "=", "shop_offer_items.product_id");
                //               $query->select('shop_offer_items.id','shop_offer_items.product_id','products.title');
            }])->with(['users' => function ($query) {
                $query->select('users.id', 'username');
            }])
            ->first();
        if (!$object) {
            return abort(404);
        }

        return view('admin.offers.edit', compact('object', 'offer_types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',

            'products' => 'required',
            'price_discount' => $request->offer_type == 1 ? 'required|numeric' : '',
            'percentage' => $request->offer_type == 2 || $request->offer_discount_type == 1 ? 'required|numeric|max:100' : '',
            'offer_discount_type' => $request->offer_type == 3 ? 'required|in:1,2' : '',
            'quantity' => $request->offer_type == 3 || $request->offer_type == 4 || $request->offer_type == 5 ? 'required|numeric' : '',
            'get_quantity' => $request->offer_type == 3 || $request->offer_type == 4 ? 'required|numeric' : '',
            'get_products' => $request->offer_type == 4 ? 'required' : '',

        ], [
            'start_date.after_or_equal' => 'تاريخ بداية العرض يجب ان يكون قبل نهاية العرض',
            'price_discount.required' => 'الخصم مطلوب',
            'percentage.required' => 'نسبة الخصم مطلوب',
            'quantity.required' => 'الكمية المطلوبة لتفعيل العرض مطلوب',
            'get_quantity.required' => 'الكمية التي يأخذها العميل',
        ]);
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $get_offer = Shop_offer::where(['id' => $id])->first();
        if (!$get_offer) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'لا يوجد عرض تابع لك',
                ],
                202
            );
        }
        $products = json_decode($request->products);
        $ids = array_column($products, 'product_id');
        $get_products_ids = [];
        if ($request->offer_type == 4) {
            $get_products = json_decode($request->get_products);
            $get_products_ids = array_column($get_products, 'product_id');
        }
        if (count($ids) == 0 || ($request->offer_type == 4 && count($get_products_ids) == 0)) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'يجب اضافة منتجات للعرض',
                ],
                202
            );
        }
        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                ],
                202
            );
        }
        $from = Carbon::createFromFormat('Y-m-d', $request->start_date);
        $to = Carbon::createFromFormat('Y-m-d', $request->end_date);

        $ifProductExist = Shop_offer_item::whereIn('product_id', array_merge($ids, $get_products_ids))
            ->whereHas('offer', function (Builder $query) use ($from, $to, $provider_id, $id) {
                $query->where(function ($query) use ($from, $to, $provider_id, $id) {
                    // $query->where('user_id', $provider_id);
                    $query->whereDate('start_date', '>=', $from);
                    $query->whereDate('end_date', '<=', $to);
                    $query->where('offer_id', '!=', $id);
                });
                $query->orWhere(function ($query) use ($from, $to, $provider_id, $id) {
                    // $query->where('user_id', $provider_id);
                    $query->whereDate('start_date', '<=', $to);
                    $query->whereDate('end_date', '>=', $from);
                    $query->where('offer_id', '!=', $id);
                });
            })->groupBy('product_id')->with('product:id,title')->get();
        if (count($ifProductExist) > 0) {
            $message_products = $ifProductExist->whereIn('product_id', $ids)->pluck('product.title');
            $message_get_products = $ifProductExist->whereIn('product_id', $get_products_ids)->pluck('product.title');

            return response()->json(
                [
                    'status' => 401,
                    'message' => 'بعض المنتجات مضافة لعرض فى نفس الفترة الزمنية المختارة',
                    'data' => $message_products,
                    'data_get_products' => $message_get_products,
                    'data1' => $ifProductExist,
                ],
                202
            );
        }

        /**/

        DB::beginTransaction();

        try {
            $new_offer = Shop_offer::whereId($id)->first();
            $new_offer->start_date = $request->start_date;
            $new_offer->end_date = $request->end_date;
            $new_offer->type_id = $request->offer_type;
            // $new_offer->user_id = $provider_id;
            $new_offer->status = $request->status ? 1 : 0;
            $new_offer->number_of_users = $request->number_of_users;
            $new_offer->one_user_use = $request->one_user_use;
            if ($request->offer_type == 1) {
                $new_offer->price_discount = $request->price_discount;
            } elseif ($request->offer_type == 2) {
                $new_offer->percentage = $request->percentage;
            } elseif ($request->offer_type == 3) {
                $new_offer->quantity = $request->quantity;
                $new_offer->get_quantity = $request->get_quantity;
                if ($request->offer_discount_type == 1) {
                    $new_offer->percentage = $request->percentage;
                } elseif ($request->offer_discount_type == 2) {
                    $new_offer->is_free = 1;
                }
            } elseif ($request->offer_type == 4) {
                $new_offer->quantity = $request->quantity;
                $new_offer->get_quantity = $request->get_quantity;
                if ($request->offer_discount_type == 1) {
                    $new_offer->percentage = $request->percentage;
                } elseif ($request->offer_discount_type == 2) {
                    $new_offer->is_free = 1;
                }
            } elseif ($request->offer_type == 5) {
                $new_offer->quantity = $request->quantity;
            }
            $new_offer->save();
            foreach ($products as $product) {

                Shop_offer_item::whereNotIn('product_id', $ids)->where(['offer_id' => $id, 'group' => 1])->delete();
                $item = Shop_offer_item::where('product_id', $product->id)->where('offer_id', $id)->where('group', 1)->first();
                if (!$item) {
                    $item = new Shop_offer_item();
                    $item->product_id = $product->product_id;
                    $item->user_id = $provider_id;
                    $item->group = 1;
                    $item->offer_id = $id;
                    $shop_product = Products::where('id', $product->product_id)->first();
                    $item->shop_product_id = $shop_product->id;
                    $item->save();
                }
                $item->percentage = $new_offer->percentage ? $new_offer->percentage : null;
                $item->quantity = $new_offer->quantity ? $new_offer->quantity : null;
                $item->save();
            }
            if ($request->offer_type == 4) {
                foreach ($get_products as $get_product) {
                    Shop_offer_item::whereNotIn('product_id', $get_products_ids)->where(['offer_id' => $id, 'group' => 2])->delete();
                    $item = Shop_offer_item::where('product_id', $get_product->id)->where('offer_id', $id)->where('group', 2)->first();
                    if (!$item) {
                        $item = new Shop_offer_item();
                        $item->product_id = $get_product->product_id;
                        $item->user_id = $provider_id;
                        $item->group = 2;
                        $item->offer_id = $id;
                        $shop_product = Products::where('product_id', $get_product->product_id)->first();
                        $item->shop_product_id = $shop_product->id;
                        $item->save();
                    }

                    $item->percentage = $new_offer->percentage ? $new_offer->percentage : null;
                    $item->quantity = $new_offer->get_quantity ? $new_offer->get_quantity : null;
                    $item->group = 2;
                    $item->save();
                }
            }
            $clients = json_decode($request->clients);
            $clients_ids = array_column($clients, 'id');    
            $new_offer->users()->sync($clients_ids);
            DB::commit();
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            \Log::alert($e);
            return response()->json(
                [
                    'status' => 400,
                    'message' => 'هناك خطأ',
                ],
                202
            );
        }
        /**/


        return response()->json([
            'message' => "تم حفظ التغييرات",
            'status' => 200
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $product = Shop_offer::where('id', $id)
            // ->where('user_id', $provider_id)
            ->first();
        if (!$product) {
            return 0;
        }
        $product->delete();
        // Shop_offer_item::where('offer_id', $id)->delete();
        return 1;
    }

    public function resend($id)
    {
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $data = Shop_offer::where('id', $id)
            // ->where('user_id', $provider_id)
            ->first();
        if (!$data) {
            return 0;
        }
        $data->approved = 0;
        $data->save();
        return 1;
    }

    public function cancel($id)
    {
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $data = Shop_offer::where('id', $id)
            // ->where('user_id', $provider_id)
            ->first();
        if (!$data) {
            return 0;
        }
        $data->approved = 3;
        $data->save();
        return 1;
    }

    public function offer_archived_restore($id)
    {
        $provider_id = Auth::user()->user_type_id == 3 ? Auth::id() : Auth::user()->main_provider;
        $product = Shop_offer::withTrashed()->where('id', $id)
            // ->where('user_id', $provider_id)
            ->first();
        if (!$product) {
            return 0;
        }
        $product = $product->restore();
        // Shop_offer_item::withTrashed()->where('offer_id', $id)->restore();
        return 1;
    }
}
