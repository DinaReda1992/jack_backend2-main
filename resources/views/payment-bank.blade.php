@extends('layouts.layout ')
@section('title')
<title> الدفع بحوالة بنكية </title>
<meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
<meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
@stop
@section('content')
    <section class="page-head">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head-con">
                        <ul>
                            <li>
                                <a href="/">الرئيسية</a>
                            </li>
                            <li>/</li>
                            <li>التحويل البنكي</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bank-payment">
        <div class="container">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-12 col-md-8">
                    <div id="Checkout" class="inline">
                        <paymentbank :order="{{$order}}" :appbanks="{{$app_banks}}" :banks="{{$banks}}" />
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
