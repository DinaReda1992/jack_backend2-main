@extends('layouts.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(5)->value }} - منتجاتي </title>
    <meta name="description" content="{{ \App\Models\Settings::find(6)->value }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(7)->value }}">
@endsection
@section('content')
    <div class="profile">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    @include('layouts.sidebar',['current'=>'my-products'])
                </div>
                <div class="col-md-9">
                    <div class="forms">
                        <div data-active=1 id="tab-b1" class="edit-your-info a-tab">
                            <h4 style="margin-bottom: 20px;color: #f14444;">منتجاتي</h4>
                            <div class="my-prods">
                                <form action="">
                                    <label for="">
                                        عرض المنتجات الخاصه بــــ
                                    </label>
                                    <select name="category_id" class="form-control category_id">
                                        <option value="0">اختر قسم</option>
                                        @foreach(\App\Models\Categories::all() as $category)
                                            <option value="{{ $category->id }}" {{ $category_id == $category->id ? "selected":"" }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                <div class="row">
`                                   @foreach($products as $product)
                                    <div class="col-lg-4 col-sm-6 col-xs-12">
                                        <div class="a-product">
                                            <a href="/product/{{ $product->id }}/{{ urlencode($product->title) }}">
                                                <div class="product-photo">
                                                    <img src="{{ @$product->getPhotos->first() ? '/uploads/'.$product->getPhotos->first()->photo : '/site/images/no-image.png'  }}" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="/product/{{ $product->id }}/{{ urlencode($product->title) }}"><h5>{{ $product->title }}</h5></a>
                                                <p>
                                                    {{ mb_substr($product->description,0,60,'utf-8') }}
                                                </p>
                                                <div class="price"> &nbsp;<span>{{ $product->price }} ريال</span>  <img style="width: 13px;" src="/site/images/money.png" alt=""></div>
                                                <div class="right-stuff" style="float: right; line-height: 30px">
                                                    الكميه الحاليه: <span>{{ $product->quantity }}</span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a onclick="return false;" class="del" data-toggle="modal"
                                                       data-target="#myModal" href="#"
                                                       delete_url="/delete-product/{{ $product -> id }}">
                                                        <i style="color:#a94442;font-size: 20px;margin-right: 18px;float: left;" class="fa fa-remove" aria-hidden="true"></i>
                                                    </a>
                                                    <a href="/edit-product/{{ $product->id }}">
                                                        <i class="fa fa-edit" aria-hidden="true"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @if(count($products) == 0)
                                <div align="center" class="col-xs-12">
                                    <h3>عفوا لا يوجد منتجات</h3>
                                </div>
                                @endif
                                <div class="paginate">
                                    {{ $products->render() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
                {!! csrf_field() !!}

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">رسالة الحذف </h4>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            هل أنت متأكد من الحذف ؟
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق</button>
                        <a class="res" href="#">
                            <button type="button" class="btn btn-primary">نعم</button>
                        </a>
                    </div>
                </div>

        </div>
    </div>


@endsection
@section('js')
    <script>
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        //get sub categories
        $('.category_id').change(function () {
            var category_id = $(this).val();
            if(category_id!=0){
                window.location.href="/my-products/"+category_id;
            }else{
                window.location.href="/my-products";
            }

        });

        $('.del').click(function () {
            var res = $(this).attr('delete_url');
            $('.res').attr('href', res);
        });
    </script>
@endsection