@extends('layouts.layout ')
@section('title')
    <title> الدفع طلب رقم {{$order->id}} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop
@section('content')

    <!-- ================= register ================= -->
    <div class="container">
        <payment :order="{{$order}}" />
    </div>
    <!-- ================= end login ================= -->



@stop