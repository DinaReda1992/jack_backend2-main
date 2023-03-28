{{--<style>--}}
    {{--html, body {--}}
        {{--height: 100%;--}}
        {{--margin: 0;--}}
        {{--padding: 0;--}}
    {{--}--}}

    {{--#map {--}}
        {{--height: 100%;--}}
    {{--}--}}

    {{--.controls {--}}
        {{--border-radius: 3px;--}}
        {{--box-sizing: border-box;--}}
        {{---moz-box-sizing: border-box;--}}
        {{--height: 32px;--}}
        {{--outline: none;--}}
    {{--}--}}

    {{--#pac-input {--}}
        {{--background-color: #fff;--}}
        {{--font-family: Roboto;--}}
        {{--font-size: 15px;--}}
        {{--font-weight: 300;--}}
        {{--margin-left: 12px;--}}
        {{--padding: 0 11px 0 13px;--}}
        {{--text-overflow: ellipsis;--}}
        {{--width: 300px;--}}
        {{--float: right;--}}
    {{--}--}}

    {{--#pac-input:focus {--}}
        {{--border-color: #4d90fe;--}}
    {{--}--}}

    {{--.pac-container {--}}
        {{--font-family: Roboto;--}}
    {{--}--}}

    {{--#type-selector {--}}
        {{--color: #fff;--}}
        {{--background-color: #4d90fe;--}}
        {{--padding: 5px 11px 0px 11px;--}}
    {{--}--}}

    {{--#type-selector label {--}}
        {{--font-family: Roboto;--}}
        {{--font-size: 13px;--}}
        {{--font-weight: 300;--}}
    {{--}--}}

    {{--#gmap {--}}
        {{--height: 400px;--}}
    {{--}--}}

{{--</style>--}}


    <div class=" form-group {{$errors->has("address") ||$errors->has("location_lat")||$errors->has("location_lng")? 'input_error':''}}">
        <label for=""><i class="fa fa-map-marker"></i>  العنوان </label>
        <input id="pac-input" name="address" type="text" class="form-control controls"  placeholder="">
        <input type="hidden" name="location_lat" value="">
        <input type="hidden" name="location_lng" value="">
        <input type="hidden" name="country_code" value="">
        <span class="help-block">ستأتيك رسالة من الموقع للسماح بتحديد موقعك تلقائيا قم باختيار ( السماح )</span>
        @if ($errors->has("address") || $errors->has("location_lat")||$errors->has("location_lng") )
            <span class="help-block">
              <strong>{{ $errors->first('address') }}</strong>
               <strong>{{ $errors->first('location_lat') }}</strong>
               <strong>{{ $errors->first('location_lng') }}</strong>
             </span>
        @endif
    </div>

<div class="form-group">
    <div class="map">
    <div class="element" id="gmap" style="height:400px;">
    </div>
    </div>
</div>

