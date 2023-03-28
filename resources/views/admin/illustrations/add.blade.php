@extends('admin.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('input[value="photo"]').click(function () {
				$('#submit').show();
                $('#photo').show();
                $('#video').hide();
            });

            $('input[value="video"]').click(function () {
                $('#submit').show();
                $('#photo').hide();
                $('#video').show();
            });
        })
	</script>

@stop
@section('content')
			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} شاشة افتتاحية </h4>
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
							<li><a href="/admin-panel/illustrations">عرض الشاشات الافتتاحية</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} شاشة افتتاحية </li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} شاشة افتتاحية  </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form enctype="multipart/form-data"  method="post" class="form-horizontal" action="{{ isset($object)? '/admin-panel/illustrations/'.$object->id : '/admin-panel/illustrations'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	
                    	@endif
								<fieldset class="content-group">
									{{--<div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">--}}
										{{--<label class="control-label col-lg-2">اختر نوع الشاشة</label>--}}
										{{--<div class="col-lg-10">--}}
											{{--<label class="radio-inline"><input type="radio" name="type" value="photo" {{ isset($object) && $object->type == "photo" ? 'checked' : ''  }}>صورة</label>--}}
											{{--<label class="radio-inline"><input type="radio" name="type" value="video" {{ isset($object) && $object->type == "video" ? 'checked' : ''  }}>فيديو</label>--}}
											{{--@if ($errors->has('type'))--}}
												{{--<span class="help-block">--}}
		                                        {{--<strong>{{ $errors->first('type') }}</strong>--}}
		                                    {{--</span>--}}
											{{--@endif--}}
										{{--</div>--}}

									{{--</div>--}}


									{{--<div @if( !isset($object) || $object->type!="video") style="display: none" @endif id="video" class="form-group{{ $errors->has('video_url') ? ' has-error' : '' }}">--}}
										{{--<label class="control-label col-lg-2">أدخل فيديو الشاشة افتتاحية</label>--}}
										{{--<div class="col-lg-10">--}}
										{{--<input type="text" name="video_url" value="{{ isset($object) ? $object->video_url  : old('video_url')  }}" class="form-control" placeholder="أدخل فيديو الشاشة افتتاحية">--}}
										{{--@if ($errors->has('video_url'))--}}
		                                    {{--<span class="help-block">--}}
		                                        {{--<strong>{{ $errors->first('video_url') }}</strong>--}}
		                                    {{--</span>--}}
		                                {{--@endif--}}
										{{--</div>--}}

									{{--</div>--}}


									<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل العنوان بالعربية</label>
										<div class="col-lg-10">
											<input type="text" name="title" value="{{ isset($object) ? $object->title  : old('title')  }}" class="form-control" placeholder="أدخل العنوان بالعربية">
											@if ($errors->has('title'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('title') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>
									<div class="form-group{{ $errors->has('title_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل العنوان بالانجليزية</label>
										<div class="col-lg-10">
											<input style="direction: ltr" type="text" name="title_en" value="{{ isset($object) ? $object->title_en  : old('title_en')  }}" class="form-control" placeholder="أدخل العنوان بالانجليزية">
											@if ($errors->has('title_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('title_en') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>


{{--									<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">--}}
{{--										<label class="control-label col-lg-2">أدخل الوصف بالعربية</label>--}}
{{--										<div class="col-lg-10">--}}
{{--											<input type="text" name="description" value="{{ isset($object) ? $object->description  : old('description')  }}" class="form-control" placeholder="أدخل الوصف بالعربية">--}}
{{--											@if ($errors->has('description'))--}}
{{--												<span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('description') }}</strong>--}}
{{--		                                    </span>--}}
{{--											@endif--}}
{{--										</div>--}}
{{--									</div>--}}
{{--									<div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">--}}
{{--										<label class="control-label col-lg-2">أدخل الوصف بالانجليزية</label>--}}
{{--										<div class="col-lg-10">--}}
{{--											<input style="direction: ltr" type="text" name="description_en" value="{{ isset($object) ? $object->description_en  : old('description_en')  }}" class="form-control" placeholder="أدخل الوصف بالانجليزية">--}}
{{--											@if ($errors->has('description_en'))--}}
{{--												<span class="help-block">--}}
{{--		                                        <strong>{{ $errors->first('description_en') }}</strong>--}}
{{--		                                    </span>--}}
{{--											@endif--}}
{{--										</div>--}}
{{--									</div>--}}



									<div @if(!isset($object) || $object->type!="photo") style="display: block" @endif id="photo">
									<div  class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
									<label class="control-label col-lg-2">أدخل صورة الشاشة افتتاحية</label>
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
									<div  class="form-group">
										<label class="control-label col-lg-2">الصورة الحالية</label>
										<div class="col-lg-10">
										 <img alt="" width="100"  src="/uploads/{{ $object  -> photo }}">
										</div>
									</div>
								    @endif
									</div>
									
									
									
								</fieldset>
								<div id="submit" @if(!isset($object))  @endif class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} الشاشة افتتاحية <i class="icon-arrow-left13 position-right"></i></button>
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