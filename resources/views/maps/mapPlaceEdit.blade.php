<div class=" form-group {{$errors->has("address") ||$errors->has("location_lat")||$errors->has("location_lng")? 'input_error':''}}">
        <label for=""><i class="fa fa-map-marker"></i>  العنوان </label>
        <input id="pac-input" value="{{ $user->address }}" name="address" type="text" class="form-control controls"  placeholder="">
        <input type="hidden" name="location_lat" value="{{ $user->latitude }}">
        <input type="hidden" name="location_lng" value="{{ $user->longitude }}">
        <input type="hidden" name="country_code" value="{{ $user->phonecode }}">
        {{--<span class="help-block">ستأتيك رسالة من الموقع للسماح بتحديد موقعك تلقائيا قم باختيار ( السماح )</span>--}}
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

