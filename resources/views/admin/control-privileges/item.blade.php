<br>
<div class="elemContainer">
    <div class="col-lg-2">
        <div class="col-md-2">
            <div class="remove-extra cancel-add sweet_warning" elmType="element" method="get" @if(isset($adv))delete_url="/admin-panel/deletePrivilegeItem/{{$adv->id}}"@endif object_id="{{isset($adv)?$adv->id:'d'}}">-</div>

        </div>
        <div class="col-md-10">
            <div class="form-group">
                    <input value="{{ isset($adv) ? $adv->card_color  : old('adv_card_color')  }}" type="color" name="adv_card_color[]" class="form-control">
                    </input>
            </div>

        </div>

    </div>

    <div class="col-lg-4">
        <input  type="text" name="property[]" value="{{ isset($adv)?$adv->privilge:'' }}" class="form-control" placeholder="اسم الرابط الفرعي ">
    </div>

    <div class="col-lg-4">
        <input  style="direction: ltr" type="text" name="value[]" value="{{ isset($adv)?$adv->url:'' }}" class="form-control" placeholder="الرابط">


    </div>
    <div class="col-lg-2">
        <select name="is_hidden[]"  class="form-control feature_item">
            <option value="1" {{isset($adv)?($adv->hidden==1?'selected':''):''}}> مخفية</option>
            <option value="0" {{isset($adv)?($adv->hidden==0?'selected':''):''}} >ظاهرة</option>
        </select>

        <input   type="hidden" name="pr_id[]" value="{{ isset($adv)?$adv->id:'' }}" >
    </div>
    <br>
    <div style="margin-top: 26px;">
        <div class="col-md-2">
            <div class="form-group">
                    <input value="{{ isset($adv) ? $adv->icon  : old('adv_icon')  }}" placeholder="حدد الايقونة" name="adv_icon[]" class="form-control iconpicker action-create icp-glyphs">
            </div>

        </div>
        <div class="col-md-5">
            <div class="form-group">
                <input  placeholder="اسم الكنترولر"  value="{{ isset($adv) ? $adv->controller  : old('adv_controller')  }}" class="form-control" type="text" name="adv_controller[]">
            </div>

        </div>
        <div class="col-md-5">

            <div class="form-group">
                <input style="direction: ltr"  placeholder="العدد في الاحصائيات "  value="{{ isset($adv) ? $adv->model  : old('adv_model')  }}" class="form-control" type="text" name="adv_model[]">

            </div>

        </div>

    </div>

</div>
<div class="clearfix"></div>
<hr>