<?php

namespace App\Http\Controllers\Panel;

use Carbon\Carbon;
use App\Models\User;
use App\Http\Requests;
use App\Models\Banners;
use App\Models\CartItem;
use App\Models\Products;
use App\Models\Projects;
use App\Models\Categories;
use App\Models\SupplierData;
use App\Models\UsersRegions;
use Illuminate\Http\Request;
use App\Models\ProductPhotos;

use App\Exports\ProductsExport;
use App\Models\MeasurementUnit;
use App\Models\ProductsRegions;
use App\Models\ProductCategories;
use App\Services\SendNotification;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\ProductSpecification;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use App\Models\MeasurementUnitsCategories;

class ProductController extends Controller
{
    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $this->check_settings((new \ReflectionClass($this))->getShortName());
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
        //        $suppliers = User::select('users.id', 'supplier_data.supplier_name')
        //            ->join('supplier_data', 'supplier_data.user_id', 'users.id')
        //            ->where('users.is_archived', 0)
        //            ->where('users.user_type_id', 3)
        //            ->where('supplier_data.stop', 0)->get();
        //        $this_supplier = null;
        //        if ($request->supplier_id) {
        //            $this_supplier = SupplierData::where('user_id', $request->supplier_id)->first();
        //        }
        return view('admin.products.all'); //['suppliers' => $suppliers, 'this_supplier' => $this_supplier]
    }

    public function productsData(Request $request)
    {
        $type = $request->type;
        //        $supplier_id = $request->supplier_id;
        $products = Products::where(function ($query) use ($type) { //$supplier_id
            if ($type == 'deleted') {
                $query->where('is_archived', 1);
            } else {
                $query->where('is_archived', 0);
            }

            //            if ($supplier_id) {
            //                $query->where('provider_id', $supplier_id);
            //            }
        });
        return DataTables::of($products)
            ->editColumn('supplier', function ($product) {
                return '<a href="/admin-panel/products?supplier_id=' . $product->provider_id . '">' . @$product->user->supplier->supplier_name . '</a>';
            })
            ->addColumn('measurement', function ($product) {
                return @$product->measurement->name;
            })
            ->addColumn('title', function ($product) {
                return  app()->getLocale() == 'ar' ? $product->title : $product->title_en;
            })
            ->addColumn('price', function ($product) {
                return $product->original_price ;
            })
            ->addColumn('status', function ($product) {
                return '<div class="checkbox checkbox-switchery switchery-sm switchery-double">
                <input type="checkbox" object_id="' . $product->id . '" delete_url="/admin-panel/stop_product/' . $product->id . '" class="switchery sweet_switch"' . ($product->stop == 0 ? 'checked' : '') . ' /></div>';
            })
            ->addColumn('image', function ($product) {
                return '<img alt="" width="50" height="50" src="/uploads/' . $product->photo . '">';
            })
            ->addColumn('actions', function ($product) {
                $ul = '<ul class="icons-list">';
                if ($product->is_archived == 1) {
                    $ul = '<li class="text-teal-600"><a onclick="return false;"
                    object_id="' . $product->id . '"
                    method="get"
                    delete_url="/admin-panel/product_archived_restore/' . $product->id . '"
                    class="sweet_warning" method="get" href="#" message="هل انت متأكد من استعادة المنتج"><i
       class="fa  fa-refresh"></i> استعادة</a></li>';
                } else {
                    $ul .= '<li class="text-primary-600"><a href="/admin-panel/products/' . $product->id . '/edit"><i class="icon-pencil7"></i></a></li>';
                    $ul .= '<li class="text-danger-600"><a onclick="return false;" object_id="' . $product->id
                        . '" delete_url="/admin-panel/products/' . $product->id
                        . '"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>';
                }

                $ul .= '</ul>';
                return $ul;
            })
            ->make(true);
    }


    public function all_products()
    {
        return view(
            'admin.products.sort_products',
            ['objects' => Products::where('is_archived', 0)->select('id', 'title', 'title_en', 'photo', 'sort', 'is_archived')->orderBy('sort', 'ASC')->get()]
        );
    }


    public function change_sort(Request $request)
    {
        if ($request->single) {
            $privilege = Products::find($request->id);
            $privilege->sort = $request->sort;
            $privilege->save();
        } else {
            foreach ($request->position as $key => $value) {
                $privilege = Products::find($value);
                $privilege->sort = $key;
                $privilege->save();
            }
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //        $suppliers = User::select('users.id', 'supplier_data.supplier_name')
        //            ->join('supplier_data', 'supplier_data.user_id', 'users.id')
        //            ->where('users.is_archived', 0)
        //            ->where('users.user_type_id', 3)
        //            // ->where('supplier_data.stop', 0)
        //            ->get();
        //        $supplier_id = $request->supplier_id;
        $categories = Categories::select('categories.id', 'name')
            ->where('categories.stop', 0)
            ->where('categories.is_archived', 0)
            ->orderBy('categories.sort', 'asc')
            ->get();
        return view('admin.products.add', ['categories' => $categories]); //'suppliers' => $suppliers, 'supplier_id' => $supplier_id,
    }

    public function add_product($project_id = 0)
    {
        return view('admin.products.add', ['project' => Projects::find($project_id)]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'title_en' => 'required',
            //            'provider_id' => 'required',
            //            'category_id' => 'required',
            'measurement_id' => 'required',
            'expiry' => 'required',
            'temperature' => 'required',
            'deliver_status' => 'required',
            'min_quantity' => 'nullable',
            'weight' => 'required',
            'quantity' => 'nullable',
            'original_price' => 'required',
            //            'price' => 'required',
            'description' => 'required',
            'photo' => 'required',
            'client_price' => 'nullable',
            'subcategory_id' => 'required',
            'min_warehouse_quantity' => 'nullable',
        ]);
        $object = new Products();
        $object->title = $request->title;
        $object->title_en = $request->title_en;
        $object->provider_id = $request->provider_id ?: null;
        $object->category_id = $request->category_id ?: 0;
        $object->subcategory_id = $request->subcategory_id ?: 0;
        $object->measurement_id = $request->measurement_id;
        $object->expiry = $request->expiry;
        $object->temperature = $request->temperature;
        $object->deliver_status = $request->deliver_status;
        $object->min_quantity = $request->min_quantity?:1;
        $object->weight = $request->weight;
        $object->quantity = $request->quantity?:0;
        $object->original_price = $request->original_price;
        $object->profit_perc = $request->profit_perc ?: 0;
        $object->price = $request->original_price ?: 0;
        $object->client_price = $request->client_price? : 0;
        $object->min_warehouse_quantity = $request->min_warehouse_quantity?:0;
        $object->has_regions1 = $request->has_regions ? 1 : 0;
        $object->has_cover = $request->has_cover ? 1 : 0;
        $object->description = $request->description;
        $object->description_en = $request->description_en ?: '';

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $ext = $file->getClientOriginalExtension();
            if (in_array($ext, ['jfif', 'tmp'])) $ext = 'jpg';

            $file = $request->file('photo');
            $fileName = 'product-' . time() . '-' . uniqid() . '.' .'webp'; // . $file->getClientOriginalExtension();
            $fileName_thumb = 'thumb-' . $fileName;
            $destinationPath = 'uploads/';
            $path = $file->getRealPath();
            $width = 500;
            $height = 500;
            $this->uploadImage($path, $fileName, $destinationPath, $width, $height);
            $object->photo = $fileName;
            $path = url('uploads/' . $fileName);
            $target = public_path('uploads/');
            $this->uploadImage($path, $fileName_thumb, $target, $width / 2, $height / 2);
            $object->thumb = $fileName_thumb;
            /**/
        }

        $object->save();
        if ($object) {
            $object->refresh();
            SendNotification::createProduct($object);

            if ($request->hasFile('photos')) {
                $files = $request->file('photos');
                foreach ($files as $file_) {
                    $ext = $file_->getClientOriginalExtension();
                    if (in_array($ext, ['jfif', 'tmp'])) $ext = 'jpg';

                    $fileName = 'products-' . time() . '-' . uniqid() . '.' . 'webp'; // . $file->getClientOriginalExtension();
                    $destinationPath = 'uploads';
                    $file_->move($destinationPath, $fileName);
                    $object1 = new ProductPhotos();
                    $object1->photo = $fileName;
                    $object1->product_id = $object->id;
                    $object1->save();
                }
            }

            if ($request->categories) {
                foreach ($request->categories as $key => $value) {
                    if ($request->categories[$key]) {
                        $adv = new ProductCategories();
                        $adv->category_id = $request->categories[$key];
                        $adv->product_id = $object->id;
                        $adv->save();
                    }
                }
            }
            if ($request->regions && $request->has_regions) {
                foreach ($request->regions as $key => $value) {
                    if ($request->regions[$key]) {
                        $adv = new ProductsRegions();
                        $adv->region_id = $request->regions[$key];
                        $adv->product_id = $object->id;
                        $adv->save();
                    }
                }
            }
            if ($request->states && $request->has_regions) {
                foreach ($request->states as $key => $value) {
                    if ($request->states[$key]) {
                        $adv = new ProductsRegions();
                        $adv->state_id = $request->states[$key];
                        $adv->product_id = $object->id;
                        $adv->save();
                    }
                }
            }
            return redirect()->back()->with('success', 'تم اضافة المنتج بنجاح .');
        } else {
            return redirect()->back()->with('error', 'حدث خطأ ما يرجى المحاولة مرة أخرى.');
        }
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

    public static function uploadImage($path, $fileName, $destinationPath, $width, $height)
    {
        $image = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath . $fileName);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //        $suppliers = User::select('users.id', 'supplier_data.supplier_name')
        //            ->join('supplier_data', 'supplier_data.user_id', 'users.id')
        //            ->where('users.is_archived', 0)
        //            ->where('users.user_type_id', 3)
        //            // ->where('supplier_data.stop', 0)
        //            ->get();
        $product = Products::find($id);
        $categories = Categories::select('categories.id', 'name')
            ->where('categories.stop', 0)
            ->where('categories.is_archived', 0)
            ->orderBy('categories.sort', 'asc')
            ->get();
        $productCategories = ProductCategories::where('product_id', $id)->pluck('category_id')->toArray();

        $current_regions = ProductsRegions::where('product_id', $id)->where('state_id', null)->pluck('region_id')->toArray();
        $current_states = ProductsRegions::where('product_id', $id)->where('region_id', 0)->pluck('state_id')->toArray();
        return view('admin.products.add', [
            'object' => $product,
            //            'suppliers' => $suppliers,
            //            'my_measurements'=>$my_measurements,
            'current_regions' => $current_regions,
            'current_states' => $current_states,
            'categories' => $categories,
            'productCategories' => $productCategories
        ]);
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
        $object = Products::find($id);
        if (!$object) return abort(404);
        $this->validate($request, [
            'title' => 'required',
            'title_en' => 'required',
            //            'provider_id' => 'required',
            //            'category_id' => 'required',
            'measurement_id' => 'required',
            'expiry' => 'required',
            'temperature' => 'required',
            'deliver_status' => 'required',
            'min_quantity' => 'nullable',
            'weight' => 'required',
            'quantity' => 'nullable',
            'original_price' => 'required',
            //            'price' => 'required',
            'description' => 'required',
            'photo' => !$object->photo ? 'required' : '',
            'client_price' => 'nullable',
            'subcategory_id' => 'required',
            'min_warehouse_quantity' => 'nullable',
        ]);
        $object->title = $request->title;
        $object->title_en = $request->title_en;
        $object->provider_id = $request->provider_id ?: null;
        $object->category_id = $request->category_id ?: 0;
        $object->subcategory_id = $request->subcategory_id ?: 0;
        $object->measurement_id = $request->measurement_id;
        $object->expiry = $request->expiry;
        $object->temperature = $request->temperature;
        $object->deliver_status = $request->deliver_status;
        $object->min_quantity = $request->min_quantity?:1;
        $object->weight = $request->weight;
        $object->quantity = $request->quantity?:0;
        $object->original_price = $request->original_price;
        $object->profit_perc = $request->profit_perc ?: 0;
        $object->price = $request->original_price  ?: 0;
        $object->client_price = $request->client_price?:0;
        $object->min_warehouse_quantity = $request->min_warehouse_quantity?:0;
        $object->has_regions1 = $request->has_regions ? 1 : 0;

        $object->description = $request->description;
        $object->description_en = $request->description_en ?: '';
        $object->has_cover = $request->has_cover ? 1 : 0;

        $file = $request->file('photo');
        if ($request->hasFile('photo')) {
            $old_file = 'uploads/' . $object->photo;
            if (is_file($old_file)) unlink($old_file);
            if ($object->thumb != "") {
                $old_file_thumb = 'uploads/' . $object->thumb;
                if (is_file($old_file_thumb)) @unlink($old_file_thumb);
            }
            $file = $request->file('photo');
            $fileName = 'product-' . time() . '-' . uniqid() . '.' . 'webp'; // . $file->getClientOriginalExtension();
            $fileName_thumb = 'thumb-' . $fileName;
            $destinationPath = 'uploads/';
            $path = $file->getRealPath();
            $width = 500;
            $height = 500;
            $this->uploadImage($path, $fileName, $destinationPath, $width, $height);
            $object->photo = $fileName;
            $path = url('uploads/' . $fileName);
            $target = public_path('uploads/');
            $this->uploadImage($path, $fileName_thumb, $target, $width / 2, $height / 2);
            $object->thumb = $fileName_thumb;
        }

        $object->save();

        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file_) {
                $ext = $file_->getClientOriginalExtension();
                if (in_array($ext, ['jfif', 'tmp'])) $ext = 'jpg';

                $fileName = 'products-' . time() . '-' . uniqid() . '.'. 'webp'; //  $ext;
                $destinationPath = 'uploads';
                $file_->move($destinationPath, $fileName);
                $object1 = new ProductPhotos();
                $object1->photo = $fileName;
                $object1->product_id = $object->id;
                $object1->save();
            }
        }
        //        return $request->regions;

        if ($request->categories) {
            $product_categoris = ProductCategories::where('product_id', $object->id)->whereNotIn('category_id', $request->categories)->delete();
            foreach ($request->categories as $key => $value) {
                if ($request->categories[$key]) {
                    $ifExist = ProductCategories::where('category_id', $request->categories[$key])->where('product_id', $object->id)->first();
                    if (!$ifExist) {
                        $adv = new ProductCategories();
                        $adv->category_id = $request->categories[$key];
                        $adv->product_id = $object->id;
                        $adv->save();
                    }
                }
            }
        }
        if ($request->regions && $request->has_regions) {
            $exists_regions = ProductsRegions::where('product_id', $object->id)->whereNotIn('region_id', $request->regions)->delete();
            foreach ($request->regions as $key => $value) {
                if ($request->regions[$key]) {
                    $ifExist = ProductsRegions::where('region_id', $request->regions[$key])->where('product_id', $object->id)->first();
                    if (!$ifExist) {
                        $adv = new ProductsRegions();
                        $adv->region_id = $request->regions[$key];
                        $adv->product_id = $object->id;
                        $adv->save();
                    }
                }
            }
        }
        if ($request->states && $request->has_regions) {
            $exists_states = ProductsRegions::where('product_id', $object->id)->whereNotIn('state_id', $request->states)->delete();
            foreach ($request->states as $key => $value) {
                if ($request->states[$key]) {
                    $ifExist = ProductsRegions::where('state_id', $request->states[$key])->where('product_id', $object->id)->first();
                    if (!$ifExist) {
                        $adv = new ProductsRegions();
                        $adv->state_id = $request->states[$key];
                        $adv->product_id = $object->id;
                        $adv->save();
                    }
                }
            }
        }
        if ($object->wasChanged()) {
            if (array_key_exists('price', $object->getChanges())) {
                CartItem::Where('order_id', 0)->where('item_id', $object->id)->update(['price' => $object->getChanges()['price']]);
            }
            SendNotification::editProduct($object);
        }
        return redirect()->back()->with('success', 'تم تعديل المنتج بنجاح .');
    }

    public function deleteProductPhoto($id)
    {
        $photo = ProductPhotos::find($id);
        if ($photo) {
            $path = 'uploads/' . $photo->photo;
            if (is_file($path)) unlink($path);
            $photo->delete();
            return redirect()->back()->with('success', 'تم حذف الصورة .');
        }
        return redirect()->back()->with('error', 'لا منتج للتعديل');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $product = Products::where('id', $id)->first();
        $product->is_archived = 1;
        $product->save();
        if ($product->wasChanged()) {
            SendNotification::deleteProduct($product);
        }
        return 1;
    }

    public function stop_product($id)
    {

        $product = Products::where('id', $id)->first();
        $product->stop = $product->stop == 1 ? 0 : 1;
        $product->save();
        if ($product->wasChanged()) {
            SendNotification::stopProduct($product);
        }
        return 1;
    }

    public function product_archived_restore($id)
    {
        $ads = $product = Products::where('id', $id)->first();
        $ads->is_archived = 0;
        $ads->save();
        if ($ads->wasChanged()) {
            SendNotification::restoreProduct($ads);
        }
        return 1;
    }

    public function exportExcel()
    {
        $date = Carbon::today();
        return Excel::download(new ProductsExport(), 'products-sheet-' . $date . '.xlsx');
    }
}
