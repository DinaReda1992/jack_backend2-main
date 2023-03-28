<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <title>رسالة تواصل</title>
    <style type="text/css">
        @import url(//fonts.googleapis.com/earlyaccess/droidarabickufi.css);
    </style>
</head>

<body>
<div style="direction: {{ trans('messages.direction_email') }};font-family: 'Droid Arabic Kufi', sans-serif; ">

    <h3>{{$subject }}</h3>
    <p>
{{$contact_message}}
    </p>
</div>
</body>
</html>