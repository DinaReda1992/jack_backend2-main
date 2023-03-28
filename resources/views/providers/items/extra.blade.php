<div class="new_feature">

    <div class="remove-extra cancel-add" itemid="{{isset($adv)?$adv->id:'d'}}">-</div>
<label class="control-label col-lg-2">الاضافة </label>

<div class="col-lg-9" style="background: #ececec; padding: 5px; margin-bottom: 10px;">
    <div class="col-lg-6">
        <input  style="direction: ltr" type="text" name="extra_name[]"   value="{{isset($adv)?$adv->name:''}}"   class="form-control price_input" placeholder="اسم الاضافة">

    </div>
    <div class="col-lg-6">
        <input  style="direction: ltr" type="text" name="extra_name_en[]"  value="{{isset($adv)?$adv->name_en:''}}"   class="form-control price_input" placeholder="الاسم باللغة الانجليزية">
    </div>
    <div class="clearfix" style="margin: 14px;"></div>

    <div class="col-lg-6">
        <input  style="direction: ltr" type="number" name="price[]"   value="{{isset($adv)?$adv->price:''}}"   class="form-control price_input" placeholder="السعر">
    </div>
    <div class="col-lg-6">
        <input  style="direction: ltr" type="number" name="limit[]"   value="{{isset($adv)?$adv->limit:''}}"   class="form-control price_input" placeholder="العدد المسموح به">
    </div>
    <div class="clearfix"></div>
    <br>
</div>

    <div class="clearfix"></div>
</div>