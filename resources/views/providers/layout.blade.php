<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="_token" content="{{ csrf_token() }}">
	<title>لوحة التحكم - {{\App\Models\Settings::find(2)->value}}</title>
	<link rel="shortcut icon" href="/site/assets/img/favicon.png" type="image/x-icon">
	<meta name="csrf-token" content="{{ csrf_token() }}"/>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/AdminLTE.min.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/AdminLTE-RTL.min.css" rel="stylesheet" type="text/css">

	<link href="/assets/css/core.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/components.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/colors.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->
	<!-- Core JS files -->
	<script type="text/javascript" src="/assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/loaders/blockui.min.js"></script>
	<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
	<!-- /core JS files -->
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">


</head>
<body>
<!-- Main navbar -->
<div class="navbar navbar-inverse" style="background: {{\App\Models\Settings::find(33)->value}}">
	<div class="navbar-header">
		<a class="navbar-brand" target="_blank" href="#"><span>لوحة تحكم {{\App\Models\Settings::find(2)->value}}</span></a>

		<ul class="nav navbar-nav visible-xs-block">
			<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
		</ul>
	</div>

	<div class="navbar-collapse collapse" id="navbar-mobile">
		<ul class="nav navbar-nav">
			<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>

		</ul>

		<p class="navbar-text"><span class="label bg-success-400">أون لاين</span></p>

		<ul class="nav navbar-nav navbar-right">

			@php
				$contacts = \App\Models\Contacts::where('status',0)->count();
				$tickets = \App\Models\Messages::where('status',0)->where('reciever_id',1)->count();
				$cont_all = $contacts  +$tickets;
			@endphp
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-bell2"></i>
					<span class="visible-xs-inline-block position-right">الاشعارات</span>
					@if($cont_all>0)
						<span class="badge bg-warning-400">{{ $cont_all }}</span>
					@endif
				</a>

				<div class="dropdown-menu dropdown-content width-350">
					<div class="dropdown-content-heading">
						الاشعارات
					</div>
					<ul class="media-list dropdown-content-body">

						<li class="media">
							<div class="media-left">
								<img src="/assets/images/placeholder.jpg" class="img-circle img-sm" alt="">
								<span class="badge bg-danger-400 media-badge">{{ $tickets }}</span>
							</div>

							<div class="media-body">
								<a href="/provider-panel/display/new_messages" class="media-heading">
									<span class="text-semibold"> تذاكر  جديدة </span>
								</a>

								<span class="text-muted">لديك {{ $tickets }} رسالة جديدة </span>
							</div>
						</li>
						<li class="media">
							<div class="media-left">
								<img src="/assets/images/placeholder.jpg" class="img-circle img-sm" alt="">
								<span class="badge bg-danger-400 media-badge">{{ $contacts }}</span>
							</div>

							<div class="media-body">
								<a href="/provider-panel/display/contacts_new" class="media-heading">
									<span class="text-semibold"> رسائل تواصل جديدة </span>
								</a>

								<span class="text-muted">لديك {{ $contacts }} رسالة جديدة </span>
							</div>
						</li>

						{{--						<li class="media">--}}
						{{--							<div class="media-left">--}}
						{{--								<img src="/assets/images/placeholder.jpg" class="img-circle img-sm" alt="">--}}
						{{--								<span class="badge bg-danger-400 media-badge">{{ $suggestions }}</span>--}}
						{{--							</div>--}}

						{{--							<div class="media-body">--}}
						{{--								<a href="/provider-panel/new_suggestions" class="media-heading">--}}
						{{--									<span class="text-semibold"> اقتراحات جديدة </span>--}}
						{{--								</a>--}}

						{{--								<span class="text-muted">لديك {{ $suggestions }} اقتراح جديد </span>--}}
						{{--							</div>--}}
						{{--						</li>--}}


						{{--						<li class="media">--}}
						{{--							<div class="media-left">--}}
						{{--								<img src="/assets/images/placeholder.jpg" class="img-circle img-sm" alt="">--}}
						{{--								<span class="badge bg-danger-400 media-badge">{{ $providers }}</span>--}}
						{{--							</div>--}}

						{{--							<div class="media-body">--}}
						{{--								<a href="/provider-panel/new_hotel_requests" class="media-heading">--}}
						{{--									<span class="text-semibold"> طلبات الانضمام كمتجر </span>--}}
						{{--								</a>--}}

						{{--								<span class="text-muted">لديك {{ $providers }} طلب انضمام كمتجر  </span>--}}
						{{--							</div>--}}
						{{--						</li>--}}




					</ul>





					<div class="dropdown-content-footer">
						<a href="#" data-popup="tooltip" title="All messages"><i class="icon-menu display-block"></i></a>
					</div>
				</div>
			</li>

			<li class="dropdown dropdown-user">
				<a class="dropdown-toggle" data-toggle="dropdown">
					<img src="/images/logoicon.png" alt="">
					<span>{{ Auth::user()->username }}</span>
					<i class="caret"></i>
				</a>

				<ul class="dropdown-menu dropdown-menu-right">
					<li><a href="/provider-panel/edit-profile"><i class="icon-user-plus"></i> الملف الشخصي</a></li>
					<li><a href="/provider-panel/logout"><i class="icon-switch2"></i> تسجيل الخروج</a></li>
				</ul>
			</li>
		</ul>
	</div>
</div>
<!-- /main navbar -->

<!-- Page container -->
<div class="page-container" id="app">

	<!-- Page content -->
	<div class="page-content">

		<!-- Main sidebar -->
		<div class="sidebar sidebar-main" style="background: {{\App\Models\Settings::find(33)->value}}">
			<div class="sidebar-content">

				<!-- User menu -->
				<div class="sidebar-user">
					<div class="category-content">
						<div class="media">
							<a href="/provider-panel/edit-profile" class="media-left"><img src="/images/logoicon.png" class="img-circle img-sm" alt=""></a>
							<div class="media-body">
								<span class="media-heading text-semibold">{{ Auth::user()->username }}</span>
								<div class="text-size-mini text-muted">
									<i class="icon-pin text-size-small"></i>  السعودية
								</div>
							</div>

							<div class="media-right media-middle">
								<ul class="icons-list">
									<li>
										<a href="/provider-panel/edit-profile"><i class="icon-cog3"></i></a>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<!-- Main navigation -->
				<div class="sidebar-category sidebar-category-visible">
					<div class="category-content no-padding">
						<ul class="navigation navigation-main navigation-accordion">

							<!-- Main -->
							<li class="@if(Request::is('/provider-panel')) active @endif"><a href="/provider-panel"><i class="icon-home4"></i> <span>الرئيسية</span></a></li>
							@if(Auth::User()->user_type_id==1)
								<?php $privileges = \App\Models\Privileges::where("parent_id",0)->where('hidden',0)->where("is_provider",1)->orderBy('orders','ASC')->get(); ?>
							@elseif(Auth::User()->user_type_id==2&&!empty(Auth::user()->privilege_id))
								<?php
								$pr=\App\Models\PrivilegesGroupsDetails::where('privilege_group_id',Auth::user()->privilege_id)->pluck('privilege_id')->toArray();
								$privileges =\App\Models\Privileges::whereIn("id",$pr)->where("parent_id",0)->where("is_provider",1)->orderBy('orders','ASC')->get();

								?>
							@else
								<?php
								$privileges =\App\Models\Privileges::where("parent_id",0)->where('hidden',0)->where("is_provider",1)->get();
								?>
							@endif
							@foreach($privileges as $privilege)
								@php $active="" @endphp
								@if($privilege->subProgrames->where('hidden',0)->count())
									@foreach($privilege->subProgrames->where('hidden',0) as $program)
										@if(Request::is(ltrim($program->url,'/')))
											@php  $active="active" @endphp
										@endif
									@endforeach
									<li class="{{ $active }}">
										<a href="#"><i class="{{$privilege->icon}}"></i> <span>{{$privilege->privilge}}</span></a>
										<ul>
											@foreach($privilege->subProgrames->where('hidden',0) as $program)
												<li><a href="{{$program->url}}"> <span>{{$program->privilge}}</span></a></li>
											@endforeach
										</ul>
									</li>
								@else
									@if(isset($privilege->url) && Request::is(ltrim($privilege->url,'/')))
										@php  $active="active" @endphp
									@endif

									<li class="{{ $active }}"><a href="{{$privilege->url}}"><i class="{{$privilege->icon}}"></i> <span>{{$privilege->privilge}}</span></a></li>
								@endif
							@endforeach


						</ul>
					</div>
				</div>
				<!-- /main navigation -->




			</div>
		</div>
		<!-- /main sidebar -->
		<!-- Main content -->
	@yield('content')
	<!-- /main content -->

	</div>
	<!-- /page content -->

</div>
<!-- /page container -->
<div id="notify_container"></div>
@yield('js_files')

</body>
</html>
