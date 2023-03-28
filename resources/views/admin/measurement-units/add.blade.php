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
    <script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>

    <script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
    <link rel="stylesheet" href="/js/selectize/css/selectize.css">
    <script src="/js/selectize/js/standalone/selectize.js"></script>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

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
                        - {{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.unit_of_measurement')}}</h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/measurement-units">
                            <button type="button" class="btn btn-success" name="button"> {{__('dashboard.view_units_of_measurement')}}</button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i></a></li>
                    <li><a href="/admin-panel/measurement-units">{{__('dashboard.view_units_of_measurement')}}</a></li>
                    <li class="active">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.unit_of_measurement')}} </li>
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
                    <h5 class="panel-title">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }} {{__('dashboard.unit_of_measurement')}} </h5>
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
                        action="{{ isset($object) ? '/' . app()->getLocale() .'/admin-panel/measurement-units/' . $object->id : '/' . app()->getLocale() .'/admin-panel/measurement-units' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{__('dashboard.unit_of_measurement_ar')}} </label>
                                <div class="col-lg-10">
                                    <input type="text" name="name"
                                        value="{{ isset($object) ? $object->name : old('name') }}" class="form-control"
                                        placeholder="{{__('dashboard.unit_of_measurement_ar')}} ">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('name_en') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">{{__('dashboard.unit_of_measurement_en')}}</label>
                                <div class="col-lg-10">
                                    <input type="text" name="name_en"
                                        value="{{ isset($object) ? $object->name_en : old('name_en') }}"
                                        class="form-control" placeholder="{{__('dashboard.unit_of_measurement_en')}}">
                                    @if ($errors->has('name_en'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name_en') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>


                        </fieldset>


                        <div class="text-right">
                            <button type="submit"
                                class="btn btn-primary">{{ isset($object) ? __('dashboard.edit') : __('dashboard.add') }}
                                {{__('dashboard.unit_of_measurement')}} <i class="icon-arrow-left13 position-right"></i></button>
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
