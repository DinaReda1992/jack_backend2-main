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
    <link rel="stylesheet" href="/js/selectize/css/selectize.css">
    <script src="/js/selectize/js/standalone/selectize.js"></script>

    <!-- /theme JS files -->
@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> -
                        {{ isset($object) ? 'تعديل' : 'إضافة' }} مورد رئيسى</h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/main-suppliers"><button type="button" class="btn btn-success" name="button">
                                عرض الموردين الرئيسيين </button></a>
                    </div>
                </div>
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i>الرئيسية</a></li>
                    <li><a href="/admin-panel/main-suppliers">عرض الموردين الرئيسيين </a></li>
                    <li class="active">{{ isset($object) ? 'تعديل' : 'إضافة' }} مورد رئيسى</li>
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
                    <h5 class="panel-title">{{ isset($object) ? 'تعديل' : 'إضافة' }} مورد رئيسي </h5>
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
                        action="{{ isset($object) ? '/admin-panel/main-suppliers/' . $object->id : '/admin-panel/main-suppliers' }}">
                        {!! csrf_field() !!}
                        @if (isset($object))
                            <input type="hidden" name="_method" value="PATCH" />
                        @endif
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">أدخل اسم المورد الرئيسي </label>
                                <div class="col-lg-10">
                                    <input type="text" name="name"
                                        value="{{ isset($object) ? $object->name : old('name') }}" class="form-control"
                                        placeholder="أدخل اسم المورد الرئيسي  ">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </fieldset>



                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ isset($object) ? 'تعديل' : 'إضافة' }} المورد الرئيسي <i
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
