<div class="box slick-slide" style="display: block;">
    <div class="card border-0 product" data-animate="fadeInUp">
        <div class="position-relative">
            <img src="{{ $product->photo }}" alt="{{ $product->title }}" style="height: 345px;">
            <div class="card-img-overlay d-flex p-3">
                @if ($product->offer_type != '')
                    <div>
                        <span class="badge badge-primary"
                            style="position: absolute; direction: ltr;padding: 6px;margin-top: 19px;margin-right: -14px;">{{ $product->offer_type }}</span>
                    </div>
                @endif
                <div class="my-auto w-100 content-change-vertical">
                    <a href="/product/{{ $product->id }}" data-toggle="tooltip" data-placement="left"
                        title="{{__('messages.product_details')}}"
                        class="add-to-cart ml-auto d-flex align-items-center justify-content-center text-secondary bg-white hover-white bg-hover-secondary w-48px h-48px rounded-circle mb-2">
                        <svg class="icon icon-shopping-bag-open-light fs-24">
                            <use xlink:href="#icon-shopping-bag-open-light"></use>
                        </svg>
                    </a>
                    <add-to-wishlist :item="{{ json_encode($product) }}" :single="0"
                        :user="{{ auth('client')->user() ?: json_encode(['id' => 0]) }}">
                    </add-to-wishlist>
                    <quick-view :item="{{ json_encode($product) }}" :single="0">
                    </quick-view>
                </div>
            </div>
        </div>
        <div class="card-body pt-4 text-center px-0">
            <h2 class="card-title fs-15 font-weight-500 mb-2"><a
                    href="/product/{{ $product->id }}">{{ $product->title }}</a></h2>
            <div style="margin-top:1.5em;direction: ltr;" class="d-flex align-items-center justify-content-center flex-wrap">

                <p class="card-text font-weight-bold fs-16 mb-1 text-secondary">
                    @if ($product->offer_price > 0)
                        <span class="fs-16 font-weight-bold" dir="rtl">{{ $product->offer_price }} {{__('dashboard.sar')}}</span>
                        <span class="fs-15 font-weight-500 text-decoration-through text-body"
                            dir="rtl">{{ $product->price }}</span>
                    @else
                        <span class="fs-16 font-weight-bold" dir="rtl">{{ $product->price }} {{__('dashboard.sar')}}</span>
                    @endif
                </p>
                <add-to-cart-action :item="{{ json_encode($product) }}" :single="0"
                    :user="{{ auth('client')->user() ?: json_encode(['id' => 0]) }}" />

            </div>
        </div>
    </div>
</div>
