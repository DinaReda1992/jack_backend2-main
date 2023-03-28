@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>

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
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.bank_account')}}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }} </a>
                    </li>
                    <li><a href="/admin-panel/bank_accounts">{{ __('dashboard.view_bank_accounts') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.bank_account')}}</li>
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
                    <h5 class="panel-title">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.bank_account')}}
                    </h5>
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
                        action="{{ isset($object) ? '/' . app()->getLocale() . '/admin-panel/bank_accounts/' . $object->id : '/' . app()->getLocale() . '/admin-panel/bank_accounts' }}"
                        enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('bank_name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.enter_bank_name') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="bank_name"
                                        value="{{ isset($object) ? $object->bank_name : old('bank_name') }}"
                                        class="form-control" placeholder="{{ __('dashboard.enter_bank_name') }}">
                                    @if ($errors->has('bank_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('bank_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('account_name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.account_owner_name') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="account_name"
                                        value="{{ isset($object) ? $object->account_name : old('account_name') }}"
                                        class="form-control" placeholder="{{ __('dashboard.account_owner_name') }}">
                                    @if ($errors->has('account_name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('account_name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('account_number') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.enter_account_number') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="account_number"
                                        value="{{ isset($object) ? $object->account_number : old('account_number') }}"
                                        class="form-control" placeholder="{{ __('dashboard.enter_account_number') }}">
                                    @if ($errors->has('account_number'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('account_number') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.bank_account') }} <i
                                    class="icon-arrow-left13 position-right"></i></button>
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
