<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <title>Activate account</title>
    <style type="text/css">
        @import url(//fonts.googleapis.com/earlyaccess/droidarabickufi.css);
    </style>
</head>

<body>
<div style="direction: {{ trans('messages.direction_email') }};font-family: 'Droid Arabic Kufi', sans-serif; ">

    <h3>{{ __('messages.activate_account') }}</h3>
    @if(isset($is_site))
        <a href="{{ url('/activate/'.$activation_code) }}"style="border-radius: 5px; color: #fff; background-color: #2d5be3;
    font-weight: bold;
    padding: 10px 18px;
    font-size: 20px;
    text-decoration: none;"> إضغط هنا لتفعيل حسابك </a>
@else
        <p>
        {{ __('messages.activate_account_mail') }} {{ $activation_code }}
    </p>
@endif
</div>
</body>
</html>