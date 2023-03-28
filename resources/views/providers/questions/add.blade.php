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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} سؤال</h4>
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
							<li><a href="/provider-panel/questions">عرض الأسئلة</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} سؤال</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} سؤال </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/questions/'.$object->id : '/provider-panel/questions'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
								   <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">إختر القسم</label>
										<div class="col-lg-10">
										<select name="category_id" class="form-control">
										<option value="">إختر القسم</option>
										@foreach($categories as $category)
										<option value="{{ $category->id }}"  {{ isset($object) && $object->category_id==$category->id ? 'selected' : (old('category_id') == $category->id ? 'selected' : '') }}>{{ $category->name }}</option>
										@endforeach
										</select>
										@if ($errors->has('category_id'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('category_id') }}</strong>
		                                    </span>
		                                @endif
										</div>
										
									</div>
								   
								   
									<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل السؤال</label>
										<div class="col-lg-10">
										<input type="text" name="name" value="{{ isset($object) ? $object->name  : old('name')  }}" class="form-control" placeholder="أدخل السؤال">
										@if ($errors->has('name'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('name') }}</strong>
		                                    </span>
		                                @endif
										</div>
										
									</div>

									<div class="form-group{{ $errors->has('required') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">هل السؤال اجباري</label>
										<div class="col-lg-10">
											<label class="radio-inline">
												<input type="checkbox" name="required" value="1" {{ @$object->required==1 ? 'checked': '' }}> اجباري
											</label>

											@if ($errors->has('required'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('required') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>
									
									
									
								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} السؤال <i class="icon-arrow-left13 position-right"></i></button>
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