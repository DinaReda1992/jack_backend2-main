@foreach($products as $product)
    @php
        if($product->discount){
            $discount_value = $product->price-(($product->price*$product->discount)/100);
        }else{
            $discount_value = $product->price;
        }
    @endphp
    <div class="col-lg-4 col-sm-6 col-xs-12">
        <div class="a-product">
            @if( strtotime($product->created_at) > strtotime('-7 day') )
                <span class="new">جديد</span>
            @endif
            @if($product->discount)
            <div class="discount">خصم <span>{{ $product->discount }}%</span></div>
            @endif
            <a href="/product/{{ $product->id }}/{{ urlencode($product->title) }}">
                <div class="product-photo">
                    <img src="{{ @$product->getPhotos->first() ? '/uploads/'.$product->getPhotos->first()->photo : '/site/images/no-image.png'  }}" alt="">
                </div>
            </a>
            <div class="lower-info">
                <a href="/product/{{ $product->id }}/{{ urlencode($product->title) }}"><h5>{{ $product->title }}</h5></a>
                <p>
                    {{ mb_substr($product->description,0,25,'utf-8')." .." }}
                </p>
                <div class="price"> @if($product->discount) &nbsp;<strike><span>{{ $product->price }} ريال</span></strike> @endif &nbsp;<span>{{ $discount_value }} ريال</span> <img style="width: 13px;"
                                                                     src="/site/images/money.png"
                                                                     alt=""></div>
                <div class="right-stuff">
                                            <span class="rating-text"><input disabled value="{{ \App\Models\Comments::where('product_id',$product->id)->avg('rate') }}" class="rating starrating"
                                                                             data-show-caption="false" data-min="0"
                                                                             data-max="5" data-step=1 data-rtl=1
                                                                             data-glyphicon=0 type="text" data-size="xs"
                                                                             step="1"></span>
                </div>
                <div class="left-stuff">
                    @if((auth()->user() && auth()->user()->user_type_id != 3) || !auth()->user())
                        <a class="loading-shopping-cart" style="display: none" >
                            <img  src='/site/images/loading.gif' width='20' height='20'  />
                        </a>
                        <a  class="product-to-shop" product_id="{{ $product->id }}" onclick="return false" href="#">
                            <img style="width: 18px" src="/site/images/cart.png" alt="">
                        </a>
                    @endif
                    <a class="loading" style="display: none" >
                        <img  src='/site/images/loading.gif' width='20' height='20'  />
                    </a>
                    <a @if(!auth()->user()) data-toggle="modal" data-target="#myModal" @else class="like" product_id="{{ $product->id }}" @endif onclick="return false" href="#">
                        @if(!auth()->user() || \App\Models\ProductLikes::where('user_id',auth()->user()->id)->where('product_id',$product->id)->first() == false)
                            <i class="fa fa-heart-o"></i>
                        @else
                            <i class="fa fa-heart" style="color: red"></i>
                        @endif
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endforeach
@if(count($products) == 0)
    <div class="col-xs-12">
        <h3 align="center">لا يوجد منتجات .</h3>
    </div>
@endif
<a class="mod2" data-toggle="modal" data-target="#myModal2" href="#" onclick="return false;" style="display: none;"></a>
<a class="mod3" data-toggle="modal" data-target="#myModal3" href="#" onclick="return false;" style="display: none;"></a>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        {!! csrf_field() !!}

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">رسالة تنبيهية  </h4>
            </div>
            <div class="modal-body">
                <div class="">
                    يجب ان تكون عضو لدينا لتتمكن من اضافة المنتج الى المفضلة .
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
                <a class="res" href="/login">
                    <button type="button" class="btn btn-primary">تسجيل الدخول</button>
                </a>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">رسالة تنبيهية  </h4>
            </div>
            <div class="modal-body">
                <div class="">
                    عفوا لا يوجد عدد كافي من هذا المنتج في المخزن .
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
                {{--<a class="res" href="/login">--}}
                    {{--<button type="button" class="btn btn-primary">تسجيل الدخول</button>--}}
                {{--</a>--}}
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">رسالة تنبيهية  </h4>
            </div>
            <div class="modal-body">
                <div class="">
                    عفوا لا يمكنك اضافة منتج لك الى سلة المشتريات .
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>

            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
            $('.like').click(function () {
                var tag = $(this);
                tag.html('');
                tag.prev('.loading').show();
                product_id = $(this).attr('product_id');
                $.get('/like/'+product_id,function (data) {
                    tag.prev('.loading').hide();
                      tag.html(data)

                })
            });
            $('.product-to-shop').click(function () {
                var tag = $(this);
                var ddd = $(this).find('img');
                ddd.css('display','none');
                tag.prev('.loading-shopping-cart').show();
                product_id = $(this).attr('product_id');
                $.get('/add-to-shop/'+product_id,function (data) {
                    if(data==2){
                        $('.mod2').click();
                        tag.prev('.loading-shopping-cart').hide();
                        ddd.css('display','block');

                    }else if(data==3){
                        $('.mod3').click();
                        tag.prev('.loading-shopping-cart').hide();
                        ddd.css('display','block');
                    }else{
                        $.get('/change-shopping-cart',function (data) {
                            $('.the-cart').html(data);
                            tag.prev('.loading-shopping-cart').hide();
                            ddd.css('display','block');
                            notify.initialization("تم اضافة المنتج الى السلة بنجاح ","success");
                        })
                    }


                })
            });

        var notify={
            initialization:function(msg,type){
                var notify_count= $("#notify_container .notif").length;
                var notify_type="";
                if(type=='success')notify_type="alert-success";
                else if(type=="failed") notify_type="alert-danger";
                var position_top=52*notify_count;
                var div_id='notify_div_'+notify_count;
                var notify_div='<div class="notif alert '+notify_type+' col-lg-6 col-md-6 col-sm-6" role="alert" style="top:'+position_top+'px; text-align: center;position: fixed;z-index: 999999999999999999999999;" id="'+div_id+'">'
                    +' <div class="pull-right btn-box-tool close_notify" onclick="notify.close_notify('+div_id+')" style="cursor: pointer"><i class="fa fa-times"></i></div>'
                    +'<span>'+msg+'</span></div>';
                $("#MessagePopUpNew").append(notify_div);

                $("#"+div_id).show(300);

                setTimeout(function() {
                    $("#"+div_id).hide(300,function(){
                        $("#"+div_id).remove();
                    });

                }, 2000);
            },
            close_notify:function(div_id){
                $("#"+div_id.id).hide(300,function(){
                    $("#"+div_id.id).remove();
                });
            }
        }
    });
</script>
