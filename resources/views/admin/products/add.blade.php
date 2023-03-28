@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>

    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script type="text/javascript" src="/assets/js/plugins/notifications/sweet_alert.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- InputMask -->

    {{--	<script type="text/javascript" src="/assets/js/core/app.js"></script> --}}

    {{--	<script type="text/javascript" src="/assets/js/pages/components_modals.js"></script> --}}

    <!-- /theme JS files -->
    <script type="text/javascript">
        function calculate_price() {
            var profit = document.getElementById('profit_perc').value;
            document.getElementById('price').value = parseFloat(document.getElementById('original_price').value) +
                parseFloat(profit);
            document.getElementById('final_price').value = parseFloat(document.getElementById('original_price').value) +
                parseFloat(profit);

        }

        $(function() {
            $('.category_id').change(function() {
                // $('.subcategory_id').html('<option value="">اختر القسم الفرعى</option>').trigger("change");

                var category_id = $('.category_id').val();
                $.ajax({
                    url: '/admin-panel/get-sub-categories/' + category_id,
                    success: function(data) {
                        $('#categories').html(data);
                    }
                });
                //
                // });
                // $('.category_id').change(function () {
                // 	var category_id = $('.category_id').val();
                // 	if(category_id =='')return false
                // 	$('#measurement_id').empty().trigger("change");
                //
                //
                // 	$.ajax({
                // 		url: '/admin-panel/getCategoryMeasurement/' + category_id,
                // 		success: function (data) {
                // 			$('#measurement_id').html(data);
                // 		}
                // 	});
                //
            });

        });
    </script>
    <style type="text/css">
        .iconpicker .iconpicker-item {
            float: right;
        }

        .remove-extra {
            width: 23px;
            display: inline-block;
            float: right;
            height: 23px;
            color: #fff;
            background: #dc4747;
            text-align: center;
            border-radius: 50%;
            margin-top: 5px;
            cursor: pointer;
        }
    </style>

@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper only add-product-content">
        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.product') }}
                    </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/products"><button type="button" class="btn btn-success" name="button">
                                {{ __('dashboard.view_products') }}</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li class="active"> {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.product') }} </li>
                </ul>
            </div>
            <div style=" text-align: center;">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a data-toggle="tab" href="#main_data">{{ __('dashboard.basic_information') }}</a>
                    </li>
                    <li><a data-toggle="tab" href="#photos">{{ __('dashboard.product_images') }}</a></li>
                </ul>
            </div>

        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">
            <form method="post" class="form-horizontal"
                action="{{ isset($object) ? '/admin-panel/products/' . $object->id : '/admin-panel/products' }}"
                enctype="multipart/form-data">
                {!! csrf_field() !!}
                @if (isset($object))
                    <input type="hidden" name="_method" value="PATCH" />
                @endif
                @include('admin.message')


                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">{{ __('dashboard.product_details') }}</h5>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                                <li><a data-action="reload"></a></li>
                                <li><a data-action="close"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div id="main_data" class="tab-pane fade in active ">

                                <fieldset class="content-group">

                                    @if (isset($suppliers))
                                        <div class="form-group{{ $errors->has('provider_id') ? ' has-error' : '' }}">
                                            <label class="control-label col-lg-2"> اختر المورد *</label>
                                            <div class="col-lg-10">
                                                <select name="provider_id"
                                                    class=" form-control select-multiple-tokenization">
                                                    <option value="">اختر المورد</option>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}"
                                                            {{ isset($object) && $object->provider_id == $supplier->id ? 'selected' : (old('provider_id') == $supplier->id || (isset($supplier_id) && $supplier_id == $supplier->id) ? 'selected' : '') }}>
                                                            {{ $supplier->supplier_name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('provider_id'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('provider_id') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2"> {{ __('dashboard.main_category') }}
                                            *</label>
                                        <div class="col-lg-10">
                                            <select name="category_id"
                                                class="category_id form-control select-multiple-tokenization">
                                                <option value="">{{ __('dashboard.main_category') }}</option>
                                                @foreach (\App\Models\MainCategories::where('stop', 0)->where('is_archived', 0)->orderBy('sort', 'asc')->get() as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ isset($object) && $object->category_id == $category->id ? 'selected' : (old('category_id') == $category->id ? 'selected' : '') }}>
                                                        {{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('category_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('category_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{--                                    <div class="form-group{{ $errors->has('categories') ? ' has-error' : '' }}"> --}}
                                    {{--                                        <label class="control-label col-lg-2">إختر الأقسام</label> --}}
                                    {{--                                        <div class="col-lg-10"> --}}
                                    {{--                                            <select class="select-multiple-tokenization" multiple="multiple" --}}
                                    {{--                                                    name="categories[]" id="categories"> --}}

                                    {{--                                                <?php $currentTypes = isset($productCategories) ? $productCategories : (old('categories') ?: []); ?> --}}
                                    {{--                                                @foreach ($categories as $category) --}}
                                    {{--                                                    <option value="{{ $category->id }}" {{(in_array($category->id, $currentTypes)) ? "selected":'' }} >{{ $category->name }}</option> --}}
                                    {{--                                                @endforeach --}}
                                    {{--                                            </select> --}}
                                    {{--                                            @if ($errors->has('categories')) --}}
                                    {{--                                                <span class="help-block"> --}}
                                    {{--		                                        <strong>{{ $errors->first('categories') }}</strong> --}}
                                    {{--		                                    </span> --}}
                                    {{--                                            @endif --}}
                                    {{--                                        </div> --}}

                                    {{--                                    </div> --}}

                                    <div class="form-group{{ $errors->has('subcategory_id') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.categories') }} *</label>
                                        <div class="col-lg-10">
                                            <select name="subcategory_id" id="categories" class="form-control">
                                                @foreach ($categories as $category)
                                                    @if (isset($object) && ($object->subcategory_id == $category->id || old('subcategory_id') == $category->id))
                                                        <option value="{{ $category->id }}"
                                                            {{ isset($object) && $object->subcategory_id == $category->id ? 'selected' : (old('subcategory_id') == $category->id ? 'selected' : '') }}>
                                                            {{ $category->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @if ($errors->has('subcategory_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('subcategory_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="form-group{{ $errors->has('measurement_id') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.unit_of_measurement') }}
                                            *</label>
                                        <div class="col-lg-10">
                                            <select name="measurement_id" class="measurement_id form-control "
                                                id="measurement_id">
                                                <option value="">{{ __('dashboard.unit_of_measurement') }}</option>
                                                @php
                                                    $my_measurements = \App\Models\MeasurementUnit::groupBy('name')->get();
                                                    
                                                @endphp
                                                @if (isset($my_measurements))
                                                    @foreach ($my_measurements as $measurement)
                                                        <option value="{{ $measurement->id }}"
                                                            {{ isset($object) && $object->measurement_id == $measurement->id ? 'selected' : (old('measurement_id') == $measurement->id ? 'selected' : '') }}>
                                                            {{ $measurement->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('measurement_id'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('measurement_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.product_name_ar') }}
                                            *</label>
                                        <div class="col-md-10">
                                            <input required placeholder="{{ __('dashboard.product_name_ar') }}"
                                                value="{{ isset($object) ? $object->title : old('title') }}" maxlength="30"
                                                class="form-control" type="text" name="title">
                                            @if ($errors->has('title'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('title') }}</strong>
                                                </span>
                                            @endif
                                            <span class="help-block">
                                                {{-- <strong>حد اقصى 30 حرف</strong> --}}
                                            </span>

                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('title_en') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.product_name_en') }}
                                            *</label>
                                        <div class="col-md-10">
                                            <input required placeholder="{{ __('dashboard.product_name_en') }}"
                                                value="{{ isset($object) ? $object->title_en : old('title_en') }}"
                                                maxlength="30" class="form-control" type="text" name="title_en">
                                            @if ($errors->has('title_en'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('title_en') }}</strong>
                                                </span>
                                            @endif
                                            <span class="help-block">
                                                {{-- <strong>حد اقصى 30 حرف</strong> --}}
                                            </span>

                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('expiry') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.product_validity') }}
                                            *</label>
                                        <div class="col-md-10">
                                            <input required placeholder=" {{ __('dashboard.product_validity') }}"
                                                maxlength="30"
                                                value="{{ isset($object) ? $object->expiry : old('expiry') }}"
                                                class="form-control" type="text" name="expiry">
                                            @if ($errors->has('expiry'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('expiry') }}</strong>
                                                </span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('temperature') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.temperature') }} *</label>
                                        <div class="col-md-10">
                                            <input required placeholder=" {{ __('dashboard.temperature') }}"
                                                maxlength="30"
                                                value="{{ isset($object) ? $object->temperature : old('temperature') }}"
                                                class="form-control" type="text" name="temperature">
                                            @if ($errors->has('temperature'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('temperature') }}</strong>
                                                </span>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('deliver_status') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2"> {{ __('dashboard.deliver_status') }}
                                            *</label>
                                        <div class="col-lg-10">
                                            <select name="deliver_status" class=" form-control">
                                                <option value="">{{ __('dashboard.deliver_status') }}</option>
                                                @foreach (\App\Models\DeliverStatus::all() as $deliverStatus)
                                                    <option value="{{ $deliverStatus->id }}"
                                                        {{ isset($object) && $object->deliver_status == $deliverStatus->id ? 'selected' : (old('deliver_status') == $deliverStatus->id ? 'selected' : '') }}>
                                                        {{ $deliverStatus->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('deliver_status'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('deliver_status') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="form-group{{ $errors->has('min_quantity') ? ' has-error' : '' }}">
                                        <label
                                            class="control-label col-lg-2">{{ __('dashboard.min_quantity_in_order') }}</label>
                                        <div class="col-md-10">
                                            <input placeholder="{{ __('dashboard.min_quantity_in_order') }} "
                                                value="{{ isset($object) ? $object->min_quantity : old('min_quantity') }}"
                                                class="form-control" type="number" name="min_quantity">
                                            @if ($errors->has('min_quantity'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('min_quantity') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('weight') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.Unit weight in grams') }}
                                            *</label>
                                        <div class="col-md-10">
                                            <input required placeholder="{{ __('dashboard.Unit weight in grams') }}"
                                                value="{{ isset($object) ? $object->weight : old('weight') }}"
                                                class="form-control" type="number" step="0.01" name="weight">
                                            @if ($errors->has('weight'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('weight') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.quantity_in_warehouse') }}
                                        </label>
                                        <div class="col-md-10">
                                            <input placeholder="{{ __('dashboard.quantity_in_warehouse') }} "
                                                value="{{ isset($object) ? $object->quantity : old('quantity') }}"
                                                class="form-control" type="number" name="quantity">
                                            @if ($errors->has('quantity'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('quantity') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div
                                        class="form-group{{ $errors->has('min_warehouse_quantity') ? ' has-error' : '' }}">
                                        <label
                                            class="control-label col-lg-2">{{ __('dashboard.min_quantity_in_warehouse') }}</label>
                                        <div class="col-md-10">
                                            <input placeholder="{{ __('dashboard.min_quantity_in_warehouse') }} "
                                                value="{{ isset($object) ? $object->min_warehouse_quantity : old('min_warehouse_quantity') }}"
                                                class="form-control" type="number" name="min_warehouse_quantity">
                                            @if ($errors->has('min_warehouse_quantity'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('min_warehouse_quantity') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('original_price') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.original_price') }}
                                            *</label>
                                        <div class="col-md-10">
                                            <input id="original_price" oninput="calculate_price()"
                                                placeholder="{{ __('dashboard.original_price') }} "
                                                value="{{ isset($object) ? $object->original_price : old('original_price') }}"
                                                class="form-control calculate_profit" type="number" step="0.01"
                                                name="original_price">

                                            @if ($errors->has('original_price'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('original_price') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    {{--                                    <div class="form-group{{ $errors->has('profit_perc') ? ' has-error' : '' }}"> --}}
                                    {{--                                        <label class="control-label col-lg-2">مبلغ الربح *</label> --}}
                                    {{--                                        <div class="col-md-10"> --}}
                                    {{--                                            <input id="profit_perc" oninput="calculate_price()" --}}
                                    {{--                                                   placeholder="اضف نسبة الربح من السعر الاساسى " --}}
                                    {{--                                                   value="{{ isset($object) ? $object->profit_perc  : old('profit_perc')  }}" --}}
                                    {{--                                                   class="form-control calculate_profit" type="number" step="0.01" --}}
                                    {{--                                                   name="profit_perc"> --}}

                                    {{--                                            @if ($errors->has('profit_perc')) --}}
                                    {{--                                                <span class="help-block"> --}}
                                    {{--		                                        <strong>{{ $errors->first('profit_perc') }}</strong> --}}
                                    {{--		                                    </span> --}}
                                    {{--                                            @endif --}}
                                    {{--                                        </div> --}}
                                    {{--                                    </div> --}}

                                    {{--                                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}"> --}}
                                    {{--                                        <label class="control-label col-lg-2">سعر المنتج بعد الربح *</label> --}}
                                    {{--                                        <div class="col-md-10"> --}}
                                    {{--                                            <input id="price" required placeholder="سعر المنتج بالريال" disabled --}}
                                    {{--                                                   value="{{ isset($object) ? $object->price  : old('price')  }}" --}}
                                    {{--                                                   class="form-control final_price" type="number" step="0.01"> --}}
                                    {{--                                            @if ($errors->has('price')) --}}
                                    {{--                                                <span class="help-block"> --}}
                                    {{--		                                        <strong>{{ $errors->first('price') }}</strong> --}}
                                    {{--		                                    </span> --}}
                                    {{--                                            @endif --}}
                                    {{--                                        </div> --}}
                                    {{--                                    </div> --}}
                                    {{--                                        --}}
                                    <input type="hidden" name="price" id="final_price"
                                        value="{{ isset($object) ? $object->price : old('price') }}">

                                    <div class="form-group{{ $errors->has('client_price') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.price_to_the_client') }}
                                        </label>
                                        <div class="col-md-10">
                                            <input
                                                placeholder="{{ __('dashboard.Determine the proposed price for selling the product in the stores') }}"
                                                value="{{ isset($object) ? $object->client_price : old('client_price') }}"
                                                class="form-control " type="number" step="0.01" name="client_price">

                                            @if ($errors->has('client_price'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('client_price') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>


                                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">
                                            {{ __('dashboard.product_description_ar') }} *</label>
                                        <div class="col-md-10">
                                            <textarea class="form-control" name="description">{{ isset($object) ? $object->description : old('description') }}</textarea>
                                            @if ($errors->has('description'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('description') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">
                                            {{ __('dashboard.product_description_en') }} *</label>
                                        <div class="col-md-10">
                                            <textarea class="form-control" name="description_en">{{ isset($object) ? $object->description_en : old('description_en') }}</textarea>
                                            @if ($errors->has('description_en'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('description_en') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="">
                                            <label for="checkbox">
                                                <input type="checkbox" id="checkbox"
                                                    {{ (isset($object) && $object->has_cover) || old('has_cover') ? 'checked' : '' }}
                                                    name="has_cover">
                                                {{ __('dashboard.Do you provide a special bag for the product?') }}
                                            </label>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="">
                                            <label for="has_regions1">
                                                <input type="checkbox" id="has_regions"
                                                    {{ (isset($object) && $object->has_regions1) || old('has_regions1') ? 'checked' : '' }}
                                                    name="has_regions">
                                                {{ __('dashboard.has_regions') }}
                                            </label>

                                        </div>
                                    </div>

                                    <div class="form-group choose-regions {{ $errors->has('regions') ? ' has-error' : '' }}"
                                        style="display: {{ (isset($object) && $object->has_regions1) || old('has_regions1') ? 'block' : 'none' }};">
                                        <label
                                            class="control-label col-lg-2">{{ __('dashboard.select_regions') }}</label>
                                        <div class="col-lg-10">
                                            <select class="select-multiple-tokenization" multiple="multiple"
                                                name="regions[]" id="regions">

                                                <?php $currentTypes = isset($current_regions) ? $current_regions : (old('regions') ?: []); ?>
                                                @foreach (\App\Models\Regions::where('is_archived', 0)->get() as $rest)
                                                    <option value="{{ $rest->id }}"
                                                        {{ in_array($rest->id, $currentTypes) ? 'selected' : '' }}>
                                                        {{ $rest->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('regions'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('regions') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="form-group choose-regions {{ $errors->has('states') ? ' has-error' : '' }}"
                                        style="display: {{ (isset($object) && $object->has_regions1) || old('has_regions1') ? 'block' : 'none' }};">
                                        <label class="control-label col-lg-2">{{ __('dashboard.select_cities') }}</label>
                                        <div class="col-lg-10">
                                            <select class="select-multiple-tokenization" multiple="multiple"
                                                name="states[]" id="states">
                                                @php $currentStates=[] @endphp
                                                <?php $currentStates = isset($current_states) ? $current_states : (old('states') ?: []); ?>
                                                @foreach (\App\Models\States::whereIn('region_id', $currentTypes)->get() as $rest)
                                                    <option value="{{ $rest->id }}"
                                                        data-region="{{ $rest->region_id }}"
                                                        {{ in_array($rest->id, $currentStates) ? 'selected' : '' }}>
                                                        {{ $rest->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('regions'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('states') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                    </div>


                                </fieldset>
                            </div>


                            <div id="photos" class="tab-pane fade ">
                                <fieldset class="content-group">

                                    <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.product_image') }}
                                            *</label>
                                        <div class="col-lg-10">

                                            <input type="file" name="photo" class="file-input"
                                                data-show-caption="false" data-show-upload="false"
                                                data-browse-class="btn btn-primary btn-xs"
                                                data-remove-class="btn btn-default btn-xs">
                                            @if ($errors->has('photo'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('photo') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if (isset($object->photo))
                                        <div class="form-group">
                                            <label
                                                class="control-label col-lg-2">{{ __('dashboard.current_image') }}</label>
                                            <div class="col-lg-10">
                                                <img alt="" width="100" height="75"
                                                    src="/uploads/{{ $object->photo }}">
                                            </div>

                                        </div>
                                    @endif

                                    <div class="form-group{{ $errors->has('photos') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">{{ __('dashboard.product_images') }}
                                        </label>
                                        <div class="col-lg-10">
                                            <input multiple type="file" id="product_photos" name="photos[]"
                                                value="{{ old('photos[]') }}" class="file-input"
                                                data-show-caption="false" data-show-upload="false"
                                                data-browse-class="btn btn-primary btn-xs"
                                                data-remove-class="btn btn-default btn-xs">
                                            @if ($errors->has('photos'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('photos') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @if (isset($object) && \App\Models\ProductPhotos::where('product_id', $object->id)->count() > 0)

                                        <div class="form-group">
                                            <label
                                                class="control-label col-lg-2">{{ __('dashboard.current_images') }}</label>

                                            <div class="col-lg-10">
                                                @foreach (\App\Models\ProductPhotos::where('product_id', $object->id)->get() as $photo)
                                                    <div align="center"
                                                        style="height: 75px;width: 100px;float: right;margin-right: 5px">
                                                        <img alt="" width="100" height="75"
                                                            src="/uploads/{{ $photo->photo }}">
                                                        <a href="/admin-panel/delete-photo-product/{{ $photo->id }}"
                                                            style="text-align: center">{{ __('dashboard.delete') }}</a>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>

                                    @endif

                                </fieldset>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                {{ __('dashboard.save') }} <i class="icon-arrow-left13 position-right"></i></button>
                        </div>

                    </div>
                </div>


                <!-- Footer -->
                @include('admin.footer')
                <!-- /footer -->
                <!-- /content area -->
            </form>
        </div>

        <script>
            $(function() {
                $("input[type='submit']").click(function() {
                    var $fileUpload = $("#product_photos");
                    if (parseInt($fileUpload.get(0).files.length) > 2) {
                        alert("You can only upload a maximum of 2 files");
                    }
                });
            });
            $('.country_id').change(function() {
                var country_id = $('.country_id').val();
                $.ajax({
                    url: '/getStates/' + country_id,
                    success: function(data) {
                        $('.state_id').html(data);
                    }
                });

            });
            $('#regions').on('change', function() {
                var regions = $('#regions').val();
                let selected = ''

                console.log(regions);
                $.ajax({
                    url: '/admin-panel/getStatesByRegions/' + regions,
                    success: function(data) {
                        var htmlData = ''
                        $.each(data, function(k, v) {
                            selected = ''
                            if (regions.includes('"' + v.id + '"')) {
                                selected = 'selected'
                            }
                            htmlData += '<option ' + selected + ' value=' + v.id + '>' + v.name +
                                '</option>'
                        })
                        $('#states').html(htmlData).select2();
                    }
                });

            });
            $('#has_regions').change(function() {
                if ($(this).is(":checked")) {
                    $('.choose-regions').show();
                } else {
                    $('.choose-regions').hide();

                }
            })
        </script>

    @stop
