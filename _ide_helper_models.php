<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\ActivationCodes
 *
 * @property int $id
 * @property int $user_id
 * @property int $phonecode
 * @property int $activation_code
 * @property string $phone
 * @property int $activate
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes query()
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes whereActivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes wherePhonecode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ActivationCodes whereUserId($value)
 */
	class ActivationCodes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Addresses
 *
 * @property int $id
 * @property string $address
 * @property string|null $details
 * @property int $user_id
 * @property int $is_home
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int|null $region_id
 * @property int|null $state_id
 * @property string|null $phone1
 * @property string|null $phone2
 * @property string|null $email
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read mixed $lat
 * @property-read mixed $lng
 * @property-read \App\Models\Regions|null $region
 * @property-read \App\Models\States|null $state
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses query()
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereIsHome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses wherePhone1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses wherePhone2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Addresses whereUserId($value)
 */
	class Addresses extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Ads
 *
 * @property-read \App\Models\States|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comments[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\AdsPhotos|null $mainPhoto
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdsOptions[] $options
 * @property-read int|null $options_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Ads newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ads newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ads query()
 */
	class Ads extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdsNotify
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdsNotify newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdsNotify newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdsNotify query()
 */
	class AdsNotify extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdsOptions
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdsOptions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdsOptions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdsOptions query()
 */
	class AdsOptions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdsOrders
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdsOrders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdsOrders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdsOrders query()
 */
	class AdsOrders extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AdsPhotos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdsPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdsPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdsPhotos query()
 */
	class AdsPhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Advantages
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Advantages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Advantages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Advantages query()
 */
	class Advantages extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Answers
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Answers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Answers query()
 */
	class Answers extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ApprovedProjects
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedProjects newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedProjects newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ApprovedProjects query()
 */
	class ApprovedProjects extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ArticleComments
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleComments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleComments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleComments query()
 */
	class ArticleComments extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ArticlePhotos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ArticlePhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticlePhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticlePhotos query()
 */
	class ArticlePhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ArticleReports
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleReports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleReports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleReports query()
 */
	class ArticleReports extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Articles
 *
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Articles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Articles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Articles query()
 */
	class Articles extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AutoPart
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property int $category_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart query()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AutoPart whereUpdatedAt($value)
 */
	class AutoPart extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Balance
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property float $price
 * @property float $site_profits
 * @property int $status
 * @property int $balance_type_id
 * @property string $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $method_name
 * @property-read \App\Models\Reservations|null $reservation
 * @method static \Illuminate\Database\Eloquent\Builder|Balance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Balance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Balance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereBalanceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereMethodName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereSiteProfits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Balance whereUserId($value)
 */
	class Balance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BalanceTypes
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BalanceTypes whereUpdatedAt($value)
 */
	class BalanceTypes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BankAccounts
 *
 * @property int $id
 * @property string $bank_name
 * @property string $bank_name_en
 * @property string $account_name
 * @property string $account_number
 * @property string $account_ipan
 * @property string $photo
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereAccountIpan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereBankNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccounts whereUserId($value)
 */
	class BankAccounts extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BankTransfer
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $order_id
 * @property int|null $transaction_id
 * @property string|null $photo
 * @property int|null $bank_id
 * @property int|null $from_bank_id
 * @property string|null $money_transfered
 * @property string|null $account_name
 * @property string|null $account_number
 * @property string|null $reference_number
 * @property string|null $bank_name
 * @property int|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankAccounts|null $to_bank
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereAccountName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereFromBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereMoneyTransfered($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BankTransfer whereUserId($value)
 */
	class BankTransfer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Banks
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Banks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banks query()
 * @method static \Illuminate\Database\Eloquent\Builder|Banks whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banks whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banks whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banks whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Banks whereUpdatedAt($value)
 */
	class Banks extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Banners
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Banners newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banners newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Banners query()
 */
	class Banners extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Blocks
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Blocks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blocks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Blocks query()
 */
	class Blocks extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BlogCategories
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogCategories query()
 */
	class BlogCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\BlogSubcategories
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BlogSubcategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogSubcategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BlogSubcategories query()
 */
	class BlogSubcategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Branches
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Branches newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branches newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Branches query()
 */
	class Branches extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CancellationTypes
 *
 * @property int $id
 * @property int $percent
 * @property string $description
 * @property string $description_en
 * @property int $days
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CancellationTypes whereUpdatedAt($value)
 */
	class CancellationTypes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CarTrips
 *
 * @property-read \App\Models\States|null $fromCity
 * @property-read \App\Models\States|null $toCity
 * @method static \Illuminate\Database\Eloquent\Builder|CarTrips newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CarTrips newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CarTrips query()
 */
	class CarTrips extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Cards
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Cards newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cards newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cards query()
 */
	class Cards extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CardsCategories
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CardsCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardsCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CardsCategories query()
 */
	class CardsCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Cars
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Cars newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cars newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cars query()
 */
	class Cars extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CarsModels
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CarsModels newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CarsModels newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CarsModels query()
 */
	class CarsModels extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CartItem
 *
 * @property int $id
 * @property int $user_id
 * @property int $shop_id
 * @property int $item_id
 * @property float $price
 * @property int $quantity
 * @property int $quantity_difference
 * @property int $type 1-> shop   2-> pricing
 * @property int $order_id
 * @property int $shipment_id
 * @property int $status status = 1 قيد التجهيز status = 2 تم استلامها من المندوب status = 3 تم شحنها status = 4 تم التوصيل status = 5 تم الالغاء
 * @property int $calculated
 * @property string $cancel_reason
 * @property int|null $provider_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|CartItem[] $cart
 * @property-read int|null $cart_count
 * @property-read \Illuminate\Database\Eloquent\Collection|CartItem[] $cart_items
 * @property-read int|null $cart_items_count
 * @property-read \App\Models\PricingOffer|null $itemProduct
 * @property-read \App\Models\Orders|null $order
 * @property-read \App\Models\Products|null $product
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCalculated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCancelReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereQuantityDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CartItem whereUserId($value)
 */
	class CartItem extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Categories
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $photo
 * @property int $sort
 * @property int $parent_id
 * @property int $stop
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CategoriesSelections[] $selections
 * @property-read int|null $selections_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subcategories[] $subCategories
 * @property-read int|null $sub_categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Categories[] $subs
 * @property-read int|null $subs_count
 * @method static \Illuminate\Database\Eloquent\Builder|Categories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories query()
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Categories whereUpdatedAt($value)
 */
	class Categories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CategoriesSelections
 *
 * @property-read \App\Models\Selections|null $selection
 * @method static \Illuminate\Database\Eloquent\Builder|CategoriesSelections newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoriesSelections newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CategoriesSelections query()
 */
	class CategoriesSelections extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Cities
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Cities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cities query()
 */
	class Cities extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ClientTypes
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTypes whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientTypes whereUpdatedAt($value)
 */
	class ClientTypes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Cobons
 *
 * @property int $id
 * @property string $code
 * @property int $used
 * @property int $order_id
 * @property int $percent
 * @property float $max_money
 * @property int $days
 * @property int $usage_quota
 * @property int $user_id
 * @property int $type
 * @property string $markter_name
 * @property string|null $link_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CobonsCategories[] $cobonCategory
 * @property-read int|null $cobon_category_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CobonsProviders[] $cobonProvider
 * @property-read int|null $cobon_provider_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereMarkterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereMaxMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons wherePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereUsageQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cobons whereUserId($value)
 */
	class Cobons extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CobonsCategories
 *
 * @property int $id
 * @property int $category_id
 * @property int $cobon_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsCategories whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsCategories whereCobonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsCategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsCategories whereUpdatedAt($value)
 */
	class CobonsCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CobonsProviders
 *
 * @property int $id
 * @property int $user_id
 * @property int $cobon_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsProviders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsProviders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsProviders query()
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsProviders whereCobonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsProviders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsProviders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsProviders whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CobonsProviders whereUserId($value)
 */
	class CobonsProviders extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Comments
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Comments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comments query()
 */
	class Comments extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CommentsFollows
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsFollows newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsFollows newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsFollows query()
 */
	class CommentsFollows extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CommentsNotify
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsNotify newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsNotify newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CommentsNotify query()
 */
	class CommentsNotify extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Companies
 *
 * @property-read \App\Models\States|null $city
 * @method static \Illuminate\Database\Eloquent\Builder|Companies newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Companies newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Companies query()
 */
	class Companies extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ContactTypes
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ContactTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactTypes whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactTypes whereUpdatedAt($value)
 */
	class ContactTypes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Contacts
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $subject
 * @property int $user_id
 * @property string $message
 * @property int $status
 * @property int $complain
 * @property int $order_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereComplain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contacts whereUserId($value)
 */
	class Contacts extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Content
 *
 * @property int $id
 * @property string|null $page_name
 * @property string $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string $meta_title_en
 * @property string $meta_description_en
 * @property string $meta_keywords_en
 * @property string $content
 * @property string $page_name_en
 * @property string $content_en
 * @property int $type
 * @property string $pdf
 * @property string $updates
 * @property int $views
 * @property string $updated_at
 * @property int $hidden
 * @property string $photo
 * @property string $photo2
 * @property string $cover
 * @property string $icon
 * @method static \Illuminate\Database\Eloquent\Builder|Content newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Content newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Content query()
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereContentEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaKeywords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaKeywordsEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereMetaTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content wherePageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content wherePageNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content wherePdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content wherePhoto2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereUpdates($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Content whereViews($value)
 */
	class Content extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Countries
 *
 * @property int $id
 * @property string|null $name_en
 * @property string $name
 * @property string|null $photo
 * @property int|null $phonecode
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Countries newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries query()
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries wherePhonecode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Countries whereUpdatedAt($value)
 */
	class Countries extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CreditCards
 *
 * @property int $id
 * @property int $user_id
 * @property string $pt_customer_email
 * @property string $pt_customer_password
 * @property string $pt_token
 * @method static \Illuminate\Database\Eloquent\Builder|CreditCards newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditCards newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditCards query()
 * @method static \Illuminate\Database\Eloquent\Builder|CreditCards whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditCards wherePtCustomerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditCards wherePtCustomerPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditCards wherePtToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CreditCards whereUserId($value)
 */
	class CreditCards extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Currencies
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $code
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies query()
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currencies whereUpdatedAt($value)
 */
	class Currencies extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DamageEstimate
 *
 * @property int $id
 * @property int $address_id
 * @property int $service_id
 * @property string $description
 * @property int $user_id
 * @property int $status 0 opened 1 accept offer 2 completed  3 canceled   4 closed
 * @property int $payment_method
 * @property int $shop_id
 * @property int $published
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\UserCar|null $car
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DamageOffer[] $offers
 * @property-read int|null $offers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DamagePhoto[] $photos
 * @property-read int|null $photos_count
 * @property-read \App\Models\ServicesCategories|null $service
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate query()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageEstimate whereUserId($value)
 */
	class DamageEstimate extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DamageOffer
 *
 * @property int $id
 * @property int $order_id
 * @property int $provider_id
 * @property int $cost_from
 * @property int $cost_to
 * @property string $time
 * @property string $description
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\DamageEstimate|null $order
 * @property-read \App\Models\User|null $shop
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereCostFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereCostTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamageOffer whereUpdatedAt($value)
 */
	class DamageOffer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DamagePhoto
 *
 * @property int $id
 * @property int $damage_id
 * @property string $photo
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\DamageEstimate|null $damage
 * @method static \Illuminate\Database\Eloquent\Builder|DamagePhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DamagePhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DamagePhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder|DamagePhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamagePhoto whereDamageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamagePhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamagePhoto wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DamagePhoto whereUpdatedAt($value)
 */
	class DamagePhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Days
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $short_name
 * @method static \Illuminate\Database\Eloquent\Builder|Days newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Days newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Days query()
 * @method static \Illuminate\Database\Eloquent\Builder|Days whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Days whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Days whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Days whereShortName($value)
 */
	class Days extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DeliverStatus
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverStatus whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverStatus whereUpdatedAt($value)
 */
	class DeliverStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DeliveryAddress
 *
 * @property int $id
 * @property int $user_id
 * @property string $fullname
 * @property int $state_id
 * @property string $street
 * @property string $building
 * @property string $floor
 * @property int $house_no
 * @property string $near_place
 * @property int $phone
 * @property int $phone2
 * @property string $note
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\States|null $state
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereBuilding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereHouseNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereNearPlace($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress wherePhone2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryAddress whereUserId($value)
 */
	class DeliveryAddress extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DeliveryTimes
 *
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTimes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTimes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliveryTimes query()
 */
	class DeliveryTimes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DeviceMake
 *
 * @property int $id
 * @property int $device_token
 * @property int $make_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceMake newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceMake newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceMake query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceMake whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceMake whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceMake whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceMake whereMakeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceMake whereUpdatedAt($value)
 */
	class DeviceMake extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\DeviceTokens
 *
 * @property int $id
 * @property int $user_id
 * @property string $device_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceTokens newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceTokens newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceTokens query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceTokens whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceTokens whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceTokens whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceTokens whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceTokens whereUserId($value)
 */
	class DeviceTokens extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExtraCategories
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $name_en
 * @property string $photo
 * @property int $sort
 * @property int $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ExtraItems[] $extra_items
 * @property-read int|null $extra_items_count
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraCategories whereUserId($value)
 */
	class ExtraCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExtraItems
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property float $price
 * @property int $limit
 * @property int $user_id
 * @property int $extra_category_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems whereExtraCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExtraItems whereUserId($value)
 */
	class ExtraItems extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Faqs
 *
 * @property int $id
 * @property string $question
 * @property string $question_en
 * @property string $answer
 * @property string $answer_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs query()
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereAnswerEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereQuestionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereUpdatedAt($value)
 */
	class Faqs extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Favorite
 *
 * @property int $id
 * @property int $user_id
 * @property int $item_id
 * @property int $type 0 product --- 1 user
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $shop
 * @property-read int|null $shop_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Favorite whereUserId($value)
 */
	class Favorite extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Feature
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property int $is_one
 * @property float $min_price
 * @property float $max_price
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereIsOne($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereMaxPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereMinPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Feature whereUpdatedAt($value)
 */
	class Feature extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Flights
 *
 * @property-read \App\Models\States|null $fromCity
 * @property-read \App\Models\States|null $toCity
 * @method static \Illuminate\Database\Eloquent\Builder|Flights newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Flights newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Flights query()
 */
	class Flights extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\FollowCar
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FollowCar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowCar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FollowCar query()
 */
	class FollowCar extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Follows
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Follows newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Follows newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Follows query()
 */
	class Follows extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Groups
 *
 * @property int $id
 * @property string $name
 * @property int $is_provider
 * @property int $provider_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Privileges[] $privileges
 * @property-read int|null $privileges_count
 * @method static \Illuminate\Database\Eloquent\Builder|Groups newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Groups newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Groups query()
 * @method static \Illuminate\Database\Eloquent\Builder|Groups whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groups whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groups whereIsProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groups whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groups whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Groups whereUpdatedAt($value)
 */
	class Groups extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Hall
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $address
 * @property string $address_en
 * @property string $longitude
 * @property string $latitude
 * @property int $state_id
 * @property float $price_per_hour
 * @property string $currency
 * @property int $capacity
 * @property int $chairs
 * @property string $terms
 * @property string $policy
 * @property string $title_en
 * @property string $description_en
 * @property string $terms_en
 * @property string $policy_en
 * @property int $user_id
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Feature[] $features
 * @property-read int|null $features_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Categories[] $hallTypes
 * @property-read int|null $hall_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HallFeature[] $hallfeatures
 * @property-read int|null $hallfeatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Feature[] $hallsFeatures
 * @property-read int|null $halls_features_count
 * @property-read \App\Models\HallPhoto|null $onePhoto
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HallPhoto[] $photos
 * @property-read int|null $photos_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HallPhoto[] $photos3
 * @property-read int|null $photos3_count
 * @property-read \App\Models\User $provider
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rating[] $ratings
 * @property-read int|null $ratings_count
 * @method static \Illuminate\Database\Eloquent\Builder|Hall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Hall query()
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereAddressEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereChairs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall wherePolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall wherePolicyEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall wherePricePerHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereTermsEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Hall whereUserId($value)
 */
	class Hall extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\HallCategory
 *
 * @method static \Illuminate\Database\Eloquent\Builder|HallCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HallCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HallCategory query()
 */
	class HallCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\HallFeature
 *
 * @property int $id
 * @property int $hall_id
 * @property int $feature_id
 * @property float $price
 * @property string $description
 * @property string $description_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Feature|null $feature
 * @property-read \App\Models\Hall|null $hall
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature whereHallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallFeature whereUpdatedAt($value)
 */
	class HallFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\HallPhoto
 *
 * @property int $id
 * @property int $hall_id
 * @property string $photo
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Hall|null $hall
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto whereHallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HallPhoto whereUpdatedAt($value)
 */
	class HallPhoto extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Illustrations
 *
 * @property int $id
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $description_en
 * @property int $sort
 * @property string $photo
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations query()
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Illustrations whereUpdatedAt($value)
 */
	class Illustrations extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InvoiceDetails
 *
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetails query()
 */
	class InvoiceDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Invoices
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Invoices newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoices newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoices query()
 */
	class Invoices extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ItemType
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemType whereUpdatedAt($value)
 */
	class ItemType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\JoinUs
 *
 * @method static \Illuminate\Database\Eloquent\Builder|JoinUs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JoinUs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|JoinUs query()
 */
	class JoinUs extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Likes
 *
 * @property int $id
 * @property int $hall_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Likes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Likes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Likes query()
 * @method static \Illuminate\Database\Eloquent\Builder|Likes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Likes whereHallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Likes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Likes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Likes whereUserId($value)
 */
	class Likes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MainCategories
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $photo
 * @property int $sort
 * @property int $stop
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Categories[] $subCategories
 * @property-read int|null $sub_categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainCategories whereUpdatedAt($value)
 */
	class MainCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MainSupplier
 *
 * @property int $id
 * @property string $name
 * @property int $stop
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $suppliers
 * @property-read int|null $suppliers_count
 * @method static \Illuminate\Database\Eloquent\Builder|MainSupplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MainSupplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MainSupplier query()
 * @method static \Illuminate\Database\Eloquent\Builder|MainSupplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainSupplier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainSupplier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainSupplier whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MainSupplier whereUpdatedAt($value)
 */
	class MainSupplier extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Main_menus
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Main_menus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Main_menus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Main_menus query()
 */
	class Main_menus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Main_slider
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sliders[] $Sliders
 * @property-read int|null $sliders_count
 * @method static \Illuminate\Database\Eloquent\Builder|Main_slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Main_slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Main_slider query()
 */
	class Main_slider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Make
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $image
 * @property int $stop
 * @property int $is_special_order
 * @property int $sort
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MakeYear[] $years
 * @property-read int|null $years_count
 * @method static \Illuminate\Database\Eloquent\Builder|Make newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Make newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Make query()
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereIsSpecialOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Make whereUpdatedAt($value)
 */
	class Make extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MakeYear
 *
 * @property int $id
 * @property int $year
 * @property int|null $make_id
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Make|null $make
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Models[] $models
 * @property-read int|null $models_count
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear query()
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear whereMakeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MakeYear whereYear($value)
 */
	class MakeYear extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MandoobPayments
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MandoobPayments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MandoobPayments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MandoobPayments query()
 */
	class MandoobPayments extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ManufactureType
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ManufactureType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufactureType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufactureType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ManufactureType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManufactureType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManufactureType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManufactureType whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ManufactureType whereUpdatedAt($value)
 */
	class ManufactureType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Marchant
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Marchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Marchant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Marchant query()
 */
	class Marchant extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MealMenu
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|MealMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealMenu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|MealMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealMenu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealMenu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealMenu whereUserId($value)
 */
	class MealMenu extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MealSize
 *
 * @property int $id
 * @property int $meal_id
 * @property string $title
 * @property int $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Products|null $meal
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize query()
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize whereMealId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MealSize whereUpdatedAt($value)
 */
	class MealSize extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MeasurementUnit
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnit whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnit whereUpdatedAt($value)
 */
	class MeasurementUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MeasurementUnitsCategories
 *
 * @property int $id
 * @property int $category_id
 * @property int $measurement_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnitsCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnitsCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnitsCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnitsCategories whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnitsCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnitsCategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnitsCategories whereMeasurementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MeasurementUnitsCategories whereUpdatedAt($value)
 */
	class MeasurementUnitsCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MembershipBenefits
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipBenefits newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipBenefits newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MembershipBenefits query()
 */
	class MembershipBenefits extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Menus
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Menus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menus query()
 */
	class Menus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Messages
 *
 * @property int $id
 * @property int $sender_id
 * @property int $reciever_id
 * @property int $status
 * @property string $message
 * @property int $ticket_id
 * @property int $type
 * @property string $photo
 * @property int $is_main
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $schedule
 * @method static \Illuminate\Database\Eloquent\Builder|Messages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Messages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Messages query()
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereIsMain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereRecieverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Messages whereUpdatedAt($value)
 */
	class Messages extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MessagesNotifications
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MessagesNotifications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessagesNotifications newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessagesNotifications query()
 */
	class MessagesNotifications extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MiddleSection
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $btn_text
 * @property string $title_en
 * @property string $description_en
 * @property string $btn_text_en
 * @property string $btn_link
 * @property string $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection query()
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereBtnLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereBtnText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereBtnTextEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MiddleSection whereUpdatedAt($value)
 */
	class MiddleSection extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Models
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property int|null $makeyear_id
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\MakeYear|null $year
 * @method static \Illuminate\Database\Eloquent\Builder|Models newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Models newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Models query()
 * @method static \Illuminate\Database\Eloquent\Builder|Models whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Models whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Models whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Models whereMakeyearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Models whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Models whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Models whereUpdatedAt($value)
 */
	class Models extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MrmandoobCards
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MrmandoobCards newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MrmandoobCards newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MrmandoobCards query()
 */
	class MrmandoobCards extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\MrmandoobCardsDetails
 *
 * @method static \Illuminate\Database\Eloquent\Builder|MrmandoobCardsDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MrmandoobCardsDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MrmandoobCardsDetails query()
 */
	class MrmandoobCardsDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Museums
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Museums newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Museums newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Museums query()
 */
	class Museums extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\NewCategories
 *
 * @method static \Illuminate\Database\Eloquent\Builder|NewCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewCategories query()
 */
	class NewCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Notification
 *
 * @property int $id
 * @property int $sender_id
 * @property int $reciever_id
 * @property string $url
 * @property int $type
 * @property string $message
 * @property string $message_en
 * @property int $order_id
 * @property int $message_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Purchase_order|null $PurchaseOrder
 * @property-read \App\Models\Orders|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereMessageEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereRecieverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUrl($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\NotificationTypes
 *
 * @property int $id
 * @property string $name
 * @property string $icon
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTypes whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationTypes whereUpdatedAt($value)
 */
	class NotificationTypes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OffersCategories
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OffersCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersCategories query()
 */
	class OffersCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OffersCategoriesRestaurants
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OffersCategoriesRestaurants newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersCategoriesRestaurants newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersCategoriesRestaurants query()
 */
	class OffersCategoriesRestaurants extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OffersImages
 *
 * @property int $id
 * @property int $user_id
 * @property int $restaurant_id
 * @property string $photo
 * @property int $hidden
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages query()
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OffersImages whereUserId($value)
 */
	class OffersImages extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderOffers
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOffers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOffers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderOffers query()
 */
	class OrderOffers extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderPhotos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderPhotos query()
 */
	class OrderPhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderShipments
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property int $shop_id
 * @property string $delivery_date
 * @property string $delivery_date_en
 * @property float $delivery_price
 * @property float $taxes
 * @property int $status status = 1 قيد التجهيز status = 2 تم استلامها من المندوب status = 3 تم شحنها status = 4 تم التوصيل status = 5 تم الالغاء
 * @property string $shipment_no
 * @property int $shipment_company
 * @property string $shipment_attach
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CartItem[] $cart_items
 * @property-read int|null $cart_items_count
 * @property-read \App\Models\OrdersStatus|null $orderStatus
 * @property-read \App\Models\User|null $shop
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereDeliveryDateEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereDeliveryPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereShipmentAttach($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereShipmentCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereShipmentNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderShipments whereUserId($value)
 */
	class OrderShipments extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrderTypes
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderTypes query()
 */
	class OrderTypes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Orders
 *
 * @property int $id
 * @property float $final_price
 * @property float $paid_price
 * @property float $delivery_price
 * @property float $order_price
 * @property int $user_id
 * @property int $address_id
 * @property int $payment_method
 * @property int|null $cart_payment_method
 * @property int $status
 * @property string $cobon
 * @property float $taxes
 * @property float $cobon_discount
 * @property string|null $longitude
 * @property string|null $latitude
 * @property string|null $address_name
 * @property string|null $address_desc
 * @property int|null $country_id
 * @property int|null $region_id
 * @property int|null $state_id
 * @property string|null $token
 * @property string|null $short_code
 * @property int|null $accepted_by
 * @property int|null $added_by
 * @property int|null $reviewd_by
 * @property int|null $driver_id
 * @property int|null $provider_id
 * @property int|null $warehouse_id
 * @property int $sent_sms
 * @property string|null $marketed_date
 * @property string|null $financial_date
 * @property string|null $warehouse_date
 * @property string|null $platform
 * @property string|null $reference_id
 * @property string|null $trackId
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $with_balance
 * @property string|null $scheduling_date
 * @property int $is_schedul
 * @property int $remining_price
 * @property int $is_edit
 * @property \Illuminate\Support\Carbon|null $edit_date
 * @property int|null $parent_order
 * @property int|null $code
 * @property string|null $delivery_date
 * @property int $stop
 * @property int $return_to_wallet
 * @property string|null $tmara_order_id
 * @property string|null $tmara_capture_id
 * @property string|null $per_payment_id
 * @property string|null $per_track_id
 * @property int|null $check_mada_with_balance
 * @property-read \App\Models\User|null $accepted
 * @property-read \App\Models\User|null $added
 * @property-read \App\Models\Addresses|null $address
 * @property-read \App\Models\Balance|null $balance
 * @property-read \App\Models\PaymentMethods|null $cartPaymentMethod
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CartItem[] $cart_items
 * @property-read int|null $cart_items_count
 * @property-read \App\Models\Categories|null $country
 * @property-read \App\Models\User|null $driver
 * @property-read \App\Models\OrdersStatus|null $orderStatus
 * @property-read Orders|null $parentOrder
 * @property-read \App\Models\PaymentMethods|null $paymentMethod
 * @property-read \App\Models\User|null $provider
 * @property-read \App\Models\Regions|null $region
 * @property-read \App\Models\User|null $reviewd
 * @property-read \App\Models\OrderShipments|null $shipment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderShipments[] $shipments
 * @property-read int|null $shipments_count
 * @property-read \App\Models\States|null $state
 * @property-read \App\Models\Transaction|null $transaction
 * @property-read \App\Models\BankTransfer|null $transferParentPhoto
 * @property-read \App\Models\BankTransfer|null $transfer_photo
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|Orders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Orders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Orders query()
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereAcceptedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereAddressDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereAddressName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereCartPaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereCheckMadaWithBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereCobon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereCobonDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereDeliveryPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereEditDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereFinancialDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereIsEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereIsSchedul($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereMarketedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereOrderPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders wherePaidPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereParentOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders wherePerPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders wherePerTrackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereReminingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereReturnToWallet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereReviewdBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereSchedulingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereSentSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereShortCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereTmaraCaptureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereTmaraOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereTrackId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereWarehouseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orders whereWithBalance($value)
 */
	class Orders extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrdersDetails
 *
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersDetails query()
 */
	class OrdersDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OrdersStatus
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $btn_text
 * @property string $color
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus whereBtnText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrdersStatus whereUpdatedAt($value)
 */
	class OrdersStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Packages
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Packages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Packages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Packages query()
 */
	class Packages extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PageCategory
 *
 * @property int $id
 * @property string $name_ar
 * @property string $name_en
 * @property int $is_offer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory whereIsOffer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory whereNameAr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategory whereUpdatedAt($value)
 */
	class PageCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PageCategoryProduct
 *
 * @property int $id
 * @property int $page_category_id
 * @property int $product_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategoryProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategoryProduct newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategoryProduct query()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategoryProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategoryProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategoryProduct wherePageCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategoryProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCategoryProduct whereUpdatedAt($value)
 */
	class PageCategoryProduct extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Partners
 *
 * @property int $id
 * @property string $url
 * @property string $photo
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Partners newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partners newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partners query()
 * @method static \Illuminate\Database\Eloquent\Builder|Partners whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partners whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partners wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partners whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partners whereUrl($value)
 */
	class Partners extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PayAccount
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PayAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PayAccount query()
 */
	class PayAccount extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentLog
 *
 * @property int $id
 * @property string|null $data
 * @property int|null $user_id
 * @property int|null $order_id
 * @property string|null $amount
 * @property string|null $platform
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentLog whereUserId($value)
 */
	class PaymentLog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentMethods
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentMethods whereUpdatedAt($value)
 */
	class PaymentMethods extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PaymentSettings
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $value
 * @property string $note
 * @property int $orders
 * @property string $input_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $option_name
 * @property int $hidden
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereOptionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereOrders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSettings whereValue($value)
 */
	class PaymentSettings extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Payments
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Payments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payments query()
 */
	class Payments extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PostPhotos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|PostPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PostPhotos query()
 */
	class PostPhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Posts
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Posts newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Posts newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Posts query()
 */
	class Posts extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Prices
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Prices newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prices newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Prices query()
 */
	class Prices extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PricingOffer
 *
 * @property int $id
 * @property int $part_id
 * @property int $provider_id
 * @property int $manufacture_type 1=original 2=Unoriginal
 * @property string $prepare_time
 * @property int $available_quantity
 * @property float $price
 * @property int $order_type
 * @property string $manufacture_country
 * @property string $brand
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\PricingOrderType|null $PricingOrderType
 * @property-read \App\Models\ManufactureType|null $manufactureType
 * @property-read \App\Models\PricingOrderPart|null $part
 * @property-read \App\Models\User|null $shop
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer query()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereAvailableQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereManufactureCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereManufactureType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereOrderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer wherePartId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer wherePrepareTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOffer whereUpdatedAt($value)
 */
	class PricingOffer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PricingOrder
 *
 * @property int $id
 * @property int $user_id
 * @property int $published 0 not published 1 published
 * @property int $address_id
 * @property string $description
 * @property int $status
 * @property int $payment_method
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\UserCar|null $car
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PricingOrderPart[] $parts
 * @property-read int|null $parts_count
 * @property-read \App\Models\States|null $state
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder query()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrder whereUserId($value)
 */
	class PricingOrder extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PricingOrderPart
 *
 * @property int $id
 * @property int $category_id
 * @property int $subcategory_id
 * @property int $order_id
 * @property string $part_name
 * @property int $quantity
 * @property int $measurement_id
 * @property string $photo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PricingOffer[] $admin_offer
 * @property-read int|null $admin_offer_count
 * @property-read \App\Models\Categories|null $category
 * @property-read \App\Models\MeasurementUnit|null $measurement
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PricingOffer[] $offers
 * @property-read int|null $offers_count
 * @property-read \App\Models\PricingOrder|null $pricing_order
 * @property-read \App\Models\Subcategories|null $subcategory
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart query()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart whereMeasurementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart wherePartName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart whereSubcategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderPart whereUpdatedAt($value)
 */
	class PricingOrderPart extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PricingOrderType
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderType query()
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderType whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PricingOrderType whereUpdatedAt($value)
 */
	class PricingOrderType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Privileges
 *
 * @property int $id
 * @property string $privilge
 * @property int $hidden
 * @property string $icon
 * @property string $card_color
 * @property int $show_panel
 * @property string $url
 * @property int $parent_id
 * @property int $orders
 * @property string $model
 * @property string $controller
 * @property int $is_provider
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Privileges[] $subProgrames
 * @property-read int|null $sub_programes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges query()
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereCardColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereController($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereIsProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereOrders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges wherePrivilge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereShowPanel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges whereUrl($value)
 */
	class Privileges extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PrivilegesCountConditions
 *
 * @property int $id
 * @property int $privilege_id
 * @property string $condition
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesCountConditions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesCountConditions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesCountConditions query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesCountConditions whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesCountConditions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesCountConditions wherePrivilegeId($value)
 */
	class PrivilegesCountConditions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PrivilegesGroupsDetails
 *
 * @property int $id
 * @property int $privilege_id
 * @property int $privilege_group_id
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesGroupsDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesGroupsDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesGroupsDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesGroupsDetails whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesGroupsDetails wherePrivilegeGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PrivilegesGroupsDetails wherePrivilegeId($value)
 */
	class PrivilegesGroupsDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Privileges_groups
 *
 * @property int $id
 * @property string $name
 * @property int $is_provider
 * @property int $provider_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups query()
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups whereIsProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Privileges_groups whereUpdatedAt($value)
 */
	class Privileges_groups extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductCategories
 *
 * @property int $id
 * @property int $product_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategories whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategories whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductCategories whereUpdatedAt($value)
 */
	class ProductCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductExtraCategories
 *
 * @property int $id
 * @property int $product_id
 * @property int $extra_categories_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductExtraCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductExtraCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductExtraCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductExtraCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductExtraCategories whereExtraCategoriesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductExtraCategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductExtraCategories whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductExtraCategories whereUpdatedAt($value)
 */
	class ProductExtraCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductLikes
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLikes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLikes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLikes query()
 */
	class ProductLikes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductMakeYear
 *
 * @property int $id
 * @property int $product_id
 * @property int $make_year_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMakeYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMakeYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMakeYear query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMakeYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMakeYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMakeYear whereMakeYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMakeYear whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMakeYear whereUpdatedAt($value)
 */
	class ProductMakeYear extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductMealMenu
 *
 * @property int $product_id
 * @property int $meal_menus_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMealMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMealMenu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMealMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMealMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMealMenu whereMealMenusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMealMenu whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductMealMenu whereUpdatedAt($value)
 */
	class ProductMealMenu extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductPhotos
 *
 * @property int $id
 * @property int $product_id
 * @property string $photo
 * @property string $thumb
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Products|null $product
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductPhotos whereUpdatedAt($value)
 */
	class ProductPhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductRating
 *
 * @property int $id
 * @property int $user_id
 * @property int $rate
 * @property int $item_id
 * @property string $comment
 * @property int $type 1 product 2 offer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductRating whereUserId($value)
 */
	class ProductRating extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductSpecification
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSpecification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSpecification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductSpecification query()
 */
	class ProductSpecification extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Products
 *
 * @property int $id
 * @property int|null $provider_id
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $description_en
 * @property float $price
 * @property float $profit_perc
 * @property float $original_price
 * @property float $client_price
 * @property int $category_id
 * @property int $subcategory_id
 * @property int $measurement_id
 * @property float $weight
 * @property string $photo
 * @property string $thumb
 * @property int $quantity
 * @property int $min_quantity
 * @property int $min_warehouse_quantity
 * @property string $expiry
 * @property string $temperature
 * @property int $deliver_status
 * @property int $has_cover
 * @property int $has_regions1
 * @property int $is_trend
 * @property int $is_archived
 * @property int $stop
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int $sort
 * @property int $supplier_data_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CartItem[] $cart_items
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Categories[] $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\Categories|null $category
 * @property-read \App\Models\DeliverStatus|null $deliverStatus
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Favorite[] $favorites
 * @property-read int|null $favorites_count
 * @property-read mixed $is_fav
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MakeYear[] $make_years
 * @property-read int|null $make_years_count
 * @property-read \App\Models\MeasurementUnit|null $measurement
 * @property-read \App\Models\Models|null $model
 * @property-read \App\Models\ProductPhotos|null $photoImage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductPhotos[] $photos
 * @property-read int|null $photos_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Regions[] $product_regions
 * @property-read int|null $product_regions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\States[] $product_states
 * @property-read int|null $product_states_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Purchase_item[] $purchase_items
 * @property-read int|null $purchase_items_count
 * @property-read \App\Models\Subcategories|null $subcategory
 * @property-read \App\Models\SupplierData|null $supplier
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Products newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Products newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Products query()
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereClientPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereDeliverStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereExpiry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereHasCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereHasRegions1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereIsTrend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereMeasurementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereMinQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereMinWarehouseQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereOriginalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereProfitPerc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereSubcategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereSupplierDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Products whereWeight($value)
 */
	class Products extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProductsRegions
 *
 * @property int $id
 * @property int $product_id
 * @property int $region_id
 * @property int|null $state_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductsRegions whereUpdatedAt($value)
 */
	class ProductsRegions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProjectOffers
 *
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectOffers newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectOffers newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectOffers query()
 */
	class ProjectOffers extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProjectPhotos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectPhotos query()
 */
	class ProjectPhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ProjectRating
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProjectRating query()
 */
	class ProjectRating extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Projects
 *
 * @property-read \App\Models\Cities|null $city
 * @property-read \App\Models\Countries|null $country
 * @property-read \App\Models\States|null $state
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Projects newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Projects newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Projects query()
 */
	class Projects extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PurchaseOrderStatus
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $btn_text
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $sort
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus whereBtnText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PurchaseOrderStatus whereUpdatedAt($value)
 */
	class PurchaseOrderStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Purchase_item
 *
 * @property int $id
 * @property int|null $order_id
 * @property int|null $product_id
 * @property int|null $provider_id
 * @property int|null $status
 * @property int|null $quantity
 * @property int|null $delivered_quantity
 * @property int $driver_quantity
 * @property float|null $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $quantity_difference
 * @property-read \App\Models\PurchaseOrderStatus|null $orderStatus
 * @property-read \App\Models\Products|null $product
 * @property-read \App\Models\User|null $provider
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereDeliveredQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereDriverQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereQuantityDifference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_item whereUpdatedAt($value)
 */
	class Purchase_item extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Purchase_order
 *
 * @property int $id
 * @property int|null $provider_id
 * @property int|null $employee_id
 * @property string|null $currencey
 * @property int|null $payment_method
 * @property float|null $final_price
 * @property float|null $order_price
 * @property float|null $delivery_price
 * @property float|null $taxes
 * @property string|null $cobon
 * @property float|null $cobon_price
 * @property string|null $delivery_date
 * @property string|null $delivery_time
 * @property string|null $delivery_method
 * @property string|null $location
 * @property string|null $transfer_photo
 * @property string|null $details
 * @property int|null $payment_terms
 * @property int|null $status
 * @property int|null $driver_id
 * @property int|null $warehouse_id
 * @property string|null $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $provider_delivery_date
 * @property string|null $provider_delivery_time
 * @property string|null $warehouse_date
 * @property float $paid_price
 * @property int $is_edit
 * @property string|null $refuse_date
 * @property int $refused
 * @property string|null $reason_of_refuse
 * @property-read \App\Models\User|null $driver
 * @property-read \App\Models\PurchaseOrderStatus|null $orderStatus
 * @property-read \App\Models\SupplierPurcheseStatus|null $orderStatusSupplier
 * @property-read \App\Models\PaymentMethods|null $paymentMethod
 * @property-read \App\Models\Purchase_payment_method|null $paymentTerm
 * @property-read \App\Models\User|null $provider
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Purchase_item[] $purchase_item
 * @property-read int|null $purchase_item_count
 * @property-read \App\Models\User|null $warehouse
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereCobon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereCobonPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereCurrencey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereDeliveryMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereDeliveryPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereIsEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereOrderPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order wherePaidPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order wherePaymentTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereProviderDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereProviderDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereReasonOfRefuse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereRefuseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereRefused($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereTransferPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereWarehouseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_order whereWarehouseId($value)
 */
	class Purchase_order extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Purchase_payment_method
 *
 * @property int $id
 * @property string|null $name
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_payment_method newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_payment_method newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_payment_method query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_payment_method whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase_payment_method whereName($value)
 */
	class Purchase_payment_method extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Questions
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Questions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Questions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Questions query()
 */
	class Questions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Rating
 *
 * @property int $id
 * @property int $user_id
 * @property int $rate
 * @property int $item_id
 * @property string $comment
 * @property int $type 1 shop 2 product
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Rating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rating query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rating whereUserId($value)
 */
	class Rating extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Regions
 *
 * @property int $id
 * @property string $name_en
 * @property int $country_id
 * @property string $name
 * @property string $photo
 * @property string $longitude
 * @property string $latitude
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Regions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Regions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Regions query()
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Regions whereUpdatedAt($value)
 */
	class Regions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReportPhotos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ReportPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportPhotos query()
 */
	class ReportPhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReportPoints
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ReportPoints newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportPoints newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportPoints query()
 */
	class ReportPoints extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReportTypes
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ReportTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReportTypes query()
 */
	class ReportTypes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Reports
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Reports newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reports newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reports query()
 */
	class Reports extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RequestMoney
 *
 * @property int $id
 * @property int $user_id
 * @property float $price
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Withdraw|null $withdraw
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestMoney whereUserId($value)
 */
	class RequestMoney extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RequestProvider
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int|null $country_id
 * @property int|null $region_id
 * @property int|null $state_id
 * @property string $email
 * @property string $phone
 * @property string $phonecode
 * @property string $address
 * @property string $details
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Countries|null $country
 * @property-read \App\Models\User|null $employee
 * @property-read \App\Models\Regions|null $region
 * @property-read \App\Models\States|null $state
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider wherePhonecode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RequestProvider whereUserId($value)
 */
	class RequestProvider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RequestRepresentative
 *
 * @property-read \App\Models\Banks|null $bank
 * @method static \Illuminate\Database\Eloquent\Builder|RequestRepresentative newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestRepresentative newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestRepresentative query()
 */
	class RequestRepresentative extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RequestUserService
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RequestUserService newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestUserService newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RequestUserService query()
 */
	class RequestUserService extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ReservationFeatures
 *
 * @property int $id
 * @property int $reservation_id
 * @property int $number
 * @property int $feature_id
 * @property float $price
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Feature[] $feature
 * @property-read int|null $feature_count
 * @property-read \App\Models\Reservations|null $reservation
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures query()
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures whereFeatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures whereReservationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ReservationFeatures whereUpdatedAt($value)
 */
	class ReservationFeatures extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Reservations
 *
 * @property int $id
 * @property int $hall_id
 * @property int $user_id
 * @property string $date
 * @property string $from_time
 * @property string $to_time
 * @property int $status
 * @property int $final_price
 * @property int $payment_method
 * @property int $number
 * @property string $reason_of_cancel
 * @property float $reservation_hours
 * @property float $reservation_price
 * @property float $features_price
 * @property float $price_after_cancel
 * @property string $cancellation_date
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\BankTransfer|null $bankTransfer
 * @property-read \App\Models\Hall|null $hall
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ReservationFeatures[] $reservationFeatures
 * @property-read int|null $reservation_features_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations query()
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereCancellationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereFeaturesPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereFromTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereHallId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations wherePriceAfterCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereReasonOfCancel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereReservationHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereReservationPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereToTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Reservations whereUserId($value)
 */
	class Reservations extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RestaurantCategories
 *
 * @property int $restaurant_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategories whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategories whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RestaurantCategories whereUpdatedAt($value)
 */
	class RestaurantCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Restaurants
 *
 * @property int $id
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $description_en
 * @property float $min_order_price
 * @property float $delivery_price
 * @property int $meal_menu_id
 * @property string $logo
 * @property string $cover
 * @property int $approved
 * @property int $stop
 * @property string $longitude
 * @property string $latitude
 * @property int $state_id
 * @property string $address
 * @property string $address_en
 * @property int $user_id
 * @property int $free_delivery
 * @property int $delivery_limit
 * @property int $delivery_time
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RestaurantCategories[] $categories
 * @property-read int|null $categories_count
 * @property-read \App\Models\Categories|null $category
 * @property-read \App\Models\MealMenu|null $mealMenu
 * @property-read \App\Models\User $provider
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rating[] $ratings
 * @property-read int|null $ratings_count
 * @property-read \App\Models\States|null $state
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants query()
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereAddressEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereCover($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereDeliveryLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereDeliveryPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereDeliveryTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereFreeDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereMealMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereMinOrderPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Restaurants whereUserId($value)
 */
	class Restaurants extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ScreenDetails
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ScreenDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScreenDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScreenDetails query()
 */
	class ScreenDetails extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Screens
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Screens newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Screens newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Screens query()
 */
	class Screens extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SelectionOptions
 *
 * @property-read SelectionOptions|null $selection
 * @method static \Illuminate\Database\Eloquent\Builder|SelectionOptions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SelectionOptions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SelectionOptions query()
 */
	class SelectionOptions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Selections
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SelectionOptions[] $options
 * @property-read int|null $options_count
 * @property-read Selections|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|Selections newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Selections newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Selections query()
 */
	class Selections extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SendNotifications
 *
 * @property int $id
 * @property string $title
 * @property string $message
 * @property int $type
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications query()
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SendNotifications whereUpdatedAt($value)
 */
	class SendNotifications extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ServiceAdvantages
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAdvantages newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAdvantages newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceAdvantages query()
 */
	class ServiceAdvantages extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Services
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceAdvantages[] $advantages
 * @property-read int|null $advantages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Services newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Services newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Services query()
 */
	class Services extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ServicesCategories
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $photo
 * @property string $icon
 * @property int $sort
 * @property int $stop
 * @property int $is_archived
 * @property int $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $suppliers
 * @property-read int|null $suppliers_count
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesCategories whereUpdatedAt($value)
 */
	class ServicesCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ServicesPhotos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServicesPhotos query()
 */
	class ServicesPhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Settings
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property string $value
 * @property string $note
 * @property int $orders
 * @property string $input_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $option_name
 * @property int $hidden
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereOptionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereOrders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereValue($value)
 */
	class Settings extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Shipment
 *
 * @property int $id
 * @property string $name
 * @property string $photo
 * @property int $status
 * @property string $url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Shipment whereUrl($value)
 */
	class Shipment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ShopTypes
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTypes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTypes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTypes query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTypes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTypes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTypes whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTypes whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShopTypes whereUpdatedAt($value)
 */
	class ShopTypes extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SiteContent
 *
 * @property int $id
 * @property string $top_photo
 * @property string $about_photo
 * @property string $features_photo
 * @property string $about_text
 * @property string $app_video
 * @property string $footer_photo
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereAboutPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereAboutText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereAppVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereFeaturesPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereFooterPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereTopPhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteContent whereUpdatedAt($value)
 */
	class SiteContent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SiteFeature
 *
 * @property int $id
 * @property string $photo
 * @property string $title
 * @property string $description
 * @property string $icon
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature query()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteFeature whereUpdatedAt($value)
 */
	class SiteFeature extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SiteScreenshots
 *
 * @property int $id
 * @property string $photo
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SiteScreenshots newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteScreenshots newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteScreenshots query()
 * @method static \Illuminate\Database\Eloquent\Builder|SiteScreenshots whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteScreenshots whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteScreenshots wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SiteScreenshots whereUpdatedAt($value)
 */
	class SiteScreenshots extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Slider
 *
 * @property int $id
 * @property string $photo
 * @property string|null $thumb
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $description_en
 * @property int $is_en
 * @property string $button_title
 * @property string $button_url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int|null $has_link
 * @property int|null $item_type
 * @property int $item_id
 * @property int|null $locale
 * @property-read \App\Models\Categories|null $category
 * @property-read \App\Models\Products|null $product
 * @property-read \App\Models\User|null $shop
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider query()
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereButtonTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereButtonUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereHasLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereIsEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Slider whereUpdatedAt($value)
 */
	class Slider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Sliders
 *
 * @property int $id
 * @property string $photo
 * @property string|null $thumb
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $description_en
 * @property int $is_en
 * @property string $button_title
 * @property string $button_url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int|null $has_link
 * @property int|null $item_type
 * @property int $item_id
 * @property int|null $locale
 * @property-read \App\Models\Main_slider|null $main_slider
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereButtonTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereButtonUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereDescriptionEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereHasLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereIsEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereTitleEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sliders whereUpdatedAt($value)
 */
	class Sliders extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SmsaSetting
 *
 * @property int $id
 * @property string $passkey
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SmsaSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsaSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsaSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsaSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsaSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsaSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsaSetting wherePasskey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsaSetting whereUpdatedAt($value)
 */
	class SmsaSetting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SocialProvider
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider_id
 * @property string $provider
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialProvider whereUserId($value)
 */
	class SocialProvider extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SpecialCategories
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $name_en
 * @property string $photo
 * @property int $sort
 * @property int $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $products
 * @property-read int|null $products_count
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialCategories whereUserId($value)
 */
	class SpecialCategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\States
 *
 * @property int $id
 * @property string $name_en
 * @property int $country_id
 * @property int $region_id
 * @property string $name
 * @property string $routeCode
 * @property string $photo
 * @property string $smsa_name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|States newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|States newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|States query()
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereRouteCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereSmsaName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|States whereUpdatedAt($value)
 */
	class States extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Steps
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Steps newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Steps newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Steps query()
 */
	class Steps extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Stores
 *
 * @property-read \App\Models\States|null $city
 * @method static \Illuminate\Database\Eloquent\Builder|Stores newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stores newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stores query()
 */
	class Stores extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Styles
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Styles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Styles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Styles query()
 */
	class Styles extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Subcategories
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property int $orderat
 * @property string $photo
 * @property int $category_id
 * @property int $mobile
 * @property int $user_id
 * @property int $is_archived
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MeasurementUnit[] $measurementUnits
 * @property-read int|null $measurement_units_count
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereOrderat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subcategories whereUserId($value)
 */
	class Subcategories extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Suggestions
 *
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property string $message
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions query()
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Suggestions whereUserId($value)
 */
	class Suggestions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SupervisorGroup
 *
 * @property int $id
 * @property string $name
 * @property string $privileges
 * @property int $provider_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup wherePrivileges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroup whereUpdatedAt($value)
 */
	class SupervisorGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SupervisorGroupsPrivileges
 *
 * @property int $id
 * @property int $group_id
 * @property int $privilege_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroupsPrivileges newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroupsPrivileges newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroupsPrivileges query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroupsPrivileges whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroupsPrivileges whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroupsPrivileges whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroupsPrivileges wherePrivilegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupervisorGroupsPrivileges whereUpdatedAt($value)
 */
	class SupervisorGroupsPrivileges extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SupplierCategory
 *
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierCategory whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierCategory whereUserId($value)
 */
	class SupplierCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SupplierData
 *
 * @property int $id
 * @property int $user_id
 * @property string $supplier_name
 * @property string $supplier_name_en
 * @property string $bio
 * @property string $commercial_no
 * @property string $tax_no
 * @property string $photo
 * @property string $email
 * @property string $phone
 * @property string $maroof_no
 * @property int $stop
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereCommercialNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereMaroofNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereStop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereSupplierName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereSupplierNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereTaxNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierData whereUserId($value)
 */
	class SupplierData extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SupplierPurcheseStatus
 *
 * @property int $id
 * @property string $name
 * @property string $name_en
 * @property string $btn_text
 * @property string $color
 * @property int|null $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus whereBtnText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus whereNameEn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierPurcheseStatus whereUpdatedAt($value)
 */
	class SupplierPurcheseStatus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Tickets
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property int $order_id
 * @property string $type none ->no type  pricing->pricing order  damage->damage order shop-> shop cart order
 * @property int $admin
 * @property int $rate
 * @property int $closed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tickets whereUserId($value)
 */
	class Tickets extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $transfer_id
 * @property int $payment_method
 * @property float|null $price
 * @property float $payed_price
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePayedPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTransferId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 */
	class Transaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $email
 * @property string|null $password
 * @property string $phone
 * @property string|null $photo
 * @property int $user_type_id
 * @property int $activate
 * @property int $block
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $last_login
 * @property int $country_id
 * @property int $region_id
 * @property int|null $state_id
 * @property int $phonecode
 * @property string $device_token
 * @property string $activation_code
 * @property string $longitude
 * @property string $latitude
 * @property string $address
 * @property int $privilege_id
 * @property string $lang
 * @property int $notification
 * @property int $ready
 * @property string $token
 * @property string $email_edited
 * @property int $currency_id
 * @property float $main_provider
 * @property int $profit_rate
 * @property int $accept_pricing
 * @property int $accept_estimate
 * @property int $add_product
 * @property int $shop_type
 * @property int $client_type
 * @property string|null $commercial_no
 * @property string|null $commercial_end_date
 * @property string|null $commercial_id
 * @property string|null $tax_number
 * @property int $shipment_id
 * @property float $shipment_price
 * @property int $shipment_days
 * @property float $taxes
 * @property string $about
 * @property int $is_archived
 * @property int $approved
 * @property string|null $cancel_reason
 * @property int $has_regions
 * @property string $device_type
 * @property string|null $licence_end_date
 * @property string|null $licence_number
 * @property string|null $licence_photo
 * @property int|null $provider_id
 * @property int|null $main_supplier_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Addresses[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Regions[] $admin_regions
 * @property-read int|null $admin_regions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Balance[] $balances
 * @property-read int|null $balances_count
 * @property-read \App\Models\Banks|null $bank
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CartItem[] $cart
 * @property-read int|null $cart_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CartItem[] $cartItems
 * @property-read int|null $cart_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServicesCategories[] $cats
 * @property-read int|null $cats_count
 * @property-read \App\Models\ClientTypes|null $clientType
 * @property-read \App\Models\Countries|null $country
 * @property-read \App\Models\Currencies|null $currency
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DeviceTokens[] $devices
 * @property-read int|null $devices_count
 * @property-read \App\Models\MainSupplier|null $mainSupplier
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Notification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Orders[] $orders
 * @property-read int|null $orders_count
 * @property-read \App\Models\Privileges_groups|null $privilegesGroup
 * @property-read \App\Models\SupervisorGroup|null $privilegesHallGroup
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products[] $products
 * @property-read int|null $products_count
 * @property-read User|null $provider
 * @property-read \App\Models\Regions|null $region
 * @property-read \App\Models\SocialProvider $socialProviders
 * @property-read \App\Models\States|null $state
 * @property-read \App\Models\SupplierData|null $supplier
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SupplierCategory[] $supplier_categories
 * @property-read int|null $supplier_categories_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAcceptEstimate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAcceptPricing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCancelReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereClientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCommercialEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCommercialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCommercialNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeviceToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailEdited($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHasRegions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsArchived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLicenceEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLicenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLicencePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMainProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMainSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhonecode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePrivilegeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProfitRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReady($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShipmentDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShipmentPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereShopType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTaxNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTaxes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUserTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject {}
}

namespace App\Models{
/**
 * App\Models\UserCar
 *
 * @property int $id
 * @property int $make_id
 * @property int $year_id
 * @property int $model_id
 * @property string $structure_no
 * @property string $structure_photo
 * @property int $user_id
 * @property int $is_deleted
 * @property int $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\Make $make
 * @property-read \App\Models\Models|null $model
 * @property-read \App\Models\User $user
 * @property-read \App\Models\MakeYear|null $year
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereMakeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereStructureNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereStructurePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserCar whereYearId($value)
 */
	class UserCar extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserRating
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserRating query()
 */
	class UserRating extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UserServices
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserServices newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserServices newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserServices query()
 */
	class UserServices extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\UsersRegions
 *
 * @property int $id
 * @property int $user_id
 * @property int $region_id
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRegions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRegions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRegions query()
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRegions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRegions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRegions whereRegionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRegions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UsersRegions whereUserId($value)
 */
	class UsersRegions extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WhyUs
 *
 * @method static \Illuminate\Database\Eloquent\Builder|WhyUs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WhyUs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WhyUs query()
 */
	class WhyUs extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Withdraw
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property string $photo
 * @property int $price
 * @property int $bank_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\BankAccounts|null $bank
 * @property-read \App\Models\RequestMoney|null $order
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereUserId($value)
 */
	class Withdraw extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WorkPhotos
 *
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkPhotos query()
 */
	class WorkPhotos extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WorkingDays
 *
 * @property int $id
 * @property int $day_id
 * @property int $is_worked
 * @property int $restaurant_id
 * @property string $time_from
 * @property string $time_to
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays query()
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays whereDayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays whereIsWorked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays whereRestaurantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays whereTimeFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays whereTimeTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WorkingDays whereUpdatedAt($value)
 */
	class WorkingDays extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Works
 *
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Works newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Works newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Works query()
 */
	class Works extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Years
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Years newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Years newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Years query()
 */
	class Years extends \Eloquent {}
}

