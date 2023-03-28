@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<script type="text/javascript" src="/assets/js/core/libraries/jquery_ui/full.min.js"></script>
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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} الشركة</h4>
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
							<li><a href="/admin-panel/companies">عرض شركات العقار</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} الشركة</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} الشركة </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">

							<form enctype="multipart/form-data"  method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/companies/'.$object->id : '/admin-panel/companies'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />

                    	@endif




									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم الشركة</label>
										<div class="col-lg-10">
										<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل اسم الشركة">
										@if ($errors->has('name'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>


								<div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
									<label class="control-label col-lg-2">نوع الشركة</label>
									<div class="col-lg-10">
										<input type="text" name="type" value="{{ isset($object) ? $object->type  : old('type')  }}" class="form-control" placeholder="نوع الشركة">
										@if ($errors->has('type'))
											<span class="help-block">
		                                        <strong>{{ $errors->first('type') }}</strong>
		                                    </span>
										@endif
									</div>

								</div>

								<div class="form-group{{ $errors->has('state_id') ? ' has-error' : '' }}">
									<label class="control-label col-lg-2">إختر المدينة</label>
									<div class="col-lg-10">
										<select name="state_id" class="form-control">
											<option value="">إختر المدينة</option>
											@foreach(\App\Models\States::all() as $state)
												<option value="{{ $state->id }}"  {{ isset($object) && $object->state_id==$state->id ? 'selected' : (old('state_id') == $state->id ? 'selected' : '') }}>{{ $state->name }}</option>
											@endforeach
										</select>
										@if ($errors->has('state_id'))
											<span class="help-block">
		                                        <strong>{{ $errors->first('state_id') }}</strong>
		                                    </span>
										@endif
									</div>

								</div>

								<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
									<label class="control-label col-lg-2">عنوان الشركة</label>
									<div class="col-lg-10">
										<input type="text" name="address" value="{{ isset($object) ? $object->address  : old('address')  }}" class="form-control" placeholder="عنوان الشركة">
										@if ($errors->has('address'))
											<span class="help-block">
		                                        <strong>{{ $errors->first('address') }}</strong>
		                                    </span>
										@endif
									</div>

								</div>

									<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">رقم الهاتف</label>
										<div class="col-lg-10">
										<input type="text" name="phone" value="{{ isset($object) ? $object->phone  : old('phone')  }}" class="form-control" placeholder="أدخل رقم الهاتف">
										@if ($errors->has('phone'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('phone') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">البريد الاكتروني للشركة</label>
										<div class="col-lg-10">
										<input type="text" name="email" value="{{ isset($object) ? $object->email  : old('email')  }}" class="form-control" placeholder="البريد الاكتروني للشركة">
										@if ($errors->has('email'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('email') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>


									<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نبذة عن الشركة</label>
										<div class="col-lg-10">
										<textarea name="description" class="form-control" >{{ isset($object) ? $object->description  : old('description')  }}</textarea>
										@if ($errors->has('description'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>



									<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
									<label class="control-label col-lg-2">أدخل صورة الشركة</label>
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
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الشركة <i class="icon-arrow-left13 position-right"></i></button>
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
