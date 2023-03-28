@extends('website.layout')

@section('title')
    <title> {{ __('trans.Payment Method') }}</title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css"
        integrity="sha512-vebUliqxrVkBy3gucMhClmyQP9On/HAWQdKDXRaAlb/FKuTbxkjPKUyqVOxAcGwFDka79eTF+YXwfke1h3/wfg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
@section('content')
    <main id="content">
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body" href="/">{{__('dashboard.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">
                            {{ __('trans.Payment Method') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>
        <div>
            <payment_methods  :appbanks="{{$app_banks}}" :banks="{{$banks}}" :balance="{{ $balance }}" :order="{{ $order }}"
                :hand_delivery_cost="{{ $handDeliveryCost }}" :payment_balance="{{App\Models\PaymentSettings::find(7)->value}}" :payment_hand_delivery="{{App\Models\PaymentSettings::find(8)->value}}"></payment_methods>
        </div>
    </main>
@endsection
