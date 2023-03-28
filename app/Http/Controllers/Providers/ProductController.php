<?php

namespace App\Http\Controllers\Providers;

use App\Models\ExtraCategories;
use App\Models\Make;
use App\Models\MakeYear;
use App\Models\MealSize;
use App\Models\MeasurementUnit;
use App\Models\MeasurementUnitsCategories;
use App\Models\Models;
use App\Models\ProductExtraCategories;
use App\Models\ProductMakeYear;
use App\Models\ProductPhotos;
use App\Models\Projects;
use App\Models\SpecialCategories;
use App\Models\Subcategories;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\ProductSpecification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Image;

class ProductController extends Controller
{
    public function __construct()
    {
    
        $this->middleware(function ($request, $next) {
            if(\auth()->user()->provider->add_product==0){
                die('<h1 align="center">ليس لديك صلاحية لاضافة منتجات    </h1>');

            }
            return $next($request);
        });
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type=$request->type;
        $this->check_provider_settings(433);
        $products=Products::where(function ($query) {
            $query->where('provider_id', auth()->id())
                ->orWhere('provider_id', auth()->user()->main_provider);
        })->where(function ($query)use ($type){
            if($type=='deleted'){
                $query->where('is_archived',1);
            }else{
                $query->where('is_archived',0);
            }
        })->get();
        return view('providers.products.all', ['objects' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->check_provider_settings(448);
        return view('providers.products.add');
    }

//    public function add_product($project_id = 0)
//    {
//        $this->check_provider_settings(448);
//
//        return view('providers.products.add', ['project' => Projects::find($project_id)]);
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->check_provider_settings(448);
        $messages = [
            "photos.max" => "file can't be more than 6."
        ];

        $this->validate($request, [
            'category_id'=>'required',
            'subcategory_id'=>'required',
            'measurement_id'=>'required',
            'title' => 'required',
            'title_en' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'min_quantity' => 'max:'.$request->quantity,
//            'description_en' => 'required',
            'photo' => 'required',
            'photos' => 'min:3'
        ],$messages);
        $object = new Products();
        $object->category_id=$request->category_id;
        $object->subcategory_id=$request->subcategory_id;
        $object->measurement_id=$request->measurement_id;

        $object->title = $request->title;
        $object->title_en = $request->title_en?:$request->title;
        $object->price = $request->price;
        $object->price_after_discount=$request->price_after_discount?:'';
        $object->quantity = $request->quantity;
        $object->min_quantity = $request->min_quantity;

        $object->description = $request->description?:'';
        $object->description_en = $request->description_en?:($request->description?:'');
        $object->usage_ar=$request->usage_ar?:'';
        $object->usage_en=$request->usage_en?:'';

        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $object->provider_id=$provider_id;
//        $file = $request->file('photo');
//        if ($request->hasFile('photo')) {
//            $fileName = 'meal-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
//            $destinationPath = 'uploads';
//            $request->file('photo')->move($destinationPath, $fileName);
//            $object->photo = $fileName;
//        }


        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $ext=$file->getClientOriginalExtension();
            if(in_array($ext,['jfif','tmp']))$ext='jpg';

            $fileName = 'product-'.time().'-'.uniqid().'.'.$file->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo=$fileName;
            $mediumthumbnailpath = public_path('uploads/'.$fileName);
            $target=public_path('uploads/thumbs/'.$fileName);
            $this->createThumbnail($mediumthumbnailpath, 300, 185,$target);
            $object->thumb=$fileName;

        }
        $object->save();
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file_) {
                $ext=$file_->getClientOriginalExtension();
                if(in_array($ext,['jfif','tmp']))$ext='jpg';
                $fileName = 'products-' . time() . '-' . uniqid() . '.' . $ext;
                $destinationPath = 'uploads';
                $file_->move($destinationPath, $fileName);
                $object1 = new ProductPhotos();
                $object1->photo = $fileName;
                $object1->product_id = $object->id;

                $mediumthumbnailpath = public_path('uploads/'.$fileName);
                $target=public_path('uploads/thumbs/'.$fileName);
                $this->createThumbnail($mediumthumbnailpath, 300, 185,$target);
                $object1->thumb=$fileName;

                $object1->save();


            }
        }

        return redirect()->back()->with('success', 'تم اضافة المنتج بنجاح .');
    }
    public function createThumbnail($path, $width, $height,$target)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($target);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->check_provider_settings(449);

        $product = Products::where(function ($query){
            $query->where('provider_id', auth()->id())
                ->orWhere('provider_id', auth()->user()->main_provider);

        })->where('id',$id)->first();
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        if($product->provider_id!=$provider_id){
            return abort(404);
        }
//        $makes = Make::where('stop',0)->where('is_archived',0)->orderBy('sort','desc')->get();
$my_subcategory=$product->subcategory_id;
        $my_measurements = MeasurementUnit::whereIn('id', function ($query) use ($my_subcategory) {
            $query->select('measurement_id')
                ->from(with(new MeasurementUnitsCategories())->getTable())
                ->where('category_id', $my_subcategory);
        })->groupBy('name')->get();
        $my_subcategories=Subcategories::where('category_id',$product->category_id)->where('is_archived',0)->get();

        return view('providers.products.add', ['object' => $product,'my_measurements'=>$my_measurements,'my_subcategories'=>$my_subcategories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $this->check_provider_settings(449);

        $object  = Products::where(function ($query){
            $query->where('provider_id', auth()->id())
                ->orWhere('provider_id', auth()->user()->main_provider);

        })->where('id',$id)->first();
        $messages = [
            "photos.max" => "file can't be more than 6."
        ];

        $this->validate($request, [
            'category_id'=>'required',
            'subcategory_id'=>'required',
            'measurement_id'=>'required',
            'title' => 'required',
//            'title_en' => 'required',
            'price' => 'required',
            'quantity' => 'required',
'min_quantity'=>'max:'.$request->quantity,
//            'description' => 'required',
//            'description_en' => 'required',
//            'photo' => 'required',
            'photos' => 'max:'.(6-$object->photos->count())
        ],$messages);
        $object->category_id=$request->category_id;
        $object->subcategory_id=$request->subcategory_id;
        $object->measurement_id=$request->measurement_id;

        $object->title = $request->title;
        $object->title_en = $request->title_en?:$request->title;
        $object->price = $request->price;
        $object->price_after_discount=$request->price_after_discount?:'';
        $object->quantity = $request->quantity;
        $object->min_quantity = $request->min_quantity;

        $object->description = $request->description?:'';
        $object->description_en = $request->description_en?:($request->description?:'');
        $object->usage_ar=$request->usage_ar?:'';
        $object->usage_en=$request->usage_en?:'';

        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;

        $object->provider_id=$provider_id;
        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $ext=$file->getClientOriginalExtension();
            if(in_array($ext,['jfif','tmp']))$ext='jpg';

            $fileName = 'meal-' . time() . '-' . uniqid() . '.' . $ext;
            $destinationPath = 'uploads';
            $request->file('photo')->move($destinationPath, $fileName);
            $object->photo = $fileName;
        }

        $object->save();
        ProductMakeYear::where('product_id',$object->id)->delete();
        if ($request->year_ids) {
            foreach ($request->year_ids as $key => $value) {
                $productYear = new ProductMakeYear();
                $productYear->product_id = $object->id;
                $productYear->make_year_id = $value;
                $productYear->save();
            }
        }

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file_) {
                $ext=$file_->getClientOriginalExtension();
                if(in_array($ext,['jfif','tmp']))$ext='jpg';

                $fileName = 'products-' . time() . '-' . uniqid() . '.' . $ext;
                $destinationPath = 'uploads';
                $file_->move($destinationPath, $fileName);
                $object1 = new ProductPhotos();
                $object1->photo = $fileName;
                $object1->product_id = $object->id;
                $object1->save();
            }
        }

        return redirect()->back()->with('success', 'تم تعديل المنتج بنجاح .');
    }
    public function deleteProductPhoto($id)
    {
        $this->check_provider_settings(383);
        $photo =ProductPhotos::find($id);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        if($photo && $photo->product->provider_id==$provider_id){
            $path = 'uploads/'.$photo->photo;
            if(is_file($path))	unlink($path);
            $photo->delete();
            //status = 0 جديده
//status = 1 مقبولة
//status = 2 معطله
//status = 3 مرفوضة
//            $hall=$photo->hall;
//            $hall->status=0;
//            $hall->save();
            return redirect()->back()->with('success','تم حذف الصورة .');
        }
        return  redirect()->back()->with('error','لا منتج للتعديل');

    }
    public function mealSizeAdd(){

        return view('providers.items.size');
    }
    public function deleteProductSize(Request $request)
    {
        $this->check_provider_settings(383);
        $size =MealSize::find($request->size_id);
        $provider_id=Auth::user()->user_type_id==3?Auth::id():Auth::user()->main_provider;
        if($size && $size->meal->user_id==$provider_id){
            $size->delete();
            return redirect()->back()->with('success','تم حذف الحجم .');
        }
        return  redirect()->back()->with('error','لا توجد منتج للتعديل');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->check_provider_settings(450);

        $ads = $product = Products::where(function ($query){
            $query->where('provider_id', auth()->id())
                ->orWhere('provider_id', auth()->user()->main_provider);

        })->where('id',$id)->first();
        $ads->is_archived=1;
        $ads->save();
        return 1;
//        $path = 'uploads/';
//
//        if ($ads != false) {
//            $ads = Products::find($ads->id);
//
//            $product_photos=$ads->getPhotos;
//            foreach($product_photos as $item){
//                $old_file = $path.$item->photo;
//                if(is_file($old_file))	unlink($old_file);
//                $item->delete();
//            }
//            $years=ProductMakeYear::where('product_id',$ads->id)->delete();
//
//            $old_photo = $path.$ads->photo;
//            if(is_file($old_photo))	unlink($old_photo);
//
//            $ads->delete();
//        }
    }

    public function product_archived_restore($id)
    {
        $this->check_provider_settings(450);

        $ads = $product = Products::where(function ($query){
            $query->where('provider_id', auth()->id())
                ->orWhere('provider_id', auth()->user()->main_provider);

        })->where('id',$id)->first();
        $ads->is_archived=0;
        $ads->save();
        return 1;

    }

}
