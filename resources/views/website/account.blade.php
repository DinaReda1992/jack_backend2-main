@extends('website.layout')
@section('title')
    <title> {{__('dashboard.edit_profile')}} </title>
    <meta name="description" content="{{ \App\Models\Settings::find(3)->value ?: '' }}">
    <meta name="keywords" content="{{ \App\Models\Settings::find(4)->value ?: '' }}">
    <style>
        #avatar {
            height: 80px;
            width: 80px;
        }

        .profile-pic-con {
            overflow: hidden;
            background: #cbcbcb;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            text-align: center;
        }
    </style>
@stop
@section('content')
    <main id="content">
        <section class="py-2 bg-gray-2">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-site py-0 d-flex justify-content-right">
                        <li class="breadcrumb-item"><a class="text-decoration-none text-body" href="/">{{__('dashboard.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active pl-0 d-flex align-items-center" aria-current="page">{{__('dashboard.edit_profile')}}
                        </li>
                    </ol>
                </nav>
            </div>
        </section>

        <section class="pb-11 pb-lg-13">
            <div class="container">
                @if (Session::has('message'))
                    <div class="alert alert-info" role="alert" style="text-align: center;">
                        {{ Session::get('message') }}
                    </div>
                @endif
                <h2 class="text-center mt-9 mb-8">{{__('dashboard.edit_profile')}}</h2>
                <form method="POST" action="{{ route('account') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex align-items-center flex-column justify-content-center">

                        <div class="col-md-6 w-sm-100 mb-2">
                            <label for="photo" class="d-flex align-items-center">
                                <div class="d-flex align-items-center justify-content-center profile-pic-con">
                                    @if (auth('client')->user()->photo == '')
                                        <img id="avatar" src="/images/upload_image.svg" class="placeholder" alt="avatar" >
                                        <i class="fal fa-edit"></i>
                                    @else
                                        <img id="avatar" src="{{ url('/uploads/' . auth('client')->user()->photo) }}"
                                          alt="avatar"  class="placeholder">
                                        <i class="fal fa-edit"></i>
                                    @endif
                                </div>
                                <div class="px-3">
                                    <span class="d-block text-dark f-14">{{__('dashboard.profile_photo')}}</span>
                                    <span class="text-muted  f-14">{{__('dashboard.upload_image')}}</span>
                                </div>
                            </label>
                            <input id="photo" name="photo" accept="image/png, image/jpeg" class="sr-only" type="file">
                        </div>
                        <div class="col-md-6 w-sm-100">
                            <label for=""> <b>{{__('dashboard.phone')}}</b></label>
                            <input type="text" disabled value="{{ old('phone', auth('client')->user()->phone) }}"
                                class="form-control">
                        </div>
                        <div class="col-md-6 w-sm-100 {{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username"> <b>{{__('dashboard.name')}}</b></label>
                            <input type="text" id="username" name="username"
                                value="{{ old('username', auth('client')->user()->username) }}" required
                                class="form-control" placeholder="{{__('dashboard.name')}}">

                            @if ($errors->has('username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            @endif

                        </div>
                        <div class="col-md-6 w-sm-100 {{ $errors->has('email') ? ' has-error' : '' }} pb-3">
                            <label for="email"> <b>{{__('dashboard.email')}}</b></label>
                            <input type="email" id="email" value="{{ old('email', auth('client')->user()->email) }}"
                                placeholder="{{__('dashboard.email')}}" name="email" class="form-control">
                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="col-md-6 w-sm-100 text-center">
                            <button type="submit" class="btn btn-primary pl-2 pr-2 pb-1 pt-1">{{__('dashboard.edit')}}</button>
                        </div>
                    </div>
                </form>


            </div>
        </section>
    </main>
@endsection
@section('js')
    <script>
        var imagesPreview = function(input, placeToInsertImagePreview) {
            if (input.files) {
                var filesAmount = input.files.length;
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $($.parseHTML('<img alt="avatar" style="width:100px;height:100px;margin:5px">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
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
