@foreach($features as $feature)
    <fieldset class="content-group">
        <div class="form-group">

            <label class="control-label col-lg-2">العنوان </label>
            <div class="col-lg-10">

                <input name="feature_title[{{$feature->id}}]" value="{{$feature->title}}" class="form-control " >

            </div>
        </div>
        <input name="feature_id[{{$feature->id}}]" type="hidden" value="{{$feature->id}}">
        <div class="form-group">

            <label class="control-label col-lg-2">وصف الميزة </label>
            <div class="col-lg-10">

                <input name="feature_description[{{$feature->id}}]" value="{{$feature->description}}" class="form-control " >

                @if ($errors->has('about_text'))
                    <span class="help-block">
																					<strong>{{ $errors->first('about_text') }}</strong>
																			</span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
            <label class="control-label col-lg-2">ايقونة الخدمة </label>
            <div class="col-md-8">
                <input value="{{ isset($feature) ? $feature->icon  : old('icon')  }}"  name="icon[{{$feature->id}}]" class="form-control iconpicker action-create icp-glyphs">
                </input>
                @if ($errors->has('icon'))
                    <span class="help-block">
		                                        <strong>{{ $errors->first('icon') }}</strong>
		                                    </span>
                @endif
            </div>
            <div class="col-md-2">
                <i style="font-size: 36px;" class="{{ isset($feature) ? $feature->icon  : old('icon')  }}"></i>
            </div>
        </div>

            <div class="form-group">
            <label class="control-label col-lg-2">أدخل صورة الميزة</label>
            <div class="col-lg-10">

                <input type="file" name="feature_photo{{$feature->id}}" class="file-input" data-show-caption="false" data-show-upload="false" data-browse-class="btn btn-primary btn-xs" data-remove-class="btn btn-default btn-xs">


                    </div>

                </div>

        @if($feature->photo)
            <div class="form-group">
                <label class="control-label col-lg-2">الصورة الحالية</label>
                <div class="col-lg-10">
                    <img alt="" width="100" height="75" src="/uploads/{{ $feature->photo }}">
                </div>

            </div>
        @endif
    </fieldset>
    <hr>
@endforeach
