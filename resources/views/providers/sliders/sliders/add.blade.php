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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} سلايدر</h4>
						</div>
 <div class="heading-elements">
                            {{--<div class="heading-btn-group">--}}
                                {{--<a href="/provider-panel/slider"><button type="button" class="btn btn-primary" name="button"> عرض الشرائح</button></a>--}}
                            {{--</div>--}}
                        </div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="/provider-panel/index"><i class="icon-home2 position-left"></i> الرئيسية</a></li>
							<li><a href="/provider-panel/main_slider">عرض الشرائح</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} سلايدر</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} سلايدر </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						

						<div class="panel-body">

							<form method="post"  enctype="multipart/form-data" class="form-horizontal" action="{{ isset($object)? '/provider-panel/slider/'.$object->id : '/provider-panel/slider'  }}">
								{!! csrf_field() !!}
									@if(isset($object))
                    	<input type="hidden" name="_method" value="PATCH" />
                    	@endif
								<fieldset class="content-group">
<!-- 									<legend class="text-bold">Basic inputs</legend> -->
                                    <div class="form-group{{ $errors->has('slide_title') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">عنوان السلايدر</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="slide_title" value="{{ isset($object) ? $object->slide_title  : old('slide_title')  }}" class="form-control" placeholder="ادخل عنوان السلايدر">
                                            @if ($errors->has('slide_title'))
                                                <span class="help-block">
		                                        <strong>{{ $errors->first('slide_title') }}</strong>
		                                    </span>
                                            @endif
                                        </div>
                                    </div>


									<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">ادخل عنوان السلايدر " بين الأقواس "</label>
										<div class="col-lg-10">
											<input type="text" name="title" value="{{ isset($object) ? $object->title  : old('title')  }}" class="form-control" placeholder=' ادخل عنوان السلايدر " بين الأقواس " ' >
											@if ($errors->has('title'))
												<span class="help-block">
		                                        <strong>{{ $errors->first('title') }}</strong>
		                                    </span>
											@endif
										</div>
									</div>

                                    <div class="form-group{{ $errors->has('text') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">ادخل نص السلايدر</label>
										<div class="col-lg-10">
										<textarea name="text"class="form-control" placeholder="نص السلايدر">{{isset($object)?$object->text: old('text')}}</textarea>
										@if ($errors->has('text'))
		                                    <span class="help-block">
		                                        <strong>{{ $errors->first('text') }}</strong>
		                                    </span>
		                                @endif
										</div>
									</div>
									{{--<div class="form-group{{ $errors->has('text2') ? ' has-error' : '' }}">--}}
										{{--<label class="control-label col-lg-2">ادخل وصف اضافى</label>--}}
										{{--<div class="col-lg-10">--}}
											{{--<textarea name="text2"class="form-control" placeholder="نص اضافى">{{isset($object)?$object->text2: old('text2')}}</textarea>--}}
											{{--@if ($errors->has('text2'))--}}
												{{--<span class="help-block">--}}
		                                        {{--<strong>{{ $errors->first('text2') }}</strong>--}}
		                                    {{--</span>--}}
											{{--@endif--}}
										{{--</div>--}}

									{{--</div>--}}
									<div class="form-group{{ $errors->has('main_slider') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">إختر السلايدر</label>
										<div class="col-lg-10">
											<select name="main_slider" id="main_slider" class="form-control">
												<option value="">اختر السلايدر</option>
												@foreach(\App\Models\Main_slider::all() as $main)
													<option value="{{ $main->id }}"{{isset($object)?($object->main_id==$main->id?"selected":""):(old("main_slider")==$main->id?"selected":"")}} >{{ $main->name }}</option>
												@endforeach
											</select>
											@if($errors->has('main_slider'))
												<span class="help-block">
												 <strong>{{ $errors->first('main_slider') }}</strong>
										 </span>
											@endif
										</div>
									</div>

                                    <div class="form-group {{ $errors->has('photo') ? ' has-error' : '' }}">
                                        <label class="control-label col-lg-2">صورة السلايدر *</label>
                                        <div class="col-lg-5">
                                            <input type="file" name="photo" value="" class="form-control" >

																					<span class="help-block">
																							<strong>{{$errors->has("photo")?$errors->has("photo"):"قم برفع صورة السلايدر *"}}</strong>
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
                                    <fieldset class="content-group">
                                        <!-- 									<legend class="text-bold">Basic inputs</legend> -->

                                        {{--<div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">--}}
                                            {{--<label class="control-label col-lg-2">رابط السلايدر</label>--}}
                                            {{--<div class="col-lg-10">--}}
                                                {{--<input type="text" name="url" value="{{ isset($object) ? $object->url  : old('url')  }}" class="form-control" placeholder="ادخل رابط السلايدر">--}}
                                                {{--@if ($errors->has('name'))--}}
                                                    {{--<span class="help-block">--}}
		                                        {{--<strong>{{ $errors->first('url') }}</strong>--}}
		                                    {{--</span>--}}
                                                {{--@endif--}}
                                            {{--</div>--}}
                                        {{--</div>--}}

                                    </fieldset>

                                </fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} سلايدر <i class="icon-arrow-left13 position-right"></i></button>
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