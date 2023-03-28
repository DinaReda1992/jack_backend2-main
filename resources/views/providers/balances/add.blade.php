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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} رصيد</h4>
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
							<li><a href="/provider-panel/display-balance">عرض جميع الأرصدة المضافة</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} رصيد</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} رصيد </h5>
							<p style="color: red">تحذير : تأكد من المبلغ المضاف قبل الارسال لانه اذا تم إضافة الرصيد لحساب المندوب سيصل إشعار للمندوب بإضافة رصيد لحسابه ولا يمكنك التعديل أو الحذف للرصيد المضاف .</p>

							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form enctype="multipart/form-data" method="post" class="form-horizontal" action="/provider-panel/add-balance">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
									<div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">إختر المندوب الذي تود إضافة رصيد له</label>
										<div class="col-lg-10">

											<select name="user_id" data-placeholder="ابحث برقم الجوال" class="select-minimum">
												<option></option>
												<optgroup label="اختر الشخص">
													@foreach($users as $user)
														<option value="{{ $user -> id }}"  {{ isset($object) && $object->user_id==$user->id ? 'selected' : (old('user_id') == $user->id ? 'selected' : '') }}>{{ $user->username . " ( " . $user->email . "  ) -" . "0" . ltrim($user->phone,0) }}</option>
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
										<label class="control-label col-lg-2">أدخل الرصيد</label>
										<div class="col-lg-10">
										<input type="number" name="price" value="{{ isset($object) ? $object->price  : old('price')  }}" class="form-control" placeholder="أدخل الرصيد">
										@if ($errors->has('price'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('price') }}</strong>
		                                    </span>
		                                @endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">ملاحظات إضافية </label>
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
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الرصيد <i class="icon-arrow-left13 position-right"></i></button>
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