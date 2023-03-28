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
							<li><a href="/provider-panel/faqs">عرض الاسئلة المكررة</a></li>
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

							<form method="post" class="form-horizontal" action="{{ isset($object)? '/provider-panel/faqs/'.$object->id : '/provider-panel/faqs'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
								   
									<div class="form-group{{ $errors->has('question') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل السؤال</label>
										<div class="col-lg-10">
										<input type="text" name="question" value="{{ isset($object) ? $object->question  : old('question')  }}" class="form-control" placeholder="أدخل السؤال">
										@if ($errors->has('question'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('question') }}</strong>
		                                    </span>
		                                @endif
										</div>
										
									</div>
									<div class="form-group{{ $errors->has('question_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل السؤال بالانجليزية</label>
										<div class="col-lg-10">
											<input style="direction: ltr" type="text" name="question_en" value="{{ isset($object) ? $object->question_en  : old('question_en')  }}" class="form-control" placeholder="أدخل السؤال بالانجليزية">
											@if ($errors->has('question_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('question_en') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									<div class="form-group{{ $errors->has('answer') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل الاجابة</label>
										<div class="col-lg-10">
											<textarea class="summernote" rows="8" cols="40" name="answer"  class="form-control">{{ isset($object) ? $object->answer  : old('answer')  }}</textarea>
											{{--<input type="text" name="answer" value="{{ isset($object) ? $object->answer  : old('answer')  }}" class="form-control" placeholder="أدخل الاجابة">--}}
											@if ($errors->has('answer'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('answer') }}</strong>
		                                    </span>
											@endif
										</div>

									</div>

									<div style="direction: ltr !important;" class="form-group{{ $errors->has('answer_en') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل الاجابة بالنانجليزية</label>
										<div class="col-lg-10">
											<textarea class="summernote" rows="8" cols="40" name="answer_en" class="form-control">{{ isset($object) ? $object->answer_en  : old('answer_en')  }}</textarea>
											{{--<input type="text" name="answer" value="{{ isset($object) ? $object->answer  : old('answer')  }}" class="form-control" placeholder="أدخل الاجابة">--}}
											@if ($errors->has('answer_en'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('answer_en') }}</strong>
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