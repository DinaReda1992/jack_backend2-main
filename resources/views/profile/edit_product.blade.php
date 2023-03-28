@extends('layouts.layout')
@section('title')
    <title>{{ \App\Models\Settings::find(5)->value }} - تعديل منتج </title>
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
                                <h4 style="margin-bottom: 20px;color: #f14444;">تعديل منتج</h4>
                                <form method="post" action="/edit-product/{{ $product->id }}" class="reg-form">
                                    {{ csrf_field() }}
                                    <div class="">
                                        <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                                            <label for=""><i class="fa fa-id-card"></i>  اسم المنتج </label>
                                            <input type="text" name="title" value="{{ $product->title }}" class="form-control"  placeholder="">
                                            @if ($errors->has('title'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('title') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                        @php $category_id = $product->category_id @endphp
                                        <div class="form-group {{ $errors->has('category_id') ? ' has-error' : '' }}">
                                            <label for=""><i class="fa fa-code-fork"></i> تصنيف المنتج الرئيسي</label>
                                            <select name="category_id" class="form-control category_id">
                                              <option value="0">اختر التصنيف</option>
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
                                                <option value="0">اختر التصنيف</option>
                                                @foreach(\App\Models\Subcategories::where('category_id',$category_id)->get() as $sub_category)
                                                    <option value="{{ $sub_category->id }}" {{ $product->sub_category_id==$sub_category->id ?'selected' : '' }}>{{ $sub_category->name }}</option>
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
                                                    <input type="number" name="price" value="{{ $product->price }}" class="form-control"  placeholder="">
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
                                                    <input type="number" name="discount" value="{{ $product->discount }}" class="form-control"  placeholder="">
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
                                            <input name="quantity" value="{{ $product->quantity }}" type="number" class="form-control"  placeholder="">
                                            @if ($errors->has('quantity'))
                                                <span class="help-block">
                                                        <strong>{{ $errors->first('quantity') }}</strong>
                                                    </span>
                                            @endif
                                        </div>
                                        <div class="form-group {{ $errors->has('description') ? ' has-error' : '' }}">
                                            <label for=""><i class="fa fa-list-alt"></i> الوصف</label>
                                            <textarea name="description" id="" cols="30" rows="5" class="form-control">{{ $product->description }}</textarea>
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
                                        <div class="form-group {{ $errors->has('quantity') ? ' has-error' : '' }}">
                                            @foreach($photos as $photo)
                                                <div style="float: right;margin-left: 5px">
                                                    <img width="150" height="150" src="/uploads/{{ $photo->photo }}" alt="" />
                                                    <br>
                                                    <a style="text-align: center;width: 100%;float: right" href="/delete-product-photo/{{ $photo->id }}">حذف</a>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="clearfix"></div>
                                        <br>
                                    </div>
                                    <button class="btn btn-default reg-btn">تعديل منتج</button>
                                </form>

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