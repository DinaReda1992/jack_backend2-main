<?php

namespace App\Repositories;

use App\Entities\UserType;
use App\Jobs\SendPushNotification;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\Month;
use App\Models\ProductBarcode;
use App\Models\ProductDiscount;
use App\Models\ProductPhotos;
use App\Models\Products;
use App\Models\ProductSalesTarget;
use App\Models\ProductSubCategories;
use App\Models\Settings;
use App\Models\Subcategories;
use App\Models\User;
use App\Repositories\Utils\UtilsRepository;
use Illuminate\Support\Facades\App;
use Lakshmaji\Thumbnail\Facade\Thumbnail;
use Yajra\DataTables\Facades\DataTables;

class ProductRepository
{

    public static function getProductsData($request, $baseUrl)
    {
        $search = [];

        $products = Products::where($search)->orderBy('id', 'DESC');
        if (isset($request->status) && !empty($request->status) && $request->status == 'deleted') {
            $products = $products->onlyTrashed();
        } else {
            $products = $products->withoutTrashed();
        }
        return DataTables::of($products)
            //            ->filterColumn('title', function ($query, $keyword) {
            //                $query->whereRaw("title  like ?", ["%{$keyword}%"]);
            //                $query->orWhereRaw("title_en  like ?", ["%{$keyword}%"]);
            //            })

            ->editColumn('brandName', function ($product) {
                return $product->brand->name;
            })
            ->addColumn('status', function ($product) use ($baseUrl) {
                return '<div class="checkbox checkbox-switchery switchery-sm switchery-double">
									<input type="checkbox" object_id="' . $product->id . '" delete_url="/' . $baseUrl . '/stop_product/' . $product->id . '" class="switchery sweet_switch" ' . ($product->stop == 0 ? 'checked' : '') . ' />
								</div>';
            })
            ->editColumn('photo', function ($product) {
                if ($product->photo) {
                    return '<img alt="" width="50" height="50" src="/uploads/' . $product->photo . '">';
                }
            })
            ->addColumn('actions', function ($product) use ($baseUrl) {
                $ul = '<ul class="icons-list">';
                if ($product->deleted_at !== null) {
                    $ul .= '<li class="text-teal-600"><a onclick="return false;"
																	 object_id="' . $product->id . '"
																	 method="get"
																	 delete_url="/' . $baseUrl . '/product_archived_restore/' . $product->id . '"
																	 class="sweet_warning" method="get" href="#" message="' . __('dashboard.are_you_sure_from_restore') . ' المنتج"><i
														class="fa  fa-refresh"></i> استعادة</a></li>';
                } else {
                    $ul .= '<li class="text-primary-600"><a href="/' . $baseUrl . '/products/' . $product->id . '/edit"><i class="icon-pencil7"></i></a></li>';
                    $ul .= '<li class="text-danger-600"><a onclick="return false;" object_id="' . $product->id
                        . '" delete_url="/' . $baseUrl . '/products/' . $product->id
                        . '"  class="sweet_warning" href="#"><i class="icon-trash"></i></a></li>';
                }
                $ul .= '';
                return $ul;
            })->make(true);
    }

    public static function loadGeneralDataForCreation(&$data, $id = null)
    {
        $data['categories'] = Categories::where([
            'stop' => 0,
            'is_archived' => 0,
            'category_type' => 1
        ])->orderBy('sort', 'asc')->get();
        $data['locales'] = [
            'ar',
            'en'
        ];
        $data['brands'] = Brand::withoutTrashed()->get();

        if ($id !== null) {
            $product = Products::find($id);
            $data['my_subcategories'] = Subcategories::where(['category_id' => $product->category_id])->get();
            $product->subCategories = ProductSubCategories::where([
                'product_id' => $product->id
            ])->pluck('sub_category_id')->toArray();
            $product->barcodes = ProductBarcode::where([
                'product_id' => $product->id
            ])->get();
            $product->discounts = ProductDiscount::withoutTrashed()->where([
                'product_id' => $product->id
            ])->get();
            $data['object'] = $product;
        }
    }

    public static function getCompaniesUsers()
    {
        return User::select('users.id', 'user_data.user_name')
            ->join('user_data', 'user_data.user_id', 'users.id')
            ->where('users.user_type_id', UserType::COMPANY_PROVIDER)
            ->where('user_data.stop', 0)->get();
    }


    public static function deleteDiscountRow($id)
    {
        $discount = ProductDiscount::where('id', $id)->first();
        $discount->delete();
    }

    public static function addProduct($request)
    {
        $product = new Products();
        self::setProductObject($product, $request);
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $ext = $file->getClientOriginalExtension();
            $fileName = 'product-' . time() . '-' . uniqid() . '.' . $ext;
            $path = $file->getRealPath();
            $destinationPath = 'uploads/';
            $imageSetting = Settings::find(73);
            if ($imageSetting && intval($imageSetting->value) === 1) {
                $width = 500;
                $height = 500;
            } else {
                $width = 0;
                $height = 0;
            }
            UtilsRepository::uploadImage($path, $fileName, $destinationPath, $width, $height);
            $product->photo = $fileName;
        }
        $product->save();
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file_) {
                $ext = $file_->getClientOriginalExtension();
                $fileName = 'product1-' . time() . '-' . uniqid() . '.' . $ext;
                $path = $file_->getRealPath();
                $destinationPath = 'uploads/';
                $imageSetting = Settings::find(73);
                if ($imageSetting && intval($imageSetting->value) === 1) {
                    $width = 500;
                    $height = 500;
                } else {
                    $width = 0;
                    $height = 0;
                }
                UtilsRepository::uploadImage($path, $fileName, $destinationPath, $width, $height);
                $object1 = new ProductPhotos();
                $object1->photo = $fileName;
                $object1->product_id = $product->id;
                $object1->save();
            }
        }
        self::handleProductExtraData($product, $request);

        $user = auth()->user();
        if ($user->user_type_id == UserType::COMPANY_SUPERVISOR_PROVIDER) {
            $notification_title = 'إضافة منتج';
            $notification_title_en = 'add product';
            $notification_message = 'قام <' . $user->username . '> بإضافة منتج <' . $product->title . '>';
            $notification_message_en = '<' . $user->username . '> has add product <' . $product->title_en . '>';
            $type = 4;
            SendPushNotification::dispatch(
                $user->id,
                $user->provider->id,
                $notification_title,
                $notification_title_en,
                $notification_message,
                $notification_message_en,
                $type,
                [],
                ''
            );
        }
    }

    protected static function setProductObject(&$product, $request)
    {
        $product->title = $request->title;
        $product->title_en = $request->title_en;
        $product->description = $request->description;
        $product->description_en = $request->description_en;
        $product->brand_id = isset($request->brand_id) && $request->brand_id != 0 ?
            $request->brand_id : null;
        $product->category_id = $request->category_id;
        $product->weight = $request->weight;
        $product->quantity = $request->quantity;
        $product->min_quantity = $request->min_quantity;
        $product->client_price = $request->client_price;
        $product->sku = $request->sku;
        $product->speed = $request->speed;
        $product->ink_type = $request->ink_type;
        $product->measuring_unit = $request->measuring_unit ?: 1;
        $product->has_tax = isset($request->has_tax) && $request->has_tax ? 1 : 0;
        $product->preparation_period = isset($request->preparation_period) && !empty($request->preparation_period) ?
            $request->preparation_period : null;
        $product->length = isset($request->length) && !empty($request->length) ?
            $request->length : null;
        $product->width = isset($request->width) && !empty($request->width) ?
            $request->width : null;
        $product->height = isset($request->height) && !empty($request->height) ?
            $request->height : null;
        $product->uses = isset($request->uses) && !empty($request->uses) ?
            $request->uses : null;
        $product->uses_en = isset($request->uses_en) && !empty($request->uses_en) ?
            $request->uses_en : null;
        $product->features = isset($request->features) && !empty($request->features) ?
            $request->features : null;
        $product->features_en = isset($request->features_en) && !empty($request->features_en) ?
            $request->features_en : null;
        $product->benefits = isset($request->benefits) && !empty($request->benefits) ?
            $request->benefits : null;
        $product->benefits_en = isset($request->benefits_en) && !empty($request->benefits_en) ?
            $request->benefits_en : null;
        $product->how_to_use = isset($request->how_to_use) && !empty($request->how_to_use) ?
            $request->how_to_use : null;
        $product->how_to_use_en = isset($request->how_to_use_en) && !empty($request->how_to_use_en) ?
            $request->how_to_use_en : null;
        $product->meta_description = isset($request->meta_description) && !empty($request->meta_description) ?
            $request->meta_description : null;
        $product->meta_description_en = isset($request->meta_description_en) && !empty($request->meta_description_en) ?
            $request->meta_description_en : null;
        $product->meta_keywords = isset($request->meta_keywords) && !empty($request->meta_keywords) ?
            $request->meta_keywords : null;
        $product->meta_keywords_en = isset($request->meta_keywords_en) && !empty($request->meta_keywords_en) ?
            $request->meta_keywords_en : null;
        $product->meta_title = isset($request->meta_title) && !empty($request->meta_title) ?
            $request->meta_title : null;
        $product->meta_title_en = isset($request->meta_title_en) && !empty($request->meta_title_en) ?
            $request->meta_title_en : null;

        $product->video_type = isset($request->video_type) && !empty($request->video_type) ?
            $request->video_type : null;

        if ($product->video_type == 1) {
            $product->video = isset($request->video_url) && !empty($request->video_url) ?
                $request->video_url : null;
        } else if ($product->video_type == 2) {
            if ($request->hasFile('video')) {
                $file = $request->file('video');
                if (isset($product->video) && $product->video !== null) {
                    $old_file = 'uploads/' . $product->video;
                    if (is_file($old_file)) unlink($old_file);
                }
                $fileName = 'product-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $request->file('video')->move($destinationPath, $fileName);
                $product->video = $fileName;

                $video_path = $destinationPath . '/' . $fileName;
                $thumbnail_path = 'uploads/product_thumb';
                $thumbnail_image = 'thumb-' . time() . '-' . uniqid() . '.png';
                Thumbnail::getThumbnail($video_path, $thumbnail_path, $thumbnail_image, 2);
                $product->thumb = $thumbnail_image;
            }
        }
    }

    protected static function handleProductExtraData($product, $request)
    {
        if ($product) ProductSubCategories::where('product_id', $product->id)->forceDelete();
        foreach ($request->subcategory_id as $subCategoryId) {
            $subCategory = new ProductSubCategories();
            $subCategory->product_id = $product->id;
            $subCategory->sub_category_id = $subCategoryId;
            $subCategory->save();
        }
        if ($product) ProductBarcode::where('product_id', $product->id)->forceDelete();
        if ($request->barcodes) {
            foreach ($request->barcodes as $barcode) {
                $productBarcode = new ProductBarcode();
                $productBarcode->product_id = $product->id;
                $productBarcode->barcode = $barcode;
                $productBarcode->save();
            }
        }
        if ($product) ProductSalesTarget::where('product_id', $product->id)->forceDelete();
        if (isset($request->month_id)) {
            foreach ($request->month_id as $key => $month_id) {
                if (isset($request->{'target_' . $month_id})) {
                    $productSalesTarget = new ProductSalesTarget();
                    $productSalesTarget->product_id = $product->id;
                    $productSalesTarget->month_id = $month_id;
                    $productSalesTarget->target = $request->{'target_' . $month_id};
                    $productSalesTarget->save();
                }
            }
        }

        if (isset($request->discount_quantity)) {
            foreach ($request->discount_quantity as $key => $discount_quantity) {
                if (
                    isset($request->discount_price[$key]) && isset($request->discount_to_quantity[$key]) && isset($request->discount_start_date[$key])
                    && isset($request->discount_end_date[$key])
                ) {
                    $productDiscount = null;
                    if (isset($request->discount_id[$key])) {
                        $productDiscount = ProductDiscount::find($request->discount_id[$key]);
                    }
                    if (!$productDiscount) {
                        $productDiscount = new ProductDiscount();
                    }
                    $productDiscount->product_id = $product->id;
                    $productDiscount->quantity = $discount_quantity;
                    $productDiscount->to_quantity = $request->discount_to_quantity[$key];
                    $productDiscount->price = $request->discount_price[$key];
                    $productDiscount->start_date = $request->discount_start_date[$key];
                    $productDiscount->end_date = $request->discount_end_date[$key];
                    $productDiscount->save();
                }
            }
        }
    }


    public static function editProduct(&$product, $request)
    {
        self::setProductObject($product, $request);
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $old_file = 'uploads/' . $product->photo;
            if (is_file($old_file)) unlink($old_file);
            $fileName = 'product-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->getRealPath();
            $destinationPath = 'uploads/';
            $imageSetting = Settings::find(73);
            if ($imageSetting && intval($imageSetting->value) === 1) {
                $width = 500;
                $height = 500;
            } else {
                $width = 0;
                $height = 0;
            }
            UtilsRepository::uploadImage($path, $fileName, $destinationPath, $width, $height);
            $product->photo = $fileName;
        }
        $product->save();
        if ($request->hasFile('photos')) {
            $files = $request->file('photos');
            foreach ($files as $file_) {
                $ext = $file_->getClientOriginalExtension();
                $fileName = 'product-' . time() . '-' . uniqid() . '.' . $ext;
                $path = $file_->getRealPath();
                $destinationPath = 'uploads/';
                $imageSetting = Settings::find(73);
                if ($imageSetting && intval($imageSetting->value) === 1) {
                    $width = 500;
                    $height = 500;
                } else {
                    $width = 0;
                    $height = 0;
                }
                UtilsRepository::uploadImage($path, $fileName, $destinationPath, $width, $height);
                $object1 = new ProductPhotos();
                $object1->photo = $fileName;
                $object1->product_id = $product->id;
                $object1->save();
            }
        }
        self::handleProductExtraData($product, $request);
    }

    public static function deleteProductPhoto($id)
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

    public static function deleteProduct($id)
    {
        $product = Products::where('id', $id)->first();
        $product->delete();
    }

    public static function stopProduct($id)
    {
        $product = Products::withTrashed()->find($id);
        $product->stop = !$product->stop;;
        $product->save();
    }

    public static function restoreProduct($id)
    {
        $product = Products::withTrashed()->where('id', $id)->restore();
    }

    public static function getOfferType($offer, $tax = 0)
    {
        $lang = App::getLocale();
        $message = '';
        if ($offer->type_id == 1) {
            $price = round($offer->price_discount + $tax, 2);
            $message = ' خصم ';
            $message .= $price;
            $message .=  ' ر.س ';
            if ($lang == 'en') {
                $message = 'Discount ' . $price . ' SAR';
            }
        } elseif ($offer->type_id == 2) {
            $message = ' %' . ' خصم' . ' ' . $offer->percentage;
            if ($lang == 'en') {
                $message = 'Discount ' . $offer->percentage . ' %';
            }
        } elseif ($offer->type_id == 3) {

            if ($offer->is_free == 1) {
                $message = ' احصل علي ';
                $message .= ($offer->quantity + $offer->get_quantity) . ' ';
                $message .= ' بقيمة ';
                $message .= $offer->quantity;
                if ($lang == 'en') {
                    $message = 'Get ' . ($offer->quantity + $offer->get_quantity) . ' for ' . $offer->quantity;
                }
            } else {
                if ($offer->percentage == 100) {
                    $message = ' احصل علي ';
                    $message .= $offer->quantity + $offer->get_quantity . ' ';
                    $message .= ' بقيمة ';
                    $message .= $offer->quantity;
                    if ($lang == 'en') {
                        $message = 'Get ' . ($offer->quantity + $offer->get_quantity) . ' for ' . $offer->quantity;
                    }
                } else {
                    if ($offer->quantity == 1) {
                        $message = $offer->percentage . ' %';
                        $message .= ' علي الحبة الثانية ';
                        if ($lang == 'en') {
                            $message = $offer->percentage . ' % on 2nd';
                        }
                    } else {
                        $message = ' عرض خاص ';
                        if ($lang == 'en') {
                            $message = 'Special offer';
                        }
                    }
                }
            }
        } elseif ($offer->type_id == 4) {
            $message = 'هدية بعد الحبة ';
            $message .= $offer->quantity;
            if ($lang == 'en') {
                $message = 'Buy ' . $offer->quantity . ' + Gift';
            }
        } elseif ($offer->type_id == 5) {
            $message = 'شحن مجاني ';
            if ($lang == 'en') {
                $message = 'Free shipping';
            }
        }
        return $message;
    }

    public static function getOfferTypeWithTax($offer, $offer_price)
    {
        $lang = App::getLocale();
        $message = '';
        if ($offer->type_id == 1) {
            $price = $offer_price;
            $message = ' خصم ';
            $message .= $price;
            $message .= '  ر.س ';
            if ($lang == 'en') {
                $message = 'Discount ' . $price . ' SAR';
            }
        } elseif ($offer->type_id == 2) {
            $message = ' %' . ' وفر' . ' ' . $offer->percentage;
            if ($lang == 'en') {
                $message = 'Discount ' . $offer->percentage . ' %';
            }
        } elseif ($offer->type_id == 3) {

            if ($offer->is_free == 1) {
                $message = ' احصل علي ';
                $message .= ($offer->quantity + $offer->get_quantity) . ' ';
                $message .= ' بقيمة ';
                $message .= $offer->quantity;
                if ($lang == 'en') {
                    $message = 'Get ' . ($offer->quantity + $offer->get_quantity) . ' for ' . $offer->quantity;
                }
            } else {
                if ($offer->percentage == 100) {
                    $message = ' احصل علي ';
                    $message .= $offer->quantity + $offer->get_quantity . ' ';
                    $message .= ' بقيمة ';
                    $message .= $offer->quantity;
                    if ($lang == 'en') {
                        $message = 'Get ' . ($offer->quantity + $offer->get_quantity) . ' for ' . $offer->quantity;
                    }
                } else {
                    if ($offer->quantity == 1) {
                        $message = $offer->percentage . ' %';
                        $message .= ' علي الحبة الثانية ';
                        if ($lang == 'en') {
                            $message = $offer->percentage . ' % on 2nd';
                        }
                    } else {
                        $message = ' عرض خاص ';
                        if ($lang == 'en') {
                            $message = 'Special offer';
                        }
                    }
                }
            }
        } elseif ($offer->type_id == 4) {
            $message = 'هدية بعد الحبة ';
            $message .= $offer->quantity;
            if ($lang == 'en') {
                $message = 'Buy ' . $offer->quantity . ' + Gift';
            }
        } elseif ($offer->type_id == 5) {
            $message = 'شحن مجاني ';
            if ($lang == 'en') {
                $message = 'Free shipping';
            }
        }
        return $message;
    }
}
