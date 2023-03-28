@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/selects/select2.min.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_select2.js"></script>
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
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.balance') }}</h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }} </a>
                    </li>
                    <li><a href="/admin-panel/display-balance">{{ __('dashboard.view_all_balances_added') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.balance') }}</li>
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
                    <h5 class="panel-title">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.balance') }} </h5>
                    <p style="color: red"></p>

                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">

                    <form enctype="multipart/form-data" method="post" class="form-horizontal"
                        action="/{{app()->getLocale()}}/admin-panel/add-balance">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{__('dashboard.select_client')}}</label>
                                <div class="col-lg-10">

                                    <select name="user_id" data-placeholder="{{__('dashboard.search_by_phone')}}" class="select-minimum">
                                        <option></option>
                                        <optgroup label="{{__('dashboard.select_client')}}">
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ isset($object) && $object->user_id == $user->id ? 'selected' : (old('user_id') == $user->id ? 'selected' : '') }}>
                                                    {{ $user->username . ' ( ' . $user->email . '  ) -' . '0' . ltrim($user->phone, 0) }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    @if ($errors->has('user_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('user_id') }}</strong>
                                        </span>
                                    @endif


                                </div>
                            </div>


                            <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.balance') }}</label>
                                <div class="col-lg-10">
                                    <input type="number" name="price"
                                        value="{{ isset($object) ? $object->price : old('price') }}" class="form-control"
                                        placeholder="{{ __('dashboard.balance') }}">
                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('price') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.additional_notes') }} </label>
                                <div class="col-lg-10">
                                    <textarea class="form-control" name="notes">{{ old('notes') }}</textarea>

                                    @if ($errors->has('notes'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('notes') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.balance') }} <i class="icon-arrow-left13 position-right"></i></button>
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
