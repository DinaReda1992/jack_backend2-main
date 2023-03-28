<div class="new_feature">
    <?php
        $max_price=isset($adv)?$adv->feature->max_price:'';
    $min_price=isset($adv)?$adv->feature->min_price:'';

    ?>

    <div class="remove-extra cancel-add" itemid="{{isset($adv)?$adv->id:'d'}}">-</div>
<label class="control-label col-lg-2">الاضافة </label>

<div class="col-lg-9" style="background: #ececec; padding: 5px; margin-bottom: 10px;">
    <div class="col-lg-6">
        <select name="features[]"  class="form-control feature_item">
            <option value="">اختر الاضافة</option>
            @foreach($features as $feature)
                <option value="{{ $feature->id }}" max_price="{{$feature->max_price}}" min_price="{{$feature->min_price}}" {{isset($adv)?($adv->feature->id==$feature->id?'selected':''):''}} >{{ $feature->name  }} {{$feature->is_one?'(للكل)':'(حسب عدد الاشخاص)'}}</option>
            @endforeach
        </select>

    </div>
    <div class="col-lg-6">
        <input  style="direction: ltr" type="number" name="price[]" max="{{$max_price}}" min="{{$min_price}}"   value="{{isset($adv)?$adv->price:''}}"   class="form-control price_input" placeholder="السعر">
   <label class="price_note"> السعر لابد ان يكون بين ({{$min_price.' - '.$max_price}})</label>
    </div>
    <div class="clearfix" style="margin: 14px;"></div>

    <div class="col-lg-6">
        <input  style="direction: ltr" type="text" name="description_[]" value="{{isset($adv)?$adv->description:''}}"  class="form-control" placeholder="الوصف">
    </div>
    <div class="col-lg-6">
        <input  style="direction: ltr" type="text" name="description_en_[]" value="{{isset($adv)?$adv->description_en:''}}"  class="form-control" placeholder="الوصف بالانجليزى">
    </div>
    <div class="clearfix"></div>
    <br>
</div>

    <div class="clearfix"></div>
</div>