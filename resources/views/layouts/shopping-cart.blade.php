<a href="#">
    <i class="fa fa-shopping-cart"></i>
    <div class="badge"><span>{{ session('items') ?  count(session('items')) : ( \App\Models\InvoiceDetails::where('user_id',auth()->id())->where('invoice_id',0)->groupBy('product_id')->count() ? count(\App\Models\InvoiceDetails::where('user_id',auth()->id())->where('invoice_id',0)->groupBy('product_id')->get()) : 0) }}</span></div>
</a>
<div class="dropdown-cart">

    @if(session('items') && count(session('items')) > 0)
        @php $sum=0 @endphp
        @foreach(session('items') as $key => $item)
            @php
                if($item[0]['product']->discount){
                    $discount_value = $item[0]['product']->price-(($item[0]['product']->price*$item[0]['product']->discount)/100);
                }else{
                    $discount_value = $item[0]['product']->price;
                }
            $discount_value = $discount_value * $item[0]['quantity'];
            $sum+=$discount_value;
            @endphp
            <div class="an-item" parent_id="{{ $key }}">
                <div class="row">
                    <div class="col-xs-9" style="text-align: right">
                        <h5>{{ $item[0]['product']->title }}</h5>
                        <p> {{ mb_substr($item[0]['product']->description,0,30)." .." }}</p>
                        <h6 style="display: inline-block;margin-left: 20px"><strong>الكمية
                                :</strong> {{ $item[0]['quantity'] }}</h6>
                        <h6 style="display: inline-block;margin-left: 20px">@if($item[0]['product']->discount)
                                &nbsp;<strike><span>{{ $item[0]['product']->price * $item[0]['quantity']  }} ريال</span></strike> @endif
                            &nbsp;<span>{{ $discount_value }} ريال</span></h6>
                        <h6 style="display: inline-block;">
                            <a href="/shopping-cart"><i class="fa fa-edit"></i></a>
                            <a class="loading-shopping-cart" style="display: none" >
                                <img  src='/site/images/loading.gif' width='20' height='20'  />
                            </a>
                            <a class="delete-product" product_id="{{ $key }}" onclick="return false;" href="#"><i class="fa fa-trash"></i></a>
                        </h6>
                    </div>
                    <div class="col-xs-3" style="padding-right: 0">
                        <img style="width: 100%;height: 100%"
                             src="{{ $item[0]['product']->getPhotos->first() ? '/uploads/'.$item[0]['product']->getPhotos->first()->photo : '/site/images/no-image.png' }}"
                             alt="">
                    </div>
                </div>
            </div>
        @endforeach
        <div class="total">
            <h5>الاجمالي : </h5> &nbsp;
            <span>{{ $sum }} ريال</span>
        </div>

        @elseif(auth()->user() && \App\Models\InvoiceDetails::where('user_id',auth()->id())->where('invoice_id',0)->first())
        @php $sum=0 @endphp
        @foreach(\App\Models\InvoiceDetails::where('user_id',auth()->id())->where('invoice_id',0)->groupBy('product_id')->get() as $invoice)

            @php
                if($invoice->getProduct->discount){
                    $discount_value = $invoice->getProduct->price-(($invoice->getProduct->price*$invoice->getProduct->discount)/100);
                }else{
                    $discount_value = $invoice->getProduct->price;
                }
                $discount_value = $discount_value * $invoice->quantity;
                $sum+=$discount_value;
            @endphp
            <div class="an-item" parent_id="{{ $invoice->product_id }}">
                <div class="row">
                    <div class="col-xs-9" style="text-align: right">
                        <h5>{{ $invoice->getProduct->title }}</h5>
                        <p> {{ mb_substr($invoice->getProduct->description,0,30)." .." }}</p>
                        <h6 style="display: inline-block;margin-left: 20px"><strong>الكمية
                                :</strong> {{ $invoice->quantity }}</h6>
                        <h6 style="display: inline-block;margin-left: 20px">@if($invoice->getProduct->discount)
                             &nbsp;<strike><span>{{ $invoice->getProduct->price * $invoice->quantity  }} ريال</span></strike> @endif
                            &nbsp;<span>{{ $discount_value }} ريال</span></h6>
                        <h6 style="display: inline-block;">
                            <a href="/shopping-cart"><i class="fa fa-edit"></i></a>
                            <a class="loading-shopping-cart" style="display: none" >
                                <img  src='/site/images/loading.gif' width='20' height='20'  />
                            </a>
                            <a class="delete-product" product_id="{{ $invoice->product_id }}" onclick="return false;" href="#"><i class="fa fa-trash"></i></a>
                        </h6>
                    </div>
                    <div class="col-xs-3" style="padding-right: 0">
                        <img style="width: 100%;height: 100%"
                             src="{{ $invoice->getProduct->getPhotos->first() ? '/uploads/'.$invoice->getProduct->getPhotos->first()->photo : '/site/images/no-image.png' }}"
                             alt="">
                    </div>
                </div>
            </div>
        @endforeach
        <div class="total">
            <h5>الاجمالي : </h5> &nbsp;
            <span>{{ $sum }} ريال</span>
        </div>



    @else
        <div class="total">
            <h5>لا يوجد منتجات في سلة المشتروات </h5> &nbsp;
            {{--<span>1999 ريال</span>--}}
        </div>
    @endif
    @if((session('items') && count(session('items')) > 0) || count(\App\Models\InvoiceDetails::where('user_id',auth()->id())->where('invoice_id',0)->groupBy('product_id')->get()))
        <div class="row">
            <div class="col-xs-6" style="padding: 0">
                <a href="/shopping-cart" class="btn btn-default btn-1">مشاهدة السله</a>
            </div>
            <div class="col-xs-6" style="padding: 0">
                <a href="/shopping-cart" class="btn btn-default btn-2">متابعة الشراء</a>
            </div>
        </div>
    @endif
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.delete-product').click(function () {
            var tag = $(this);
            var ddd = $(this);
            ddd.css('display','none');
            tag.prev('.loading-shopping-cart').show();
            product_id = $(this).attr('product_id');
            $.get('/delete-product-from-cart/'+product_id,function (data) {
                $.get('/change-shopping-cart',function (data) {
                    $('.the-cart').html(data);
                    tag.prev('.loading-shopping-cart').hide();
                    ddd.css('display','block');
                })
            })
        });
    });
</script>
