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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} متجر</h4>
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
							<li><a href="/provider-panel/marchant">عرض المتاجر</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} متجر</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} متجر </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>



						<div class="panel-body">

							<form enctype="multipart/form-data"  method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/marchant/'.$object->id : '/provider-panel/marchant'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />

                    	@endif




									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل اسم المتجر</label>
										<div class="col-lg-10">
										<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل اسم المتجر">
										@if ($errors->has('name'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>

									  <div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">إختر العضو صاحب المتجر</label>
										<div class="col-lg-10">



													<select name="user_id" data-placeholder="ابحث بالاسم" class="select-minimum">
														<option></option>
														<optgroup label="إختر العضو صاحب المتجر">
															@foreach($users as $user)
															<option value="{{ $user -> id }}"  {{ isset($object) && $object->user_id==$user->id ? 'selected' : (old('user_id') == $user->id ? 'selected' : '') }}>{{ $user->full_name() . " ( " . $user->email . "  )" }}</option>
															@endforeach

														</optgroup>

													</select>



										{{-- <select name="user_id" class="form-control">
										<option value="">إختر العضو صاحب المتجر</option>
										@foreach($users as $user)
										<option value="{{ $user -> id }}"  {{ isset($object) && $object->user_id==$user->id ? 'selected' : (old('user_id') == $user->id ? 'selected' : '') }}>{{ $user->full_name() . " ( " . $user->email . "  )" }}</option>
										@endforeach
										</select> --}}
										@if ($errors->has('user_id'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('user_id') }}</strong>
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

									<div class="form-group{{ $errors->has('longitude') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">longitude</label>
										<div class="col-lg-10">
										<input type="text" name="longitude" value="{{ isset($object) ? $object->longitude  : old('longitude')  }}" class="form-control" placeholder="longitude">
										@if ($errors->has('longitude'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('longitude') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('latitude') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">latitude</label>
										<div class="col-lg-10">
										<input type="text" name="latitude" value="{{ isset($object) ? $object->latitude  : old('latitude')  }}" class="form-control" placeholder="latitude">
										@if ($errors->has('latitude'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('latitude') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نبذة عن المتجر</label>
										<div class="col-lg-10">
										<textarea name="description" class="form-control" >
										{{ isset($object) ? $object->description  : old('description')  }}
										</textarea>
										@if ($errors->has('description'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
		                                @endif
										</div>

									</div>



									<div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
									<label class="control-label col-lg-2">أدخل صورة المتجر</label>
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
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} متجر <i class="icon-arrow-left13 position-right"></i></button>
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
