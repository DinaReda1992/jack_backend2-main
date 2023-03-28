@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/editors/summernote/summernote.min.js"></script>

	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<script type="text/javascript" src="/assets/js/pages/editor_summernote.js"></script>

	<!-- /theme JS files -->

@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} وسط الموقع</h4>
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
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} وسط الموقع</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} وسط الموقع </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post" enctype="multipart/form-data" class="form-horizontal" action="{{ isset($object)? '/admin-panel/middle-section/'.$object->id : '/admin-panel/middle-section'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
								   
									<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">العنوان الرئيسى</label>
										<div class="col-lg-10">
										<input type="text" name="title" value="{{ isset($object) ? $object->title  : old('title')  }}" class="form-control" placeholder="ادخل العنوان باللغة العربية">
										@if ($errors->has('title'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('title') }}</strong>
		                                    </span>
		                                @endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('title_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">العنوان الرئيسى بالانجليزية</label>
										<div class="col-lg-10">
											<input style="direction: ltr" type="text" name="title_en" value="{{ isset($object) ? $object->title_en  : old('title_en')  }}" class="form-control" placeholder="أدخل العنوان بالانجليزية">
											@if ($errors->has('title_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('title_en') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">ادخل النص الاضافى بالعربية</label>
										<div class="col-lg-10">
											<textarea  rows="8" cols="40" name="description"  class="form-control">{{ isset($object) ? $object->description  : old('description')  }}</textarea>
											{{--<input type="text" name="answer" value="{{ isset($object) ? $object->answer  : old('answer')  }}" class="form-control" placeholder="أدخل الاجابة">--}}
											@if ($errors->has('description'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									<div style="direction: ltr !important;" class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل النص الاضافى بالنانجليزية</label>
										<div class="col-lg-10">
											<textarea  rows="8" cols="40" name="description_en" class="form-control">{{ isset($object) ? $object->description_en  : old('description_en')  }}</textarea>
											{{--<input type="text" name="answer" value="{{ isset($object) ? $object->answer  : old('answer')  }}" class="form-control" placeholder="أدخل الاجابة">--}}
											@if ($errors->has('description_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('description_en') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>
									<div class="form-group{{ $errors->has('btn_text') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نص الزر بالعربى</label>
										<div class="col-lg-10">
											<input type="text" name="btn_text" value="{{ isset($object) ? $object->btn_text  : old('btn_text')  }}" class="form-control" placeholder="ادخل نص الزر باللغة العربية">
											@if ($errors->has('btn_text'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('btn_text') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('btn_text_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">نص الزر بالانجليزى</label>
										<div class="col-lg-10">
											<input type="text" name="btn_text_en" value="{{ isset($object) ? $object->btn_text_en  : old('btn_text_en')  }}" class="form-control" placeholder="ادخل نص الزر باللغة الانجليزية">
											@if ($errors->has('btn_text_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('btn_text_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

									<div class="form-group{{ $errors->has('btn_link') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">رابط الزر</label>
										<div class="col-lg-10">
											<input type="text" name="btn_link" value="{{ isset($object) ? $object->btn_link  : old('btn_link')  }}" class="form-control" placeholder="ادخل رابط الزر">
											@if ($errors->has('btn_link'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('btn_link') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">صورة الخلفية </label>
										<div class="col-lg-5">
											<input type="file" name="image" value="" class="form-control" >

											<span class="help-block">
																							<strong>{{$errors->has("image")?$errors->has("image"):"قم برفع صورة الخليفة"}}</strong>
																					</span>

										</div>
										@if(isset($object))
											@if(!empty($object -> image))
												<div class="col-lg-5">
													<img width="150" height="100px" src="/uploads/{{ $object -> image }}" alt="" />
													<span class="help-block">
																							<strong>الصورة الحالية</strong>
																					</span>

												</div>
											@endif
										@endif
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