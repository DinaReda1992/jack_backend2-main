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
							<h4><i class="icon-arrow-right6 position-left"></i> <span class="text-semibold">لوحة التحكم </span> -  عرض الرسائل على الطلب رقم - {{ $order->id }}</h4>
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
							<li><a href="/admin-panel/users">عرض الاعضاء</a></li>
							<li class="active">{{ isset($object)? 'تعديل':'إضافة' }} عضو</li>
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
							<h5 class="panel-title">{{ isset($object)? 'تعديل':'إضافة' }} عضو </h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="reload"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>


						<div class="panel panel-flat timeline-content">
							<div class="panel-heading">
								<h6 class="panel-title">الرسائل بين {{ $order->getUser->username }} و {{ $order->getRepresentative->username }} </h6>
								<a class="heading-elements-toggle"><i class="icon-menu"></i></a></div>

							<div class="panel-body">
								<ul class="media-list chat-list content-group">
{{--									<li class="media date-step">--}}
{{--										<span>Today</span>--}}
{{--									</li>--}}
@foreach($messages as $message)
	@if($message->sender_id == $order->user_id)
									<li class="media reversed">
										<div class="media-body">
											<div class="media-content">{{ $message->message }}</div>
											<span class="media-annotation display-block mt-10">{{ $message->created_at }} ( <a href="/admin-panel/all-users/{{ $message->sender_id }}/edit">{{ $message->getSenderUser->username }}</a> )</span>
										</div>

										<div class="media-right">
											<a href="/admin-panel/all-users/{{ $message->sender_id }}/edit">
												<img src="{{ @$message->getSenderUser->photo ? '/uploads/'.@$message->getSenderUser->photo : '/assets/images/placeholder.jpg' }}" class="img-circle" alt="">
											</a>
										</div>
									</li>
	@else
											<li class="media">
												<div class="media-left">
													<a href="/admin-panel/all-users/{{ $message->reciever_id }}/edit">
														<img src="{{ @$message->getRecieverUser->photo ? '/uploads/'.@$message->getRecieverUser->photo : '/assets/images/placeholder.jpg' }}" class="img-circle" alt="">
													</a>
												</div>

												<div class="media-body">
													<div class="media-content">{{ $message->message }}</div>
													<span class="media-annotation display-block mt-10">{{ $message->created_at }} ( <a href="/admin-panel/all-users/{{ $message->reciever_id }}/edit">{{ $message->getRecieverUser->username }}</a> )</span>
												</div>
											</li>

	@endif
@endforeach


								</ul>



							</div>
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
