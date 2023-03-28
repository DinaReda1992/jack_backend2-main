<div class="user-main-info">
    <div class="user-pic">
        <img src="{{ auth()->user()->photo ? '/uploads/'.auth()->user()->photo  :  '/site/images/no-image.png'  }}" class="img-thumbnail img-responsive profile-image" alt="">
        <a href="#uploader"  data-toggle="modal">
            <label><i style="    color: #3b383c;
    font-size: 20px;
    margin-top: 15px;
    cursor: pointer;" class="fa fa-camera changePhoto"></i></label>
        </a>
    </div>
    <h4>{{ auth()->user()->username }}</h4>
    <a href="/profile" class="btn btn-default {{ $current == "update-profile" ? 'active':'' }}">
        <i class="fa fa-edit" aria-hidden="true"></i> تعديل البيانات الشخصيه
    </a>
    @if(auth()->user()->user_type_id != 3)
        <a href="/favourite" class="btn btn-default {{ $current == "favourite" ? 'active':'' }}">
            <i class="fa fa-heart-o" aria-hidden="true"></i>                 المنتجات المفضله
        </a>
        <a href="/evaluated" class="btn btn-default {{ $current == "evaluated" ? 'active':'' }}">
            <i class="fa fa-star-o" aria-hidden="true"></i>منتجات قيمتها
        </a>
        <a href="/payed" class="btn btn-default {{ $current == "payed" ? 'active':'' }}">
            <i class="fa fa-dollar" aria-hidden="true"></i>                 منتجات قمت بشرائها
        </a>
        <a href="/outcomming-invoices" class="btn btn-default {{ $current == "outcomming-invoices" ? 'active':'' }}">
            <i class="fa fa-file" aria-hidden="true"></i>                 الفواتير المرسلة
        </a>
    @endif
    @if(auth()->user()->user_type_id != 4 )
        <a href="/my-products" class="btn btn-default {{ $current == "my-products" ? 'active':'' }}">
            <i class="fa fa-list" aria-hidden="true"></i>                 منتجاتي
        </a>
        <a href="/add-product" class="btn btn-default {{ $current == "add-product" ? 'active':'' }}">
            <i class="fa fa-plus" aria-hidden="true"></i>                 اضف منتج
        </a>
        <a href="/incomming-invoices" class="btn btn-default {{ $current == "incomming-invoices" ? 'active':'' }}">
            <i class="fa fa-file" aria-hidden="true"></i>                 الفواتير الواردة
        </a>
    @endif
</div>
@section('JsXone')
    <script src="/site/js/dropzone.js"></script>
    <link id="default-css" href="/site/css/dropzone.css" rel="stylesheet" media="all"/>
    <script>
        $(window).ready(function () {

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            initUserZropZone(1, '#uploadform');

            function initUserZropZone (type,selector) {
                Dropzone.autoDiscover = false; // keep this line if you have multiple dropzones in the same page
                UserPhotos = new Dropzone(selector,
                    {

                        acceptedFiles: ".jpg,.jpeg,.png",

                        url: '/files/uploadProfileImage',
                        maxFiles: 1, // Number of files at a time
                        maxFilesize: 1, //in MB
                        maxfilesexceeded: function (file) {
                            alert('لا يمكنك رفع أكثر من صوره , الصوره الاولى فقط هى التى سيتم تحميلها');


                        },
                        sending: function(file, xhr, formData) {
                            // Pass token. You can use the same method to pass any other values as well such as a id to associate the image with for example.
                            formData.append("_token", $('meta[name="csrf-token"]').attr('content')); // Laravel expect the token post value to be named _token by default
                            formData.append('type', type);

                        },


                        success: function (file, response) {
                            var fileName = response.fileName;
                            $('#uploader').modal('hide');
                            d = new Date();
                            if(type==1){
                                $('.profile-image').attr('src', "/uploads/" + fileName+"?"+d.getTime());

                            }
                            else if(type==2){
                                $('#cover_image').attr('src', "/uploads/" + fileName+"?"+d.getTime());

                            }
                            this.removeAllFiles();
                        },
                        addRemoveLinks: true,
                        removedfile: function (file) {
                            var _ref;
                            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                        }
                    });

            }



        })


    </script>
@endsection
<div class="modal fade" id="uploader" tabindex="-1" role="dialog" aria-labelledby="updater"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">✕</button>
                <br>
                <i class="icon-credit-card icon-7x"></i>
                <p class="no-margin">قم برفع صورة شخصية</p>
            </div>
            <div class="modal-body">
                <div id="uploadform" class="uploadform  no-margin dz-clickable">
                    <div style="cursor: pointer;" class="dz-default dz-message">
                        <span>أضغط هنا لرفع الصور</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default attachtopost" data-dismiss="modal">الغاء
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>