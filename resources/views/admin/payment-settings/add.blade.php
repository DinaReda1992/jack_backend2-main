@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switch.min.js"></script>

    <script type="text/javascript" src="/assets/js/pages/form_checkboxes_radios.js"></script>
    <script>
        function resetColor(element) {
            $(element).val('#000');
        }
    </script>
    <!-- /theme JS files -->
    <style>
        .radio-inline,
        .checkbox-inline {
            padding-right: 0px;
            margin-left: 75px;

        }
    </style>

@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span>
                        - {{ __('dashboard.settings') }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
                    <li class="active">{{ __('dashboard.payment_methods') }}</li>
                </ul>
            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

            @include('admin.message')
            <form enctype="multipart/form-data" method="post" class="form-horizontal" action="/admin-panel/payment-settings">
                {!! csrf_field() !!}

                <?php foreach(\App\Models\PaymentSettings::orderBy('orders', 'ASC')->groupBy('type')->get() as  $value){ ?>
                <!-- Form horizontal -->
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title"> {{ $value->type }} </h5>
                    </div>
                    <div class="panel-body">

                        <?php foreach (\App\Models\PaymentSettings::where('type', '=', $value->type)->where('hidden', 0)->get() as  $value1){ ?>
                        <fieldset class="content-group">

                            @if ($value1->input_type == 'radio')

                                <div class="form-group">
                                    <label class="control-label col-lg-2">{{ $value1->name }} </label>
                                    <div class="col-lg-10">
                                        <?php $old_user_type = old('user_type_id'); ?>
                                        <label class="radio-inline">
                                            <input type="radio" value="0" name="settings[{{ $value1->id }}]"
                                                {{ $value1->value == 0 ? 'checked="checked"' : '' }}>
                                            @if ($value1->option_name == 'production')
                                                test
                                            @else
                                                لا
                                            @endif

                                        </label>

                                        <label class="radio-inline">
                                            <input type="radio" value="1" name="settings[{{ $value1->id }}]"
                                                {{ $value1->value == 1 ? 'checked="checked"' : '' }}>
                                            @if ($value1->option_name == 'production')
                                                production
                                            @else
                                                نعم
                                            @endif
                                        </label>
                                        <span class="help-block">
                                            <strong>{{ $value1->note }}</strong>
                                        </span>
                                    </div>
                                </div>
                            @elseif($value1->input_type == 'switch')
                                <div class="form-group">
                                    <label class="control-label col-lg-2">{{ $value1->name }} </label>
                                    <div class="col-md-10">
                                        <div class="checkbox checkbox-switchery switchery-sm switchery-double">
                                            <input type="checkbox" name="settings[{{ $value1->id }}]"
                                                class="switchery sweet_switch"
                                                {{ $value1->value == 1 ? 'checked="checked"' : '' }} />
                                        </div>
                                        <span class="help-block">
                                            <strong>{{ $value1->note }}</strong>
                                        </span>

                                    </div>
                                </div>
                            @elseif($value1->input_type == 'radio-langs')
                                <div class="form-group">
                                    <label class="control-label col-lg-2">{{ $value1->name }} </label>
                                    <div class="col-lg-10">
                                        <?php $old_user_type = old('user_type_id'); ?>
                                        <label class="radio-inline">
                                            <input type="radio" value="1" name="settings[{{ $value1->id }}]"
                                                {{ $value1->value == 1 ? 'checked="checked"' : '' }}>
                                            العربية
                                        </label>

                                        <label class="radio-inline">
                                            <input type="radio" value="2" name="settings[{{ $value1->id }}]"
                                                {{ $value1->value == 2 ? 'checked="checked"' : '' }}>
                                            الانجليزية
                                        </label>

                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="control-label col-lg-2">{{ $value1->name }}</label>
                                    <div class="{{ $value1->input_type == 'color' ? 'col-lg-8' : 'col-lg-10' }}">
                                        <input type="{{ $value1->input_type == 'color' ? 'color' : 'text' }}"
                                            name="settings[{{ $value1->id }}]" value="{{ $value1->value }}"
                                            class="form-control option{{ $value1->id }}">

                                        <span class="help-block">
                                            <strong>{{ $value1->note }}</strong>
                                        </span>

                                    </div>
                                    @if ($value1->input_type == 'color')
                                        <div class="col-md-2">
                                            <a onclick="resetColor('.option{{ $value1->id }}')"><i
                                                    style="font-size: 36px;" class="fa fa-refresh"></i></a>
                                        </div>
                                    @endif

                                </div>
                            @endif


                        </fieldset>
                        <?php } ?>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary"> {{ __('dashboard.edit') }} {{ __('dashboard.settings') }}<i
                                    class="icon-arrow-left13 position-right"></i></button>
                        </div>

                    </div>
                </div>

                <!-- /form horizontal -->
                <?php } ?>
            </form>
            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
@stop
