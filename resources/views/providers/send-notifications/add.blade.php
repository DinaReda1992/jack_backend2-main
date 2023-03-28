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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} اشعار</h4>
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
							<li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li><a href="/provider-panel/send_notifications">عرض الاشعارات</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} اشعار</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} اشعار </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/send_notifications/'.$object->id : '/provider-panel/send_notifications'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
								   
									<div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل رسالة الاشعار</label>
										<div class="col-lg-10">
										<input type="text" name="message" value="{{ isset($object) ? $object->message  : old('message')  }}" class="form-control" placeholder="أدخل رسالة الاشعار">
										@if ($errors->has('message'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('message') }}</strong>
		                                    </span>
		                                @endif
										</div>
										
									</div>

{{--									<div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">--}}
{{--										<label class="control-label col-lg-2">لمن يرسل الاشعار</label>--}}
{{--										<div class="col-lg-10">--}}
{{--											<div class="radio">--}}
{{--												<label>--}}
{{--													<input type="radio" name="type" value="0" id="optionsRadios1" {{ @$object->type==0 ? 'checked' : "" }}>--}}
{{--													الكل--}}
{{--												</label>--}}
{{--											</div>--}}
{{--											<div class="radio">--}}
{{--												<label>--}}
{{--													<input type="radio" name="type" value="3" id="optionsRadios1" {{ @$object->type==3 ? 'checked' : "" }}>--}}
{{--													العملاء--}}
{{--												</label>--}}
{{--											</div>--}}
{{--											<div class="radio">--}}
{{--												<label>--}}
{{--													<input type="radio" name="type" value="4" id="optionsRadios2" {{ @$object->type==4 ? 'checked' : "" }}>--}}
{{--المناديب--}}
{{--												</label>--}}
{{--											</div>--}}
{{--										</div>--}}

{{--									</div>--}}




								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الاشعار <i class="icon-arrow-left13 position-right"></i></button>
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