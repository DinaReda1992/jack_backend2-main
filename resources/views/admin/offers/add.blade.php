@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script src="{{ mix('js/app.js') }}"></script>

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
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
    <!-- InputMask -->

    {{-- <script type="text/javascript" src="/assets/js/core/app.js"></script> --}}

    {{-- <script type="text/javascript" src="/assets/js/pages/components_modals.js"></script> --}}

    <!-- /theme JS files -->
    <script>
        var switchElem = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
        switchElem.forEach(function(html) {
            var switchery = new Switchery(html, {
                color: '#64bd63',
                secondaryColor: '#B2BABB'
            });
        });
    </script>
@endsection
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">
        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.offer')}} </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/offers"><button type="button" class="btn btn-success" name="button"> {{__('dashboard.view_offers')}}</button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i>
                            {{ __('dashboard.home') }}</a></li>
                    <li><a href="/admin-panel/offers"><i class="icon-home2 position-left"></i> {{__('dashboard.view_offers')}}</a></li>
                    <li class="active">{{ __('dashboard.add') }} {{ __('dashboard.offer') }} </li>
                </ul>
            </div>


        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content" style="min-height: 400px">

            @include('admin.message')
            <div>
                <create_offer :offer_types="{{ $offer_types }}" />
            </div>



            <!-- Footer -->
            @include('admin.footer')
            <!-- /footer -->
            <!-- /content area -->
        </div>

    </div>
@endsection
