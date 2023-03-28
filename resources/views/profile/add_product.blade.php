@extends('layouts.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(5)->value }} - أضافة منتج </title>
    <meta name="description" content="{{ \App\Models\Settings::find(6)->value }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(7)->value }}">

@endsection
@section('content')
    <div class="profile">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    @include('layouts.sidebar',['current'=>'add-product'])
                </div>
                <div class="col-md-9">
                    <div class="forms">
                        <div data-active=1 id="tab-b1" class="edit-your-info a-tab">
                                <h4 style="margin-bottom: 20px;color: #f14444;">أضف منتج</h4>
                                <form method="post" action="/add-product" class="reg-form">
                                    {{ csrf_field() }}
                                    <div class="">
                                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                            <label for=""><i class="fa fa-id-card"></i>  اسم المنتج </label>
                                            <input type="text" name="title" value="{{ old('title') }}" class="form-control"  placeholder="">
                                            @if ($errors->has('title'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('title') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                        @php $category_id = old('category_id') @endphp
                                        <div class="form-group {{ $errors->has('category_id') ? ' has-error' : '' }}">
                                            <label for=""><i class="fa fa-code-fork"></i> تصنيف المنتج الرئيسي</label>
                                            <select name="category_id" class="form-control category_id">
                                              <option value="">اختر التصنيف</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $category_id==$category->id ?'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('category_id'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('category_id') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                        <div class="form-group {{ $errors->has('sub_category_id') ? ' has-error' : '' }}">
                                            <label for=""><i class="fa fa-code-fork"></i> تصنيف المنتج الفرعي</label>
                                            <select name="sub_category_id" class="form-control sub_category_id">
                                                <option value="">اختر التصنيف</option>
                                                @foreach(\App\Models\Subcategories::where('category_id',$category_id)->get() as $sub_category)
                                                    <option value="{{ $sub_category->id }}" {{ old('sub_category_id')==$sub_category->id ?'selected' : '' }}>{{ $sub_category->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('sub_category_id'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('sub_category_id') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                        <div class="row">

                                            <div class="col-sm-12">
                                                <div class="form-group {{ $errors->has('price') ? ' has-error' : '' }}">
                                                    <label for=""><i class="fa fa-dollar"></i>  السعر للوحدة بالريال </label>
                                                    <input type="number" name="price" value="{{ old('price') }}" class="form-control"  placeholder="">
                                                    @if ($errors->has('price'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('price') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-sm-12">
                                                <div class="form-group {{ $errors->has('discount') ? ' has-error' : '' }}">
                                                    <label for=""><i class="fa fa-free-code-camp"></i>  الخصم بالنسبة المؤية % ( ان وجد )</label>
                                                    <input type="number" name="discount" value="{{ old('discount') }}" class="form-control"  placeholder="">
                                                    @if ($errors->has('discount'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('discount') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            {{--<div class="col-sm-4">--}}
                                                {{--<div class="form-group">--}}
                                                    {{--<label for=""><i class="fa fa-money"></i> العمله </label>--}}
                                                    {{--<select class="form-control">--}}
                                                        {{--<option>sar</option>--}}
                                                        {{--<option>usd</option>--}}
                                                    {{--</select>--}}
                                                {{--</div>--}}
                                            {{--</div>--}}
                                        </div>
                                        <div class="form-group {{ $errors->has('quantity') ? ' has-error' : '' }}">
                                            <label for=""><i class="fa fa-sort-numeric-asc"></i>  الكميه </label>
                                            <input name="quantity" value="{{ old('quantity') }}" type="number" class="form-control"  placeholder="">
                                            @if ($errors->has('quantity'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('quantity') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                        <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                                            <label for=""><i class="fa fa-list-alt"></i> الوصف</label>
                                            <textarea name="description" id="" cols="30" rows="5" class="form-control">{{ old('description') }}</textarea>
                                            @if ($errors->has('description'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('description') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                        <div style="display:none;" class="rel_photo">
                                        </div>
                                        <div class="form-group ">
                                            <div  class="dropzone" id="related_photos">
                                                <p class="click-dropzone" style="cursor: pointer">
                                                    اضغط هنا لرفع صور المنتج
                                                </p>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <button class="btn btn-default reg-btn">اضف منتج</button>
                                </form>

                        </div>
                        <div data-active=0 id="tab-b2" class="prefered-products a-tab">
                            <h4 style="margin-bottom: 20px;color: #f14444;">المنتجات المفضله</h4>
                            <div class="inner-c">
                                <div class="slick-controllers">
                                    <span class="slick-next-2 s-next-btn"><i class="fa fa-angle-right"
                                                                             aria-hidden="true"></i></span>
                                    <span class="slick-prev-2 s-prev-btn"><i class="fa fa-angle-left"
                                                                             aria-hidden="true"></i></span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="slider-2 slick-style">
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span> 20% </span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $('.slider-2').slick({
                                    prevArrow: '.slick-prev-2',
                                    nextArrow: '.slick-next-2',
                                    infinite: true,
                                    slidesToShow: 3,
                                    autoplay: true,
                                    SlidesToScroll: -1,
                                    responsive: [
                                        {
                                            breakpoint: 1024,
                                            settings: {
                                                slidesToShow: 2,
                                                slidesToScroll: 1,
                                                infinite: true,
                                                dots: true
                                            }
                                        },
                                        {
                                            breakpoint: 600,
                                            settings: {
                                                slidesToShow: 1,
                                                slidesToScroll: 1
                                            }
                                        }
                                    ]
                                });
                            </script>
                        </div>
                        <div data-active=0 id="tab-b3" class="rated-products a-tab">
                            <h4 style="margin-bottom: 20px;color: #f14444;">المنتجات منتجات قيمتها</h4>
                            <div class="inner-c">
                                <div class="slick-controllers">
                                    <span class="slick-next-3 s-next-btn"><i class="fa fa-angle-right"
                                                                             aria-hidden="true"></i></span>
                                    <span class="slick-prev-3 s-prev-btn"><i class="fa fa-angle-left"
                                                                             aria-hidden="true"></i></span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="slider-3 slick-style">
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span> 20% </span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $('.slider-3').slick({
                                    prevArrow: '.slick-prev-3',
                                    nextArrow: '.slick-next-3',
                                    infinite: true,
                                    slidesToShow: 3,
                                    autoplay: true,
                                    SlidesToScroll: -1,
                                    responsive: [
                                        {
                                            breakpoint: 1024,
                                            settings: {
                                                slidesToShow: 2,
                                                slidesToScroll: 1,
                                                infinite: true,
                                                dots: true
                                            }
                                        },
                                        {
                                            breakpoint: 600,
                                            settings: {
                                                slidesToShow: 1,
                                                slidesToScroll: 1
                                            }
                                        }
                                    ]
                                });
                            </script>
                        </div>
                        <div data-active=0 id="tab-b4" class="bought-products a-tab">
                            <h4 style="margin-bottom: 20px;color: #f14444;">منتجات تم شراؤها</h4>
                            <div class="inner-c">
                                <div class="slick-controllers">
                                    <span class="slick-next-1 s-next-btn"><i class="fa fa-angle-right"
                                                                             aria-hidden="true"></i></span>
                                    <span class="slick-prev-1 s-prev-btn"><i class="fa fa-angle-left"
                                                                             aria-hidden="true"></i></span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="slider-1 slick-style">
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span> 20% </span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="s-item">
                                        <div class="a-product">
                                            <span class="new">جديد</span>
                                            <div class="discount">خصم <span>20%</span></div>

                                            <a href="#">
                                                <div class="product-photo">
                                                    <img src="/site/images/mob-1.png" alt="">
                                                </div>
                                            </a>
                                            <div class="lower-info">
                                                <a href="#"><h5>هاتف انفينكس سمارت</h5></a>
                                                <p>
                                                    X5010 ثنائي الشريحة - سعة 16 جيجابايت- رام 1 جيجابايت
                                                </p>
                                                <div class="price"> &nbsp;<span>999 ريال</span> <img
                                                            style="width: 13px;" src="/site/images/money.png" alt="">
                                                </div>
                                                <div class="right-stuff">
                                                    <span class="rating-text"><input class="rating starrating"
                                                                                     data-show-caption="false"
                                                                                     data-min="0" data-max="5"
                                                                                     data-step=1 data-rtl=1
                                                                                     data-glyphicon=0 type="text"
                                                                                     data-size="xs" step=".5"></span>
                                                </div>
                                                <div class="left-stuff">
                                                    <a href="#">
                                                        <img style="width: 18px" src="/site/images/cart.png" alt="">
                                                    </a>
                                                    <a href="#">
                                                        <i class="fa fa-heart-o"></i>
                                                    </a>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                $('.slider-1').slick({
                                    prevArrow: '.slick-prev-1',
                                    nextArrow: '.slick-next-1',
                                    infinite: true,
                                    autoplay: true,
                                    slidesToShow: 3,
                                    SlidesToScroll: -1,
                                    responsive: [
                                        {
                                            breakpoint: 1024,
                                            settings: {
                                                slidesToShow: 2,
                                                slidesToScroll: 1,
                                                infinite: true,
                                                dots: true
                                            }
                                        },
                                        {
                                            breakpoint: 600,
                                            settings: {
                                                slidesToShow: 1,
                                                slidesToScroll: 1
                                            }
                                        }
                                    ]
                                });
                            </script>
                        </div>
                    </div>
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
            $('.sub_category_id').html("<option>جاري تحميل الاقسام ...</option>");
                $.ajax({
                    url: '/getSubcategories/' + category_id,
                    success: function (data) {
                        $('.sub_category_id').html(data);
                    }
                });
        });
    </script>
    <script src="/site/js/dropzone.js"></script>
    <link id="default-css" href="/site/css/dropzone.css" rel="stylesheet" media="all"/>
    <script type="text/javascript">
        Dropzone.autoDiscover = false;
        // initialization of main photo upload
        // initialization of main photo upload
        var relatedPhotos = new Dropzone("#related_photos", {
                url: '/files/uploadimage',
                thumbnailWidth: 200,
                thumbnailHeight: 150,
                acceptedFiles: "image/*",
                addRemoveLinks: true,
                dictDefaultMessage: "أدرج العديد من الصور للموقع",
                clickable: true,
                enqueueForUpload: true,
                // uploadMultiple:true,
                maxFilesize: 3,
                maxFiles: 9,
                sending: function(file, xhr, formData) {
                    // Pass token. You can use the same method to pass any other values as well such as a id to associate the image with for example.
                    formData.append("_token", $('meta[name="csrf-token"]').attr('content')); // Laravel expect the token post value to be named _token by default

                },
                success : function (file, response) {
                    file.fileName = response.fileName;
                    file.className = "form_file";
                    $('.help-block-message-sub').show();
                    $(".rel_photo").append("<input type='hidden' name='related_photos[]' value='" + response.fileName + "' />");


                }
                ,
                removedfile : function (file) {
                    removeFile(file);
                }
            })
        ;
        function removeFile(file) {
            var data = "photo=" + file.fileName;
            $.ajax({
                url: "/files/removefile",
                type: "POST",
                data: data,
                success: function (data) {

                    $('input[value="' + data + '"]').val('');
                    var _ref;
                    return ( _ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) :
                        void 0;

                },
                error: function () {
                    alert("error");
                }
            });
        }




    </script>

@endsection