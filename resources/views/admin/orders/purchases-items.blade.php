@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script src="{{ mix('js/app.js') }}"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->

@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> - {{ __('dashboard.warehouse') }}
                    </h4>
                </div>

            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
                    <li><a href="/admin-panel/warehouse">{{ __('dashboard.warehouse') }}</a></li>
                    <li class="active">{{__('dashboard.Create a order from the supplier')}} {{ $object->username }} </li>
                </ul>

            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

            @include('admin.message')

            <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">
                    @if (count($object->products) > 0)
                        <create_purchase
                            @isset($new)
											:new_order="{{ $new }}"
											:user_id="{{ $object->id }}"
										@endisset
                            :object="{{ $object }}" />
                    @else
                        <span>{{ __('dashboard.not_found_product') }}</span>
                    @endif
                </div>


            </div>
            <!-- /form horizontal -->


            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->

        </div>
        <!-- /content area -->

    </div>
@stop
