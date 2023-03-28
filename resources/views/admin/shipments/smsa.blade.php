@extends('admin.layout')
@section('js_files')
    <!-- Theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
    <!-- /theme JS files -->
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>

@stop
@section('content')
    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Page header -->
        <div class="page-header">
            <div class="page-header-content">
                <div class="page-title">
                    <h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} اعدادات سمسا </h4>
                </div>
                <div class="heading-elements">
                    <div class="heading-btn-group">
                        <a href="/admin-panel/shipments"><button type="button" class="btn btn-success" name="button">  عرض طرق الشحن</button></a>
                    </div>
                </div>

                <!-- 						<div class="heading-elements"> -->
                <!-- 							<div class="heading-btn-group"> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-bars-alt text-primary"></i><span>Statistics</span></a> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calculator text-primary"></i> <span>Invoices</span></a> -->
                <!-- 								<a href="#" class="btn btn-link btn-float has-text"><i class="icon-calendar5 text-primary"></i> <span>Schedule</span></a> -->
                <!-- 							</div> -->
                <!-- 						</div> -->
            </div>

            <div class="breadcrumb-line">
                <ul class="breadcrumb">
                    <li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i></a></li>
                    <li><a href="/admin-panel/shipments">عرض طرق السحن</a></li>
                    <li class="active">{{ isset($object)? 'تعديل':'إضافة' }} اعدادات سمسا </li>
                </ul>



                <!-- 						<ul class="breadcrumb-elements"> -->
                <!-- 							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Support</a></li> -->
                <!-- 							<li class="dropdown"> -->
                <!-- 								<a href="#" class="dropdown-toggle" data-toggle="dropdown"> -->
                <!-- 									<i class="icon-gear position-left"></i> -->
                <!-- 									Settings -->
                <!-- 									<span class="caret"></span> -->
                <!-- 								</a> -->

                <!-- 								<ul class="dropdown-menu dropdown-menu-right"> -->
                <!-- 									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li> -->
                <!-- 									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li> -->
                <!-- 									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li> -->
                <!-- 									<li class="divider"></li> -->
                <!-- 									<li><a href="#"><i class="icon-gear"></i> All settings</a></li> -->
                <!-- 								</ul> -->
                <!-- 							</li> -->
                <!-- 						</ul> -->
            </div>
        </div>
        <!-- /page header -->

        <!-- Content area -->
        <div class="content">

        @include('admin.message')

        <!-- Form horizontal -->
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} اعدادات سمسا </h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                            <li><a data-action="reload"></a></li>
                            <li><a data-action="close"></a></li>
                        </ul>
                    </div>
                </div>



                <div class="panel-body">

                    <form enctype="multipart/form-data" method="post" class="form-horizontal" action="/admin-panel/smsa_update">
                        {!! csrf_field() !!}
                        <fieldset class="content-group">
                            <div class="form-group{{ $errors->has('passkey') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">Passkey </label>
                                <div class="col-lg-10">
                                    <input type="text" name="passkey" value="{{ isset($object) ? $object->passkey  : old('passkey')  }}" class="form-control" placeholder="Enter passkey ">
                                    @if ($errors->has('passkey'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('passkey') }}</strong>
		                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="control-label col-lg-2">الاسم</label>
                                <div class="col-lg-10">
                                    <input  type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل اسم المستخدم">
                                    @if ($errors->has('name'))
                                        <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
                                    @endif
                                </div>
                            </div>
{{--                            <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">--}}
{{--                                <label class="control-label col-lg-2">تفعيل طريقة الشحن </label>--}}
{{--                                <div class="col-md-10">--}}
{{--                                    <p><input type="checkbox" class="js-switch" name="status" {{ isset($object) ? ($object->status==1?'checked':'')  :'checked'  }} />  </p>--}}
{{--                                </div>--}}
{{--                            </div>--}}




                        </fieldset>



                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} اعدادات سمسا  <i class="icon-arrow-left13 position-right"></i></button>
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
    <script>
        var elem = document.querySelector('.js-switch');
        var init = new Switchery(elem);

    </script>
@stop