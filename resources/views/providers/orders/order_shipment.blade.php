@extends('providers.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->

@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content" style="    margin-bottom: 72px;">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span>
                        - ارسال الطلب </h4>
                    <img style="width: 196px;
    float: left;" src="/uploads/{{$shipment->photo}}">
                </div>

                <!-- 						<div class="heading-elements"> -->
                <!-- 							<div class="heading-btn-group"> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a> -->
                <!-- 							</div> -->
                <!-- 						</div> -->
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
                    <li><a href="/provider-panel/orders">عرض الطلبات</a></li>
                    <li class="active">ارسال الطلب لشركة الشحن</li>
                </ul>


                <!-- 						<ul class="breadcrumb-elements"> -->
                <!-- 							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li> -->
                <!-- 							<li class="dropdown"> -->
                <!-- 								<a href="#" class="dropdown-toggle" data-toggle="dropdown"> -->
                <!-- 									<i class="icon-gear position-left"></i> -->
                <!-- 									Settings -->
                <!-- 									<span class="caret"></span> -->
                <!-- 								</a> -->

                <!-- 								<ul class="dropdown-menu dropdown-menu-right"> -->
                <!-- 									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li> -->
                <!-- 									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li> -->
                <!-- 									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li> -->
                <!-- 									<li class="divider"></li> -->
                <!-- 									<li><a href="#"><i class="icon-gear"></i> All settings</a></li> -->
                <!-- 								</ul> -->
                <!-- 							</li> -->
                <!-- 						</ul> -->
            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

        @include('providers.message')

        <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">بيانات الشحنة </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>


                <div class="panel-body">

                    <form method="post" class="form-horizontal"
                          action="{{ '/provider-panel/send-shipment/'.$object->id  }}">
                        {!! csrf_field() !!}

                        <fieldset class="content-group">
                            <h4>بيانات العميل</h4>
                            <!-- 									<legend class="text-bold">Basic inputs</legend> -->

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">اسم العميل</label>
                                <div class="col-lg-10">
                                    <input type="text" disabled name="name" value="{{ $object->user->username  }}"
                                           class="form-control">
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">المدينة</label>
                                <div class="col-lg-10">
                                    <input type="text" disabled name="state"
                                           value="{{@$object->getOrder->address->state->name}}" class="form-control">

                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">العنوان</label>
                                <div class="col-lg-10">
                                    @php
                                        $address='';
if($shipment_address->street)$address=$shipment_address->street.' - ';
if($shipment_address->building)$address.=$shipment_address->building.' - ';
if($shipment_address->floor)$address.=' الطابق '.$shipment_address->floor.' - ';
if($shipment_address->house_no)$address.=' رقم '.$shipment_address->house_no.' - ';
if($shipment_address->near_place)$address.=' بجوار '.$shipment_address->near_place.' - ';
if($shipment_address->note)$address.=' ملحوظة : '.$shipment_address->note.' - ';

                                    @endphp
                                    <input type="text" disabled name="address_" value="{{$address}}"
                                           class="form-control">
                                    <input type="hidden"  name="address" value="{{$address}}"
                                           class="form-control">

                                </div>
                            </div>

                        </fieldset>
                        <fieldset class="content-group">
                            <h4>بيانات موقع الشحن</h4>
                            <!-- 									<legend class="text-bold">Basic inputs</legend> -->

                            <div class="form-group{{ $errors->has('shop_name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">اسم المتجر</label>
                                <div class="col-lg-10">
                                    <input type="text" name="shop_name"
                                           value="{{ auth()->user()->provider->username  }}" class="form-control">
                                </div>
                                @if ($errors->has('shop_name'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('shop_name') }}</strong>
		                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('user_name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">اسم المرسل</label>
                                <div class="col-lg-10">
                                    <input type="text" name="user_name" value="{{ auth()->user()->username  }}"
                                           class="form-control">
                                </div>
                                @if ($errors->has('user_name'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('user_name') }}</strong>
		                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">رقم الجوال</label>
                                <div class="col-lg-10">
                                    <input type="text" name="phone" value="{{ auth()->user()->phone  }}"
                                           class="form-control">
                                </div>
                                @if ($errors->has('phone'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('phone') }}</strong>
		                                    </span>
                                @endif
                            </div>
                            <div class="form-group {{ $errors->has('shipper_state') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">المدينة : </label>
                                <div class="col-lg-10">
                                    <select class="select-multiple-tokenization" name="shipper_state" class="shipper_state form-control">
                                        <option value="">اختر المدينة</option>
                                        @foreach(\App\Models\States::where('country_id',188)->get() as $state)
                                            <option value="{{ $state->name_en }}" {{ $state->id == auth()->user()->provider->state_id ? ' selected' : '' }}>{{ $state -> name_en }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('shipper_state'))
                                        <span class="help-block">
									{{ $errors->first('shipper_state') }}
									</span>

                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('shipper_address') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">العنوان</label>
                                <div class="col-lg-10">

                                    <input type="text" name="shipper_address"
                                           value="{{auth()->user()->provider->address}}" class="form-control">
                                </div>
                                @if ($errors->has('shipper_address'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('shipper_address') }}</strong>
		                                    </span>
                                @endif
                            </div>

                        </fieldset>
                        <fieldset class="content-group">
                            <h4>بيانات الشحنة</h4>
                            <!-- 									<legend class="text-bold">Basic inputs</legend> -->

                            <div class="form-group{{ $errors->has('reference_no') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">رقم الشحنة</label>
                                <div class="col-lg-10">
                                    <input type="text" name="reference_no"
                                      disabled     value="{{ $object->id  }}" class="form-control">
                                </div>
                                @if ($errors->has('reference_no'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('reference_no') }}</strong>
		                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('item_description') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">الوصف</label>
                                <div class="col-lg-10">
                                    <input type="text" name="item_description" value="عدد المنتجات ({{$object->cart_items->count()}})"
                                           class="form-control">
                                </div>
                                @if ($errors->has('item_description'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('item_description') }}</strong>
		                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('weight') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">(KM) الوزن</label>
                                <div class="col-lg-10">
                                    <input type="number" name="weight" value="{{old('weight')}}"
                                           class="form-control">
                                </div>
                                @if ($errors->has('weight'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('weight') }}</strong>
		                                    </span>
                                @endif
                            </div>
                            <div class="form-group{{ $errors->has('cod') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">قيمة الدفع عند الاستلام</label>
                                <div class="col-lg-10">
                                    <input type="text" name="cod" value="{{$object->getOrder->payment_method==1?(@$object->cart_items->sum('price') + $object->delivery_price+$object->taxes):'مدفوع'}}"
                                         disabled  class="form-control">
                                </div>
                                @if ($errors->has('cod'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('cod') }}</strong>
		                                    </span>
                                @endif
                            </div>

                            <div class="form-group {{ $errors->has('boxes') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">عدد العبوات</label>
                                <div class="col-lg-10">
                                    <input type="number" name="boxes" value="{{old('boxes')}}"
                                           class="form-control">
                                </div>
                                @if ($errors->has('boxes'))
                                    <span class="help-block">
		                                        <strong>{{ $errors->first('boxes') }}</strong>
		                                    </span>
                                @endif
                            </div>

                        </fieldset>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">ارسال<i
                                        class="fa  fa-truck position-right"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /form horizontal -->


            <!-- Footer -->
        @include('providers.footer')
        <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
@stop
