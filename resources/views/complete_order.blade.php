@extends('layouts.layout ')
@section('title')
    <title> استكمال الطلب </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
@stop
@section('content')
    <section class="page-head">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-head-con">
                        <ul class="d-inline-block">
                            <li class="d-inline-block">
                                <a href="/">الرئيسية</a>
                            </li>
                            <li class="d-inline-block">/</li>
                            <li class="d-inline-block">سلة الشراء</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="cart-page">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <main>
                        <completeorder :address="{{ $address }}" :items="{{ $cart }}"
                            :taxs="{{ $taxs }}" :shipmentprice="{{ $shipment_price }}" :user="{{ auth()->user() }}"
                            :messages="{{ json_encode($messages) }}"
                            :online_payment="{{ intval(\App\Models\Settings::find(40)->value) }}"
                            :tap_payment="{{ intval(\App\Models\Settings::find(49)->value) }}"
                            :pay_later="{{ intval(\App\Models\Settings::find(44)->value) }}"
                            :show_tmara="{{ intval($show_tmara) }}" />
                    </main>
                </div>
            </div>
        </div>
    </section>
@endsection
