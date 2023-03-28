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
	<style>
		.shop-img{
			width: 35px;
			height: 35px;
			border-radius: 50%;
			margin-left: 5px;
		}
	</style>

@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - اجراء سحب رصيد للطلب رقم :{{$order->id}}</h4>
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
							<li><a href="/admin-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li><a href="/admin-panel/request-money">طلبات سحب الرصيد</a></li>
							<li class="active">بيانات تحويل الرصيد</li>
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
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">بيانات الطلب </h5>
							<div class="heading-elements">
								<ul class="icons-list">
									<li><a data-action="collapse"></a></li>
									<li><a data-action="reload"></a></li>
									<li><a data-action="close"></a></li>
								</ul>
							</div>
						</div>



						<div class="panel-body">
							<fieldset class="content-group">
								<!-- 									<legend class="text-bold">Basic inputs</legend> -->

								<div class="form-group row">
									<label class="control-label col-lg-2">صاحب الطلب</label>
									<div class="col-lg-10">
										<img class="shop-img" src="/uploads/{{@$order->photo?@$order->photo:'placeholder.png' }}">{{ @$order->username }}
{{--										<span>{{$order->username}}</span>--}}
									</div>
								</div>
								<hr>
								<div class="form-group row">
									<label class="control-label col-lg-2">المبلغ المطلوب</label>
									<div class="col-lg-10">
										<span>{{$order->price}}</span>
									</div>

								</div>
								<hr>

								<div class="form-group row">
									<label class="control-label col-lg-2">الرصيد الحالى</label>
									<div class="col-lg-10">
										<span>{{ \App\Models\Balance::where('user_id',$order->user_id)->sum('price')-\App\Models\Balance::where('user_id',$order->user_id)->sum('site_profits') }}</span>
									</div>

								</div>
								<hr>

								<div class="form-group row">
									<label class="control-label col-lg-2">تاريخ الطلب</label>
									<div class="col-lg-10">
										<span>{{$order->created_at->diffForHumans()}}</span>
									</div>

								</div>

							</fieldset>
						</div>
					</div>
					<!-- Form horizontal -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">بيانات التحويل </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">

							<form method="post" enctype="multipart/form-data" class="form-horizontal" action="{{ '/admin-panel/sendBalance/'.$order->id   }}">
								{!! csrf_field() !!}
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->

									<div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">القيمة التحويل بالريال</label>
										<div class="col-lg-10">
										<input type="number" name="price" value="{{ isset($object) ? $object->price  : old('price')  }}" class="form-control" placeholder="حدد قيمة التحويل">
										@if ($errors->has('price'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('price') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>
									<div class="form-group{{ $errors->has('bank_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">حدد البنك</label>
										<div class="col-lg-10">
											<select name="bank_id" class="form-control">
												<option value="">اختر البنك المحول له</option>
												@foreach(\App\Models\BankAccounts::where('user_id',$order->user_id)->get() as $bank)
													<option value="{{ $bank->id }}"  {{ isset($object) && $object->bank_id==$bank->id ? 'selected' : (old('bank_id') == $bank->id ? 'selected' : '') }}>{{ $bank->bank_name }}</option>
												@endforeach
											</select>
											@if ($errors->has('bank_id'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('bank_id') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل صورة التحويل</label>
										<div class="col-lg-10">

											<input type="file" name="photo" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">
											@if ($errors->has('photo'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('photo') }}</strong>
		                                    </span>
											@endif



										</div>

									</div>
									@if(isset($object->photo))
										<div class="form-group">
											<label class="control-label col-lg-2">الصورة الحالية</label>
											<div class="col-lg-10">
												<img alt="" width="100" height="75" src="/uploads/{{ $object  -> photo }}">
											</div>

										</div>
									@endif
								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">ارسال <i class="icon-arrow-left13 position-right"></i></button>
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
