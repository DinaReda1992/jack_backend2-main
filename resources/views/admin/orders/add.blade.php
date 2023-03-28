@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script src="{{ mix('js/app.js') }}"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script>
        $('#search_box').on('keyup', function() {
            /*if($(this).val().length < 4 ){
            	$('#user_id').val('');
            	$('#searchList').fadeOut();
            	return;
            }*/
            var query = $(this).val();
            if (query != '') {
                var _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "/admin-panel/warehouse-purchases/search-users",
                    method: "GET",
                    data: {
                        query: query,
                        _token: _token
                    },
                    success: function(data) {
                        $('#searchList').fadeIn();
                        $('#searchList').html(data);
                    }
                });
            }
        });

        $(document).on('click', 'li a.get-user', function(e) {
            e.preventDefault()
            $('#search_box').val($(this).data('name'));
            $('#user_id').val($(this).data('id'));
            $('#searchList').fadeOut();
        });
        $(document).on('click', '.add-user', function(e) {
            e.preventDefault()
            $('.add-user-con,.add-order-con').toggle()
            $('#user_id').val('');
            $('#searchList').fadeOut();
        });
    </script>
@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.purchase_order')}}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}</a>
                    </li>
                    <li><a href="/admin-panel/warehouse-purchases">{{__('dashboard.purchase_orders')}}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.purchase_order')}}</li>
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
                    <h5 class="panel-title">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.purchase_order')}} </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="panel-body">
                    <form method="get" class="form-horizontal add-order-con"
                        action="{{ isset($object) ? '/admin-panel/warehouse-purchases/' . $object->id : '/admin-panel/warehouse-purchases/create' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{__('dashboard.search_by_supplier_id_or_supplier_phone')}}</label>
                                <div class="col-lg-10">
                                    <input type="hidden" id="user_id" name="user_id" value="">
                                    <input autocomplete="off" type="text" id="search_box" name="username"
                                        class="form-control" placeholder="{{ __('dashboard.search') }}">
                                    <div id="searchList">
                                    </div>
                                    @if ($errors->has('user_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('user_id') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{__('dashboard.purchase_order')}}
                                <i class="icon-arrow-left13 position-right"></i></button>
                        </div>
                    </form>


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
