@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
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
                            class="text-semibold">{{ __('dashboard.dashboard') }} </span> -
                        {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.notification') }}
                    </h4>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> {{ __('dashboard.home') }}
                        </a></li>
                    <li><a href="/admin-panel/send_notifications">{{ __('dashboard.view_notifications') }}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                        {{ __('dashboard.notification') }}</li>
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
                        {{ __('dashboard.notification') }} </h5>
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
                        action="{{ isset($object) ? '/' . app()->getLocale() . '/admin-panel/send_notifications/' . $object->id : '/' . app()->getLocale() . '/admin-panel/send_notifications' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <!-- 									<legend class="text-bold">Basic inputs</legend> -->
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.notification_title') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="title"
                                        value="{{ isset($object) ? $object->title : (old('title') ?: 'رسالة من الإدارة') }}"
                                        class="form-control" placeholder="{{ __('dashboard.notification_title') }}">
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('title') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>

                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{ __('dashboard.message') }}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="message"
                                        value="{{ isset($object) ? $object->message : old('message') }}"
                                        class="form-control" placeholder="{{ __('dashboard.message') }}">
                                    @if ($errors->has('message'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('message') }}</strong>
                                        </span>
                                    @endif
                                </div>

                            </div>

                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <label
                                    class="control-label col-lg-2">{{ __('dashboard.who_receive_notification') }}</label>
                                <div class="col-lg-10">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="type" value="0" id="optionsRadios1"
                                                style="margin-{{ __('dashboard.right') }}: 0;{{ __('dashboard.left') }}: 0;"
                                                {{ @$object->type == 0 ? 'checked' : '' }}>
                                            {{ __('dashboard.all') }}
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="type" value="5" id="optionsRadios2"
                                                style="margin-{{ __('dashboard.right') }}: 0;{{ __('dashboard.left') }}: 0;"
                                                {{ @$object->type == 5 ? 'checked' : '' }}>
                                            {{ __('dashboard.clients') }}
                                        </label>
                                    </div>

                                    {{-- <div class="radio">
                                        <label>
                                            <input type="radio" name="type" value="3" id="optionsRadios1"
                                                style="margin-{{ __('dashboard.right') }}: 0;{{ __('dashboard.left') }}: 0;"
                                                {{ @$object->type == 3 ? 'checked' : '' }}>
                                            {{ __('dashboard.suppliers') }}
                                        </label>
                                    </div> --}}
                                </div>
                            </div>
                        </fieldset>
                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{ __('dashboard.notification') }} <i
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
