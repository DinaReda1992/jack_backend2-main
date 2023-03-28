@extends('layouts.layout')
@section('title')
<title> تعديل الحساب </title>
<meta name="description" content="{{ \App\Models\Settings::find(3)->value?:'' }}">
<meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?:''}}">
    <style>
        #avatar{
            height: 80px;
            width: 80px;
        }
        .profile-pic-con{
            overflow: hidden;background: #cbcbcb;width: 80px;height: 80px;border-radius: 50%;text-align: center;
        }
    </style>
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
                            <li class="d-inline-block">تعديل الحساب</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="register-section">
        <div class="container">
          <form method="POST" action="{{route('account')}}">
              @csrf
              <div class="d-flex align-items-center flex-column justify-content-center">

                  <div class="col-md-6 w-sm-100 mb-2" >
                      <label for="photo" class="d-flex align-items-center" >
                          <div class="d-flex align-items-center justify-content-center profile-pic-con" >
                              @if(auth()->user()->photo=='')
                                  <img id="avatar" src="/images/upload_image.svg" class="placeholder">
                              @else
                                  <img id="avatar"  src="{{url('/uploads/'.auth()->user()->photo)}}" class="placeholder">
                              @endif
                          </div>
                          <div class="px-3">
                              <span class="d-block text-dark f-14">صورة شخصية</span>
                              <span class="text-muted  f-14">قم برفع صورة شخصية واضحة لك</span>
                          </div>
                      </label>
                      <input id="photo" accept="image/png, image/jpeg"  class="sr-only" type="file" >
                  </div>
                  <div class="col-md-6 w-sm-100" >
                      <label for=""> <b>رقم الجوال</b></label>
                      <input type="text" disabled value="{{old('phone',auth()->user()->phone)}}" class="form-control" >
                      {{--                <input type="hidden" name="phone" value="{{isset($phone)?$phone:''}}" >--}}
                  </div>
                  <div class="col-md-6 w-sm-100 {{ $errors->has('username') ? ' has-error' : '' }}" >
                      <label for="username"> <b>الاسم</b></label>
                      <input type="text" id="username" name="username" value="{{old('username',auth()->user()->username)}}" required class="form-control" placeholder="الاسم بالكامل">

                      @if ($errors->has('username'))
                          <span class="help-block">
		                                        <strong>{{ $errors->first('username') }}</strong>
		                                    </span>
                      @endif

                  </div>
                  <div class="col-md-6 w-sm-100 {{ $errors->has('email') ? ' has-error' : '' }}">
                      <label for="email"> <b>البريد الالكترونى</b></label>
                      <input type="email" id="email" value="{{old('email',auth()->user()->email)}}" placeholder="البريد الالكترونى" name="email">
                      @if ($errors->has('email'))
                          <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                      @endif
                  </div>

                  <div class="col-md-6 w-sm-100">
                      <button type="submit" class="btn btn-success">تعديل</button>
                  </div>
              </div>
          </form>


        </div>
    </section>

@endsection
@section('js')
    <script>
        var imagesPreview = function(input, placeToInsertImagePreview) {
            if (input.files) {
                var filesAmount = input.files.length;
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        // $($.parseHTML('<img style="width:100px;height:100px;margin:5px">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                        $('#avatar').attr('src', event.target.result)
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }
        };
        $('#photo').on('change', function() {
            imagesPreview(this, 'div.profile-pic-con');
        });
    </script>
@endsection
