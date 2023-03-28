<div id="MessagePopUp" style="margin-top: 17px;top:0;position: fixed; z-index: 50000;max-width: 600px;left: 0" class="container">
@if(Session::has('error'))
<div class="alert alert-danger media d-flex">
<button class="close media-object" data-dismiss="alert">
<b aria-hidden="true">×</b><span class="sr-only">إخفاء</span></button>
<div class="media-body message__body px-2">{{ Session::get('error') }}</div> </div>
@endif
@if(Session::has('success'))
<div class="alert alert-success media d-flex">
<button class="close media-object" data-dismiss="alert">
<b aria-hidden="true">×</b><span class="sr-only">إخفاء</span></button>
<div class="media-body message__body px-2">{!!  Session::get('success') !!} </div> </div>
@endif
@if (isset($errors) && count($errors) > 0)
                @foreach ($errors->all() as $error)


        <div class="alert alert-danger media d-flex">
<button class="close media-object" data-dismiss="alert">
<b aria-hidden="true">×</b><span class="sr-only">إخفاء</span></button>
<div class="media-body message__body px-2">   {{ $error }}</div> </div>
                @endforeach
@endif
</div>
<div id="MessagePopUpNew" style="margin-top: 17px;top:0;position: fixed; z-index: 50000;max-width: 600px;left: 0" class="container">
</div>
