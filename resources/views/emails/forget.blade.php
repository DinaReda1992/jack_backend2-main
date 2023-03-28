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

    <h3>{{ __('messages.reset_password') }}</h3>
    <p>
        {{ __('messages.activate_account_mail1') }} {{ $activation_code }}
    </p>

</div>
</body>
</html>