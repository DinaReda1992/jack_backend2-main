@extends('layouts.layout')
@section('title')
    <title> باقي الطلب </title>
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
                            <li class="d-inline-block">باقي الطلب</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="cart-page">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-12">
                    <div class="basket">
                        <div class="basket-labels">
                            <ul>
                                <li class="item item-heading">المنتج</li>
                                <li class="price">السعر</li>
                                <li class="quantity">الكمية</li>
                                <li class="subtotal">مجمل السعر</li>
                            </ul>
                        </div>
                        @php
                           $i=0; 
                        @endphp
                        @foreach (json_decode($shop) as $shops)
                            @foreach ($shops->cart_items as $item)
 @php
 $i++;    
 @endphp 
                                <div class="basket-product">
                                    <div class="item">
                                        <div class="product-image">
                                            <img src="{{ $item->photo }}" width="50" alt="{{ $item->title }}"
                                                class="product-frame">
                                        </div>
                                        <div class="product-details">
                                            <h1><strong><span class="item-quantity">{{ $item->quantity }}</span>
                                                    {{ $item->title }}</strong></h1>
                                        </div>
                                    </div>
                                    <div class="price"> {{ $item->price }}</div>
                                    <div class="quantity">
                                        {{ $item->quantity }}
                                    </div>
                                    <div class="subtotal">{{ $item->price * $item->quantity }}</div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="summary">
                        <div class="summary-total-items summary-total">
                            <span class="">مجموع المنتجات :</span>
                            <span class="">{{$i}}</span>
                        </div>
                        <div class="summary-subtotal   my-0">
                            <div class="subtotal-title">سعر المنتجات</div>
                            <div class="subtotal-value final-value">{{round( $total,2) }}</div>
                        </div>
                        <div class="summary-subtotal  my-0">
                            <div class="subtotal-title">سعر الشحن</div>
                            <div class="subtotal-value final-value">0</div>
                        </div>
                        @if ($cobon != 0)
                            <div class="summary-subtotal   my-0">
                                <div class="subtotal-title">خصم الكوبون</div>
                                <div class=" final-value"> {{ round($cobon,2) }} SAR</div>
                            </div>
                        @endif
                        <div class="summary-subtotal  my-0">
                            <div class="subtotal-title">نسبة الضرائب</div>
                            <div class=" final-value">
                                {{ round($total_taxes,2) . ' SAR' }}
                            </div>
                        </div>
                        <div class="summary-total  my-0">
                            <div class="total-title">السعر الكلي</div>
                            <div class="total-value final-value" id="basket-total">
                                {{ round($final_price,2) . ' SAR' }}
                            </div>
                        </div>
                        <div class="">
                            <a class="btn btn-primary" href="/add-new-order/{{ $order_id }}">
                                أعادة شحن
                            </a>
                            <a class="btn btn-success" href="/return-balance/{{ $order_id }}">
                                أيداع المبلغ بالحفظة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
