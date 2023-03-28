@extends('providers.layout')
@section('js_files')
	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/plugins/forms/styling/uniform.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/uploaders/fileinput.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<script type="text/javascript" src="/assets/js/pages/form_inputs.js"></script>
	<!-- /theme JS files -->
	<script type="text/javascript" src="/assets/js/pages/uploader_bootstrap.js"></script>
	<script type="text/javascript" src="/assets/js/pages/gallery.js"></script>
	<style type="text/css">
		.chat-list, .chat-stacked{
			max-height: initial !important;
			overflow: initial !important;;
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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> - {{ isset($object)? 'تعديل':'إضافة' }} رد </h4>
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
							<li><a href="/provider-panel/messages">عرض جميع الرسائل</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} رد </li>
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

@include('providers.message')

					<!-- Form horizontal -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} رد  </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>
						
						
						<div class="panel-body">

							<form enctype="multipart/form-data"  method="post" class="form-horizontal" action="/provider-panel/messages">
								{!! csrf_field() !!}
								<fieldset class="content-group">

									<div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
										<label class="control-label col-lg-2">أدخل الرد</label>
										<div class="col-lg-10">
											<textarea class="form-control" name="message">{{  old('message')  }}</textarea>
											<input type="hidden" name="reciever_id" value="{{ $other_user->id }}">
											<input type="hidden" name="hall_id" value="{{ $hall_id }}">
											@if ($errors->has('message'))
												<span class="help-block">
													<strong>{{ $errors->first('message') }}</strong>
												</span>
											@endif
										</div>
										
									</div>
								</fieldset>
								<div class="text-right">
									<button type="submit" class="btn btn-primary">{{ isset($object)? 'تعديل':'إضافة' }} رد <i class="icon-arrow-left13 position-right"></i></button>
								</div>
							</form>
						</div>
					</div>
					<!-- /form horizontal -->

	<!-- Form horizontal -->
	<div class="panel panel-flat">
		<div class="panel-heading">
			<h5 class="panel-title">عرض الرسائل مع {{ $other_user->username }}  </h5>
			<div class="heading-elements">
				<ul class="icons-list">
					<li><a data-action="collapse"></a></li>
					<li><a data-action="reload"></a></li>
					<li><a data-action="close"></a></li>
				</ul>
			</div>
		</div>


		<div class="panel-body">
			<ul id="chat" class="media-list chat-list content-group">
				{{--									<li class="media date-step">--}}
				{{--										<span>Today</span>--}}
				{{--									</li>--}}

				@php
					$last_id=0;
                        $i=1;
				@endphp
				@foreach($objects as $object)
					@php
						if($i==1){
                        $last_id = $object->id;
                        }
					$message = $object;
					@endphp
					@if($object->sender_id != $owner_user)

						<li class="media reversed">
							<div class="media-body">
								<div class="media-content">
{{--									@if($message->type==1)--}}
{{--										<div class="thumbnail">--}}
{{--											<div class="thumb">--}}
{{--												<img style="width: 100px;height: 100px;" src="/uploads/{{$message -> photo}}" alt="">--}}
{{--												<div class="caption-overflow">--}}
{{--														<span>--}}
{{--															<a href="/uploads/{{$message -> photo}}" data-popup="lightbox" rel="gallery" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-plus3"></i></a>--}}
{{--														</span>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									@elseif($message->type==2)--}}
{{--										<audio controls>--}}
{{--											<source src="/uploads/{{ $message-> audio }}" type="audio/ogg">--}}
{{--											Your browser does not support the audio element.--}}
{{--										</audio>--}}
{{--									@else--}}
										{{ $message->message }}
{{--									@endif--}}
								</div>
								<span class="media-annotation display-block mt-10">{{ $message->created_at->format('Y/m/d h:i A') }} <br> ( <a href="/admin-panel/all-users/{{ $message->sender_id }}/edit">{{ $message->getSenderUser->username }}</a> )</span>
							</div>
							<div class="media-right">
								<a href="/admin-panel/all-users/{{ $message->sender_id }}/edit">
									<img src="{{ @$message->getSenderUser->photo ? '/uploads/'.@$message->getSenderUser->photo : '/assets/images/placeholder.jpg' }}" class="img-circle" alt="">
								</a>
							</div>
						</li>
						@php
							$object->status=1;
                            $object->save();
						@endphp
					@else
						<li class="media">
							<div class="media-left">
								<a href="#">
									<img src="/images/logo.png" class="img-circle" alt="">
								</a>

							</div>
							<div class="media-body">
								<div class="media-content">
{{--									@if($message->type==1)--}}
{{--										<div class="thumbnail">--}}
{{--											<div class="thumb">--}}
{{--												<img style="width: 100px;height: 100px;" src="/uploads/{{$message -> photo}}" alt="">--}}
{{--												<div class="caption-overflow">--}}
{{--														<span>--}}
{{--															<a href="/uploads/{{$message -> photo}}" data-popup="lightbox" rel="gallery" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-plus3"></i></a>--}}
{{--														</span>--}}
{{--												</div>--}}
{{--											</div>--}}
{{--										</div>--}}
{{--									@elseif($message->type==2)--}}
{{--										<audio controls>--}}
{{--											<source src="/uploads/{{ $message-> audio }}" type="audio/ogg">--}}
{{--											Your browser does not support the audio element.--}}
{{--										</audio>--}}
{{--									@else--}}
										{{ $message->message }}
{{--									@endif--}}
								</div>
								<span class="media-annotation display-block mt-10">{{ $object->created_at->format('Y/m/d h:i A') }} <br> You </span>

							</div>
						</li>

					@endif
					@php
						$i++;
					@endphp
				@endforeach


			</ul>



		</div>

	</div>
	<script type="text/javascript">
        $(document).ready(function () {

            var last1= "{{ $last_id  }}";

            // setInterval(ajaxCall, 2000); //300000 MS == 5 minutes
            //
            // // function ajaxCall() {
            // //     $.get('/get-last-message-with-user/'+last1,function (data) {
				// // 	$('#chat').html(data);
            // //     });
            // // }
        });
	</script>
	<!-- /form horizontal -->

	<!-- Footer -->
					@include('providers.footer')
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
@stop	