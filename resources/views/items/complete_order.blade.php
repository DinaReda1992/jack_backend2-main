<div class="item prCategory{{$product->category_id}}">
<div class="product-card">
    @if($product->is_trend)
        <div class="badge">Hot</div>
    @endif
    <div class="product-tumb">
        <img src="/uploads/{{$product->photo}}" alt="{{$product->title}}">
    </div>
    <div class="product-details">
        <div class="product-top-details">
            <h4>
                <a href="/product/{{$product->id}}">{{$product->title}}</a>
            </h4>
            <span class="product-catagory">السعر  شامل الضريبة</span>
            @if(auth()->user())
                <div style="direction: ltr;display: inline-block" class="product-price">
                    {{--                                        <small> 96.00 SAR </small>--}}
                    {{round($product->price,2)}} SAR
                </div>

                <div class="product-sell">
{{--                    <label>تم بيعه ({{@$product->cart_items_count}}) مرة</label>--}}
                </div>

{{--                <div class="product-rate">--}}
{{--                    <input class="rating rating-loading input-id" data-min="0" data-max="5" data-step="1" value="{{$product->product_rate}}" data-size="xxs" disabled>--}}
{{--                </div>--}}
            @else
                <a style="    border: 1px solid black;" href="/login" class="btn btn-default">تسجيل دخول </a>
            @endif

        </div>
        @if(auth()->user())
        <div class="product-bottom-details" >
            <actions :item="{{$product}}" :single="0" :user="{{auth()->user()}}" />
<!--            <div class="product-links" >
                <a href="#"><i class="las la-share-alt"></i></a>
                <a href="#"><i class="la la-heart"></i></a>
                <a href="#"><i class="la la-shopping-cart"></i></a>
            </div>-->
        </div>
            @endif
    </div>
</div>
</div>
