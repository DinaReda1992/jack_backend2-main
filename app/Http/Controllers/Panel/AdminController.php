<?php

namespace App\Http\Controllers\Panel;

use Carbon\Carbon;
use App\Models\User;
use App\Http\Requests;
use App\Models\Models;
use App\Models\Orders;
use App\Models\MakeYear;
use App\Models\Products;
use App\Models\Settings;
use App\Models\DamagePhoto;
use App\Models\SiteContent;
use App\Models\SiteFeature;
use App\Models\PricingOrder;
use Illuminate\Http\Request;
use App\Models\ProductPhotos;
use App\Models\Purchase_order;
use App\Models\MeasurementUnit;
use App\Models\SiteScreenshots;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use MFrouh\Sms4jawaly\Facades\Sms4jawaly;
use App\Models\MeasurementUnitsCategories;

class AdminController extends Controller
{



    public function getIndex()
    {
        $client = User::selectRaw('(SELECT count(*) FROM orders ) as all_orders')
            // ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.status=1) as new_orders')
            // ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.status=2	) as preparing_orders')
            // ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.status=3	) as prepared_orders')
            // ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.status=4	) as shipping_orders')
            // ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.status=6	) as delivering_orders')
            // ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.status=7	) as completed_orders')
            // ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.status=5	) as canceled_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)	) as last_all_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE   orders.status=1 and orders.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as last_new_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=2 and orders.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as last_preparing_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=3 and orders.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as last_prepared_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=4 and orders.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as last_shipping_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=6 and orders.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as last_delivering_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and orders.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as last_completed_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=5 and orders.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as last_canceled_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=1	) as january_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=2	) as february_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=3	) as march_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=4	) as april_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=5	) as may_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=6	) as june_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=7	) as july_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=8	) as august_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=9	) as september_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=10	) as october_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=11	) as november_orders')
            ->selectRaw('(SELECT count(*) FROM orders WHERE orders.financial_date IS NOT NULL and orders.status=7 and MONTH(orders.created_at)=12	) as december_orders')
            ->first();


        $last_orders = Orders::select('orders.*')->where('payment_method', '!=', 0)
            ->with('paymentMethod', 'orderStatus', 'user')
            ->orderBy('created_at', 'desc')
            ->where('created_at', '>', Carbon::now()->subDays(1))
            ->take(10)
            ->get();

        $statistic_in_day = Orders::where('financial_date', '!=', null)->statisticInDay();

        $warehouse_products = Products::whereRaw('quantity - min_quantity < min_warehouse_quantity')
        ->select('id','quantity','min_warehouse_quantity','title')
        ->selectRaw('quantity - min_warehouse_quantity as new_quantity')
        ->where('is_archived', 0)
        ->where('stop', 0)
        ->orderBy('new_quantity','asc')
        ->take(4)->get();

        return view('admin.index', compact('client', 'last_orders','statistic_in_day','warehouse_products'));
    }

    
    public function getCategoryMeasurement($id = 0)
    {
        $objects = MeasurementUnit::whereIn('id', function ($query) use ($id) {
            $query->select('measurement_id')
                ->from(with(new MeasurementUnitsCategories())->getTable())
                ->where('category_id', $id);
        })->get();
        echo "<option value=''>اختر وحدة القياس</option>";
        foreach ($objects as $object) {
            echo "<option value='" . $object->id . "'>" .  $object->name . "</option>";
        }
    }

    public function updateSiteManagement(Request $request)
    {
        $this->check_settings(487);

        $screenshots = SiteScreenshots::all();
        $object = SiteContent::first();
        $features = SiteFeature::all();
        return view('admin.site_manager.add', ['screenshots' => $screenshots, 'object' => $object, 'features' => $features]);
    }
    public function updateSiteContent(Request $request)
    {
        $this->check_settings(487);

        $content = SiteContent::first();

        $content->about_text = $request->about_text ?: '';
        $content->app_video = $request->app_video ?: '';

        $top_photo = $request->file('top_photo');
        if ($request->hasFile('top_photo')) {
            $fileNameTop = 'top_photo-' . time() . '-' . uniqid() . '.' . $top_photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('top_photo')->move($destinationPath, $fileNameTop);
            $content->top_photo = $fileNameTop;
        }

        $about_photo = $request->file('about_photo');
        if ($request->hasFile('about_photo')) {
            $fileNameAbout = 'about_photo-' . time() . '-' . uniqid() . '.' . $about_photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('about_photo')->move($destinationPath, $fileNameAbout);
            $content->about_photo = $fileNameAbout;
        }
        $features_photo = $request->file('features_photo');
        if ($request->hasFile('features_photo')) {
            $fileNameFeature = 'features_photo-' . time() . '-' . uniqid() . '.' . $features_photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('features_photo')->move($destinationPath, $fileNameFeature);
            $content->features_photo = $fileNameFeature;
        }

        $footer_photo = $request->file('footer_photo');
        if ($request->hasFile('footer_photo')) {
            $fileNameFooter = 'footer_photo-' . time() . '-' . uniqid() . '.' . $footer_photo->getClientOriginalExtension();
            $destinationPath = 'uploads';
            $request->file('footer_photo')->move($destinationPath, $fileNameFooter);
            $content->footer_photo = $fileNameFooter;
        }
        $content->save();

        if ($request->hasFile('screenshots')) {
            $files = $request->file('screenshots');
            foreach ($files as $file_) {
                $fileName = 'screenshot-' . time() . '-' . uniqid() . '.' . $file_->getClientOriginalExtension();
                $destinationPath = 'uploads';
                $file_->move($destinationPath, $fileName);
                $object1 = new SiteScreenshots();
                $object1->photo = $fileName;
                $object1->save();
            }
        }
        return redirect()->back()->with('success', 'تم تعديل المحتوى');
    }
    public function deleteScreenshotPhoto($id)
    {
        $this->check_settings(487);

        $photo = SiteScreenshots::find($id);
        if (!$photo) {
            return redirect()->back()->with('error', 'لا توجد هذه الصورة');
        }
        $old_file = 'uploads/' . $photo->photo;
        if (is_file($old_file))    unlink($old_file);
        $photo->delete();
        return redirect()->back()->with('success', 'تم حذف الصورة');
    }
    public function appFeatures(Request $request)
    {
        $this->check_settings(487);

        $features = SiteFeature::all();
        return view('admin.site_manager.update_features', ['features' => $features]);
    }
    public function updateSiteFeatures(Request $request)
    {
        if ($request->feature_title) {
            foreach ($request->feature_id as $key => $value) {
                if (!empty($value)) {
                    $feature = SiteFeature::find($value);
                    if ($feature) {
                        $feature->title = $request->feature_title[$key];
                        $feature->description = @$request->feature_description[$key] ?: '';
                        $feature->icon = @$request->icon[$key] ?: '';
                        $features_photo = $request->file('feature_photo' . $value);
                        if ($features_photo) {
                            $fileNameFeature = 'features_photo-' . time() . '-' . uniqid() . '.' . $features_photo->getClientOriginalExtension();
                            $destinationPath = 'uploads';
                            $features_photo->move($destinationPath, $fileNameFeature);
                            $feature->photo = $fileNameFeature;
                        }
                        $feature->save();
                    }
                }
            }
        }






        return redirect()->back()->with('success', 'تم تعديل المحتوى');
    }
    public function clearData()
    {
        $objects = MakeYear::where('is_archived', 1)->get();
        foreach ($objects as $object) {
            $count = 0;
            $models = Models::where('makeyear_id', $object->id)->get();
            foreach ($models as $model) {
                $products = Products::where('model_id', $model->id)->get();
                //           foreach ($products as $product){
                //               $old_file = 'uploads/'.$product->photo;
                //               if(is_file($old_file))	unlink($old_file);
                //               $product->delete();
                //           }
                if ($products->count() == 0) {

                    $model->delete();
                } else {
                    $count++;
                }
            }
            if ($count == 0) {
                $object->delete();
            }
        }

        $models = Models::where('is_archived', 1)->get();
        foreach ($models as $model) {
            $products = Products::where('model_id', $model->id)->get();

            //           foreach ($products as $product){
            //               $old_file = 'uploads/'.$product->photo;
            //               if(is_file($old_file))	unlink($old_file);
            //               $photos=ProductPhotos::where('product_id',$product->id)->get();
            //               foreach ($photos as $photo){
            //                   $old_file = 'uploads/'.$photo->photo;
            //                   if(is_file($old_file))	unlink($old_file);
            //                   $photo->delete();
            //
            //               }
            //               $product->delete();
            //           }
            if ($products->count() == 0) {
                $model->delete();
            }
        }
    }
    const SUCCESS_CODES = [100];
    const CREDIT_SUCCESS_CODES = [117];

    public function getBalance()
    {
        return  Sms4jawaly::getBalance();
    }
}
