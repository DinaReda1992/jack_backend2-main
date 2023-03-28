<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>لوحة التحكم - صفحة تسجيل الدخول للوحة التحكم</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/core.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/components.css" rel="stylesheet" type="text/css">
	<link href="/assets/css/colors.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script type="text/javascript" src="/assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="/assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="/assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->


	<!-- Theme JS files -->
	<script type="text/javascript" src="/assets/js/core/app.js"></script>
	<!-- /theme JS files -->
 <style>
	.page-container{
		min-height: 0px !important;
	}
	html, body {
		height: 100%;
		font-family: 'Droid Arabic Kufi', sans-serif;
	}
 </style>
</head>

<body>
	<!-- Page container -->
	<div class="page-container login-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Content area -->
				<div class="content">

					<!-- Simple login form -->
					<form method="post"  action="/admin-panel/login">
					{!! csrf_field() !!}
						<div class="panel panel-body login-form">
							<div class="text-center">
								{{--<div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div>--}}
								<img style="width: 263px;"  src="/images/{{ \App\Models\Settings::find(1)->value }}">
								<h5 class="content-group">لوحة تحكم {{ \App\Models\Settings::find(2)->value }} <small class="display-block">سجل بيانات الدخول لحسابك في الاسفل</small></h5>
							</div>
							
							@include('admin.alert')
							<div class="form-group has-feedback has-feedback-left{{ $errors->has('email') ? ' has-error' : '' }}">
								<input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="البريد الالكتروني">
								 @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
								<div class="form-control-feedback">
									<i class="icon-user text-muted"></i>
								</div>
							</div>

							<div class="form-group has-feedback has-feedback-left{{ $errors->has('password') ? ' has-error' : '' }}">
								<input type="password" name="password" class="form-control" placeholder="كلمة المرور">
								 @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
								<div class="form-control-feedback">
									<i class="icon-lock2 text-muted"></i>
								</div>
							</div>
							 <div class="form-group">
                                <div class="checkbox">
                                    <label for="checkbox">
                                        <input type="checkbox" id="checkbox" name="remember"> تذكرني
                                    </label>
                                </div>
                            </div>

							<div class="form-group">
								<button type="submit" name="submit" class="btn btn-primary btn-block">تسجيل الدخول <i class="icon-circle-left2 position-right"></i></button>
							</div>

							{{--<div class="text-center">--}}
								{{--<a href="/password/email">نسيت كلمة المرور ؟</a>--}}
							{{--</div>--}}
						</div>
					</form>
					<!-- /simple login form -->


					<!-- Footer -->
					@include('admin.footer')
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>
