@extends('website.layout')
@section('title')
    <title> {{ __('dashboard.Thank you for shopping at Jak') }}</title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
@endsection
@section('content')
    <main id="content">
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body"
                                href="/">{{ __('dashboard.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">
                            {{ __('dashboard.Thank you for shopping at Jak') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>
        <section class="new_products">
            <div class="container text-center">
                <h2 style="margin-top:0.5em"> {{ __('dashboard.Your Order has been created successfully') }}</h2>
                <img class="img-responsive" style="width: 25%;" src="/images/1200px-Antu_task-complete.svg.png" alt="avatar" >
                <div class="thank-you-details">
                    <h4>{{ __('dashboard.Thank you for shopping at Jak') }}</h4>
                    <h5>{{ __('trans.OrderNo') }} : #{{ $order->id }}</h5>
                    <h5>{{ __('trans.order_total') }} : {{ $order->final_price }} {{ trans('trans.SAR') }}</h5>
                </div>
                <a style="margin:2em 0" class="btn btn-success bg-hover-primary border-hover-primary fadeInUp animated"
                    href="/i/{{ $order->short_code }}">{{ __('dashboard.view_invoice') }}</a>
                <a style="margin:2em 0" class="btn btn-secondary bg-hover-primary border-hover-primary fadeInUp animated"
                    href="/">{{ __('dashboard.Continue shopping') }}</a>
            </div>
        </section>
    </main>
@endsection
