@extends('layouts.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(5)->value }} - الملف الشخصي</title>
    <meta name="description" content="{{ \App\Models\Settings::find(6)->value }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(7)->value }}">

@endsection
@section('content')
    <div class="profile">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    @include('layouts.sidebar',['current'=>'update-profile'])
                </div>
                <div class="col-md-9">
                    <div class="forms">
                        <div data-active=1 id="tab-b1" class="edit-your-info a-tab">
                            <h4 style="margin-bottom: 20px;color: #f14444;">تعديل البيانات الشخصيه</h4>
                            <form action="/update-profile" method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div align="center" class="col-sm-12">
                                        <div class="choose">
                                            <h5> نوع الحساب</h5>
                                            <ol class="registrations-radio-icons">
                                                <li>
                                                    <label><input name="user_type_id" type="radio"
                                                                  value="3" {{ $user->user_type_id == 3 ? 'checked' : ''  }} ><span
                                                                title="Buying"><i
                                                                    class="fa fa-tag"></i><p>بائع</p></span></label>
                                                </li>
                                                <li>
                                                    <label><input name="user_type_id" type="radio"
                                                                  value="4" {{ $user->user_type_id == 4 ? 'checked' : ''  }}><span
                                                                title="Selling"><i class="fa fa-shopping-cart"></i><p>مشتري</p></span></label>
                                                </li>
                                                <li>
                                                    <label><input name="user_type_id" type="radio"
                                                                  value="5" {{ $user->user_type_id == 5 ? 'checked' : ''  }}><span
                                                                title="Both"><i class="fa fa-exchange"></i><p>كلاهما</p></span></label>
                                                </li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="">
                                            <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
                                                <label for=""><i class="fa fa-user"></i> اسم المستخدم </label>
                                                <input type="text" value="{{ $user->username }}" class="form-control"
                                                       name="username" placeholder="">
                                                @if ($errors->has('username'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            @if(auth()->user()->user_type_id!=4)
                                                <div class="form-group {{ $errors->has('bio') ? ' has-error' : '' }}">
                                                    <label for=""><i class="fa fa-sticky-note"></i> نبذة عن المتجر </label>
                                                    <input type="text" value="{{ $user->bio }}" class="form-control"
                                                           name="bio" placeholder="">
                                                    @if ($errors->has('bio'))
                                                        <span class="help-block">
                                                        <strong>{{ $errors->first('bio') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>
                                             @endif

                                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                                <label for=""><i class="fa fa-envelope"></i> البريد الالكتروني </label>
                                                <input type="email" value="{{ $user->email }}" name="email"
                                                       class="form-control" placeholder="">
                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                                <label for=""><i class="fa fa-phone"></i> رقم الجوال </label>
                                                <input readonly type="tel" value="{{ $user->phone }}" name="phone"
                                                       class="form-control"
                                                       placeholder="">
                                                @if ($errors->has('phone'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('phone') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                                <label for=""><i class="fa fa-lock"></i> كلمة المرور الجديدة </label>
                                                <input type="password" name="password" class="form-control"
                                                       placeholder="">
                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                                <label for=""><i class="fa fa-lock"></i> تأكيد كلمة المرور الجديدة
                                                </label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                       placeholder="">
                                                @if ($errors->has('password_confirmation'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        @include('maps.mapPlaceEdit',['user'=>$user])
                                    </div>
                                </div>
                                <button class="btn btn-default reg-btn">تعديل الملف الشخصي</button>
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
    <script src="/site/js/maps.js" ></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCkQ8_neT4uZpVaXG1SbZNWKH1fnQHnbGk&libraries=places&callback=initMap&language=ar"
            async defer></script>
    <script>
        $( window ).load(function() {
            getCurrentLocationEdit({{ $user->latitude }},{{ $user->longitude }});
        })
    </script>
@endsection