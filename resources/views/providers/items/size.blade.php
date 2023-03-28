<div class="new_feature">
<input type="hidden" value="{{isset($adv)?$adv->id:''}}" name="size_id[]">
    @if(isset($adv))
        <a onclick="return false;" object_id="{{ $adv->id }}" delete_url="/provider-panel/remove_meal_size?size_id={{ $adv->id }}" class="remove-extra  sweet_warning"  href="#">-</a>
    @else
        <div class="remove-extra cancel-add" itemid="{{isset($adv)?$adv->id:'d'}}">-</div>
    @endif
<label class="control-label col-lg-2">الحجم </label>

<div class="col-lg-9" style="background: #ececec; padding: 5px; margin-bottom: 10px;">
    <div class="col-lg-6">
        <input  style="" type="text" name="size_title[]"    value="{{isset($adv)?$adv->title:''}}"   class="form-control " placeholder="  عنوان الحجم ( صغير - وسط - كبير )">

    </div>
    <div class="col-lg-6">
        <input   style="" type="number" name="size_price[]"    value="{{isset($adv)?$adv->price:''}}"   class="form-control " placeholder="السعر">
    </div>
    <div class="clearfix" style="margin: 14px;"></div>

    <div class="clearfix"></div>
    <br>
</div>


    <div class="clearfix"></div>
</div>