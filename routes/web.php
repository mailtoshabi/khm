<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\OneClickController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\AffiliateSelfController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\UpdatePasswordController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\front\PdfGenerateController;
use App\Http\Controllers\front\PhonePeController;
use App\Http\Controllers\front\HomeController as FrontHomeController;
use App\Http\Controllers\front\AffiliateController as FrontAffiliateController;
use App\Http\Controllers\CustomerAuth\LoginController as CustomerLoginController;
use App\Http\Controllers\CustomerAuth\RegisterController as CustomerRegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//clear all cache
Route::get('/all_cache', function() {

    Artisan::call('cache:clear');
    Artisan::call('optimize');
    Artisan::call('route:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return '<h1>All cache cleared</h1>';
});

Route::get('/pagination_publish', function() {
    Artisan::call('vendor:publish --tag=laravel-pagination');
    return '<h1>Pagination Published</h1>';
});


//Clear Cache facade value:
Route::get('/clear_cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route_cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route_clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view_clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config_cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth::routes();

Route::get('/my_test', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Auth::routes(['login' => false]);
Route::prefix('administrator')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.show.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login');


    Route::group(['middleware' => 'auth', 'as' => 'admin.'], function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/', [HomeController::class, 'index'])->name('index');
        Route::get('/my_test', [App\Http\Controllers\HomeController::class, 'index'])->name('my_test');

        Route::group(['prefix' => 'affiliates', 'as' => 'affiliate.', 'middleware' => ['role:affiliate']], function () {
            // Route::get('/add-meta', ['as' => 'show.meta', 'uses'=> 'AffiliateSelfController@show_meta']);
            Route::get('/add-meta', [AffiliateSelfController::class, 'show_meta'])->name('show.meta');
            // Route::put('/update-meta', ['as' => 'update.meta', 'uses'=> 'AffiliateSelfController@update_meta']);
            Route::put('/update-meta', [AffiliateSelfController::class, 'update_meta'])->name('update.meta');
            Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
                // Route::get('/', ['as' => 'index', 'uses'=> 'AffiliateSelfController@index']);
                Route::get('/', [AffiliateSelfController::class, 'index'])->name('index');
                // Route::get('/all/data', ['as' => 'data', 'uses'=> 'AffiliateSelfController@data']);
                Route::get('/all/data', [AffiliateSelfController::class, 'data'])->name('data');
                // Route::get('{id}/add-details', ['as' => 'edit', 'uses'=> 'AffiliateSelfController@edit']);
                Route::get('/{id}/add-details', [AffiliateSelfController::class, 'edit'])->name('edit');
                // Route::put('{id}/update-details', ['as' => 'update', 'uses'=> 'AffiliateSelfController@update']);
                Route::put('/{id}/update-details', [AffiliateSelfController::class, 'update'])->name('update');
                // Route::get('/{id}/remove_product', ['as' => 'remove_product', 'uses' => 'AffiliateSelfController@remove_product']);
                Route::get('/{id}/remove_product', [AffiliateSelfController::class, 'remove_product'])->name('remove_product');
                // Route::post('/affiliate-getprice', ['as' => 'getprice', 'uses'=> 'AffiliateSelfController@getprice']);
                Route::post('/affiliate-getprice', [AffiliateSelfController::class, 'getprice'])->name('getprice');
            });
            Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
                // Route::get('/', ['as' => 'index', 'uses'=> 'AffiliateSelfController@category_index']);
                Route::get('/', [AffiliateSelfController::class, 'category_index'])->name('index');
                // Route::get('/childs', ['as' => 'child', 'uses'=> 'AffiliateSelfController@subcategory_index']);
                // Route::get('/childs', [AffiliateSelfController::class, 'subcategory_index'])->name('child');
                // Route::get('/data', ['as' => 'data', 'uses'=> 'AffiliateSelfController@category_data']);
                Route::get('/data', [AffiliateSelfController::class, 'category_data'])->name('data');
                // Route::get('/sub_data', ['as' => 'sub.data', 'uses'=> 'AffiliateSelfController@subcategory_data']);
                // Route::get('/sub_data', [AffiliateSelfController::class, 'subcategory_data'])->name('sub.data');
                // Route::get('/{id}/add_category', ['as' => 'add_category', 'uses' => 'AffiliateSelfController@add_category']);
                Route::get('/{id}/add_category', [AffiliateSelfController::class, 'add_category'])->name('add_category');
            });
            Route::group(['prefix' => 'brands', 'as' => 'brands.'], function () {
                // Route::get('/', ['as' => 'index', 'uses'=> 'AffiliateSelfController@brand_index']);
                Route::get('/', [AffiliateSelfController::class, 'brand_index'])->name('index');
                // Route::get('/data', ['as' => 'data', 'uses'=> 'AffiliateSelfController@brand_data']);
                Route::get('/data', [AffiliateSelfController::class, 'brand_data'])->name('data');
                // Route::get('/{id}/add_brand', ['as' => 'add_brand', 'uses' => 'AffiliateSelfController@add_brand']);
                Route::get('/{id}/add_brand', [AffiliateSelfController::class, 'add_brand'])->name('add_brand');
            });
            Route::group(['prefix' => 'banners', 'as' => 'banners.'], function () {
                // Route::get('/', ['as' => 'index', 'uses'=> 'AffiliateSelfController@banner_index']);
                Route::get('/', [AffiliateSelfController::class, 'banner_index'])->name('index');
                // Route::get('/data', ['as' => 'data', 'uses'=> 'AffiliateSelfController@banner_data']);
                Route::get('/data', [AffiliateSelfController::class, 'banner_data'])->name('data');
                // Route::get('/{id}/change_status', ['as' => 'change_status', 'uses' => 'AffiliateSelfController@banner_change_status']);
                Route::get('/{id}/change_status', [AffiliateSelfController::class, 'banner_change_status'])->name('change_status');
            });
            Route::group(['prefix' => 'oneclick_purchase', 'as' => 'oneclick_purchase.'], function () {
                // Route::get('/', ['as' => 'index', 'uses'=> 'AffiliateSelfController@show_oneclick']);
                Route::get('/', [AffiliateSelfController::class, 'show_oneclick'])->name('index');
                // Route::get('/data', ['as' => 'data', 'uses'=> 'AffiliateSelfController@oneclick_data']);
                Route::get('/data', [AffiliateSelfController::class, 'oneclick_data'])->name('data');
                // Route::get('/{id}/change_status', ['as' => 'change_status', 'uses' => 'AffiliateSelfController@change_status']);
                Route::get('/{id}/change_status', [AffiliateSelfController::class, 'change_status'])->name('change_status');
            });
            Route::group(['prefix' => 'sales', 'as' => 'sales.'], function () {
                // Route::get('/', ['as' => 'index', 'uses'=> 'AffiliateSelfController@show_sale']);
                Route::get('/', [AffiliateSelfController::class, 'show_sale'])->name('index');
                // Route::get('/data', ['as' => 'data', 'uses'=> 'AffiliateSelfController@sale_data']);
                Route::get('/data', [AffiliateSelfController::class, 'sale_data'])->name('data');
                // Route::get('/{id}/details', ['as' => 'show', 'uses'=> 'AffiliateSelfController@sale_show']);
                Route::get('/{id}/details', [AffiliateSelfController::class, 'sale_show'])->name('show');
            });
        });

        Route::group(['prefix' => 'brand', 'as' => 'brands.', 'middleware' => ['role:brand']], function () {
            Route::get('/add-meta', [BrandController::class, 'show_meta'])->name('show.meta');
            Route::put('/update-meta', [BrandController::class,'update_meta'])->name('update.meta');
            Route::group(['as' => 'products.'], function () {
                Route::get('/products', [BrandController::class,'product_index'])->name('index');
                Route::get('/data-product', [BrandController::class,'product_data'])->name('data');
                Route::get('/add-product', [BrandController::class,'product_create'])->name('create');
                Route::post('/add-product', [BrandController::class,'product_store'])->name('store');
                Route::get('{id}/edit-product', [BrandController::class,'product_edit'])->name('edit');
                Route::put('{id}/update-product', [BrandController::class,'product_update'])->name('update');
                Route::delete('/{id}/delete-product', [BrandController::class,'product_destroy'])->name('delete');
                Route::get('/{id}/change_status-product', [BrandController::class,'product_change_status'])->name('change_status');
                Route::get('/sidebar_categories/{id?}', [BrandController::class,'sidebar_categories'])->name('sidebar_categories');
            });
            Route::get('/dealer-data', [BrandController::class,'affiliates_data'])->name('dealer.data');
            Route::get('/dealers', [BrandController::class,'affiliates'])->name('dealer.index');
            Route::get('/{id}/dealers/add', [BrandController::class,'affiliate_add'])->name('dealer.add');
        });

        Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
            Route::group(['middleware' => ['role:admin']], function () {
                Route::get('/', [ProductController::class,'index'])->name('index');
                Route::get('/data', [ProductController::class,'data'])->name('data');
                Route::get('/add', [ProductController::class,'create'])->name('create');
                Route::post('/add', [ProductController::class,'store'])->name('store');
                Route::get('/{id}/edit', [ProductController::class,'edit'])->name('edit');
                Route::put('/{id}/update', [ProductController::class,'update'])->name('update');
                Route::delete('/{id}/update', [ProductController::class,'destroy'])->name('delete');
                Route::get('/{id}/change_status', [ProductController::class,'change_status'])->name('change_status');
                Route::get('/sidebar_categories/{id?}', [ProductController::class,'sidebar_categories'])->name('sidebar_categories');
            });

            Route::group(['prefix' => 'types', 'as' => 'types.','middleware' => ['role:admin']], function () {
                Route::get('/', [ProductTypeController::class,'index'])->name('index');
                Route::get('/data', [ProductTypeController::class,'data'])->name('data');
                Route::get('/create_modal', [ProductTypeController::class,'create_modal'])->name('create_modal');
                Route::post('/add', [ProductTypeController::class,'store'])->name('store');
                Route::get('/{id}/edit_modal', [ProductTypeController::class,'edit_modal'])->name('edit_modal');
                Route::put('/{id}/update', [ProductTypeController::class,'update'])->name('update');
                Route::delete('/{id}/delete', [ProductTypeController::class,'destroy'])->name('delete');
                Route::get('/{id}/change_status', [ProductTypeController::class,'change_status'])->name('change_status');
            });
        });

        Route::group(['middleware' => ['role:admin']], function () {
            Route::group(['prefix' => 'sales', 'as' => 'sales.'], function () {
                Route::get('/', [SaleController::class,'index'])->name('index');
                Route::get('/data', [SaleController::class,'data'])->name('data');
                Route::get('/{id}/details', [SaleController::class,'show'])->name('show');
                Route::get('/invoice/{id}/download', [SaleController::class,'bill_download'])->name('bill.download');
                Route::get('/change_status', [SaleController::class,'change_status'])->name('change_status');
                Route::get('/change_payment', [SaleController::class,'changePayment'])->name('change.payment');
                Route::get('/{id}/courier', [SaleController::class,'edit_courier'])->name('courier.edit');
                Route::put('/{id}/courier/update', [SaleController::class,'update_courier'])->name('courier.update');
                Route::put('/{id}/sms/sent', [SaleController::class,'sent_custom_sms'])->name('sms.sent');
                Route::get('/utr', [SaleController::class,'utr_update'])->name('utr.update');
            });
            Route::group(['prefix' => 'customers', 'as' => 'customers.'], function () {
                Route::get('/', [CustomerController::class,'index'])->name('index');
                Route::get('/data', [CustomerController::class,'data'])->name('data');
                Route::delete('/{id}/delete', [CustomerController::class,'destroy'])->name('delete');
                Route::get('/{id}/change_status', [CustomerController::class,'change_status'])->name('change_status');
                Route::get('/{id}/change_access', [CustomerController::class,'change_access'])->name('change_access');
            });
            Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
                Route::get('/', [CategoryController::class,'index'])->name('index');
                Route::get('/data', [CategoryController::class,'data'])->name('data');
                Route::get('/create_modal', [CategoryController::class,'create_modal'])->name('create_modal');
                Route::post('/add', [CategoryController::class,'store'])->name('store');
                Route::get('/{id}/edit', [CategoryController::class,'edit'])->name('edit');
                Route::get('/{id}/edit_modal', [CategoryController::class,'edit_modal'])->name('edit_modal');
                Route::put('/{id}/update', [CategoryController::class,'update'])->name('update');
                Route::delete('/{id}/delete', [CategoryController::class,'destroy'])->name('delete');
                Route::get('/{id}/change_status', [CategoryController::class,'change_status'])->name('change_status');
            });
            Route::group(['prefix' => 'brands', 'as' => 'brands.'], function () {
                Route::get('/', [BrandController::class,'index'])->name('index');
                Route::get('/data', [BrandController::class,'data'])->name('data');
                Route::get('/create_modal', [BrandController::class,'create_modal'])->name('create_modal');
                Route::post('/add', [BrandController::class,'store'])->name('store');
                Route::get('/{id}/edit', [BrandController::class,'edit'])->name('edit');
                Route::get('/{id}/edit_modal', [BrandController::class,'edit_modal'])->name('edit_modal');
                Route::put('/{id}/update', [BrandController::class,'update'])->name('update');
                Route::delete('/{id}/delete', [BrandController::class,'destroy'])->name('delete');
                Route::get('/{id}/change_status', [BrandController::class,'change_status'])->name('change_status');
            });
            Route::group(['prefix' => 'banners', 'as' => 'banners.'], function () {
                Route::get('/', [BannerController::class,'index'])->name('index');
                Route::get('/data', [BannerController::class,'data'])->name('data');
                Route::get('/create_modal', [BannerController::class,'create_modal'])->name('create_modal');
                Route::post('/add', [BannerController::class,'store'])->name('store');
                Route::get('/{id}/edit', [BannerController::class,'edit'])->name('edit');
                Route::get('/{id}/edit_modal', [BannerController::class,'edit_modal'])->name('edit_modal');
                Route::put('/{id}/update', [BannerController::class,'update'])->name('update');
                Route::delete('/{id}/delete', [BannerController::class,'destroy'])->name('delete');
                Route::get('/{id}/change_status', [BannerController::class,'change_status'])->name('change_status');
            });
            Route::group(['prefix' => 'sliders', 'as' => 'sliders.'], function () {
                Route::get('/', [SliderController::class,'index'])->name('index');
                Route::get('/data', [SliderController::class,'data'])->name('data');
                Route::get('/create_modal', [SliderController::class,'create_modal'])->name('create_modal');
                Route::post('/add', [SliderController::class,'store'])->name('store');
                Route::get('/{id}/edit', [SliderController::class,'edit'])->name('edit');
                Route::get('/{id}/edit_modal', [SliderController::class,'edit_modal'])->name('edit_modal');
                Route::put('/{id}/update', [SliderController::class,'update'])->name('update');
                Route::delete('/{id}/delete', [SliderController::class,'delete'])->name('delete');
                Route::get('/{id}/change_status', [SliderController::class,'change_status'])->name('change_status');
            });

            Route::group(['prefix' => 'affiliates', 'as' => 'affiliates.'], function () {
                Route::get('/', [AffiliateController::class,'index'])->name('index');
                Route::get('/data', [AffiliateController::class,'data'])->name('data');
                Route::get('/add', [AffiliateController::class,'create'])->name('create');
                Route::post('/add', [AffiliateController::class,'store'])->name('store');
                Route::get('/{id}/edit', [AffiliateController::class,'edit'])->name('edit');
                Route::put('/{id}/update', [AffiliateController::class,'update'])->name('update');
                Route::delete('/{id}/delete', [AffiliateController::class,'delete'])->name('delete');
                Route::get('/{id}/change_status', [AffiliateController::class,'change_status'])->name('change_status');
            });

            Route::group(['prefix' => 'subscribers', 'as' => 'subscriber.'], function () {
                Route::get('/', [SubscriberController::class,'index'])->name('index');
                Route::get('/data', [SubscriberController::class,'data'])->name('data');
            });
            Route::group(['prefix' => 'oneclick_purchase', 'as' => 'oneclick_purchase.'], function () {
                Route::get('/', [OneClickController::class,'index'])->name('index');

                Route::get('/data', [OneClickController::class,'data'])->name('data');

                Route::get('/{id}/change_status', [OneClickController::class,'change_status'])->name('change_status');
                Route::delete('/{id}/delete', [OneClickController::class,'destroy'])->name('delete');
            });

            Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
                Route::get('/', [SettingsController::class,'general_edit'])->name('general.edit');
                Route::put('/update', [SettingsController::class,'general_update'])->name('general.update');
                Route::get('/password', [UpdatePasswordController::class,'edit'])->name('password.edit');
                Route::put('/password/update', [UpdatePasswordController::class,'update'])->name('password.update');
            });

        });


    });
});


/* Front End Start*/

Route::group(['as' => 'customer.'], function () {
    Route::group(['middleware' => 'customer.guest'], function () {
        Route::post('/customer_register', [CustomerRegisterController::class,'register'])->name('register');
        Route::post('/validate_otp', [CustomerRegisterController::class,'validate_otp'])->name('validate.otp');
        Route::post('/future_otp', [CustomerRegisterController::class,'generate_future_otp'])->name('future.otp');
        Route::post('/resend_otp', [CustomerRegisterController::class,'resend_otp'])->name('resend.otp');
        Route::post('/customer_login', [CustomerLoginController::class,'login'])->name('login');
        Route::post('/check_customer', [CustomerLoginController::class,'check_customer'])->name('check');
    });

    // Route::group(['middleware' => 'customer.guest'], function () {
        Route::get('/auth/gmail', [CustomerLoginController::class,'redirectToGmail'])->name('redirect.gmail');
        Route::get('/auth/gmail/callback', [CustomerLoginController::class,'handleGmailCallback'])->name('callback.gmail');
    // });
    Route::group(['middleware' => 'customer.auth'], function() {
        Route::post('/customer_logout', [CustomerLoginController::class,'logout'])->name('logout');
    });
});
// Route::group(['namespace' => 'front'], function () {

// });
Route::get('/', [FrontHomeController::class,'index'])->name('index');
    Route::get('/test', [FrontHomeController::class,'test'])->name('test');
    Route::get('/about', [FrontHomeController::class,'about_us'])->name('about');
    Route::get('/contact', [FrontHomeController::class,'contact'])->name('contact');
    /*Route::post('/contact-send', ['as' => 'contact.send', 'uses' => 'HomeController@contact_send']);*/
    Route::post('/dealer-send', [FrontHomeController::class,'dealer_send'])->name('dealer.send');
    Route::get('/payments', [FrontHomeController::class,'payments'])->name('payments');
    Route::get('/disclaimer', [FrontHomeController::class,'disclaimer'])->name('disclaimer');
    Route::get('/shipping', [FrontHomeController::class,'shipping'])->name('shipping');
    Route::get('/cancellation', [FrontHomeController::class,'cancellation'])->name('cancellation');
    Route::get('/privacy_policy', [FrontHomeController::class,'privacy_policy'])->name('privacy_policy');
    Route::get('/terms_conditions', [FrontHomeController::class,'terms_conditions'])->name('terms_conditions');
    Route::get('/go-online-now', [FrontHomeController::class,'affiliate'])->name('affiliate');
    Route::get('/categories', [FrontHomeController::class,'category_all'])->name('category.all');
    Route::get('/featured-categories', [FrontHomeController::class,'featured_products'])->name('category.featured');
    Route::get('/offers', [FrontHomeController::class,'offer_products'])->name('offer.products');
    Route::get('/brands', [FrontHomeController::class,'brands'])->name('brands');
    Route::get('/shipping/option/{id}', [FrontHomeController::class,'shipping_options'])->name('shipping.options');
    Route::post('/prescription', [FrontHomeController::class,'prescription'])->name('prescription');
    Route::group(['as' => 'product.'], function () {
        Route::post('/product/oneclick_purchase', [FrontHomeController::class,'oneclick_purchase'])->name('oneclick.purchase');
        Route::post('/product/search', [FrontHomeController::class,'search_results'])->name('search');
        Route::get('/product/search', [FrontHomeController::class,'search_results'])->name('search.get');
        Route::post('/product/search/type', [FrontHomeController::class,'search_on_type'])->name('search.on_type');
        Route::post('/list-subcategories', [FrontHomeController::class,'subcategory_list'])->name('list.subcategories');
        Route::get('/product/get_price', [FrontHomeController::class,'get_price'])->name('get_price');
        Route::get('/product/get_stock', [FrontHomeController::class,'get_stock'])->name('get_stock');
        Route::get('/add_cart', [FrontHomeController::class,'cart_add'])->name('add_cart');
        Route::get('/{item}/update_cart', [FrontHomeController::class,'cart_update'])->name('update_cart');
        Route::get('/{item}/delete_cart', [FrontHomeController::class,'cart_delete'])->name('delete_cart');
        Route::get('/cart', [FrontHomeController::class,'cart_show'])->name('cart');
        Route::get('/cart/refresh', [FrontHomeController::class,'refresh_cart'])->name('cart.refresh');
        Route::get('/clear_cart', [FrontHomeController::class,'cart_clear'])->name('cart.clear');
    });
    // Route::get('/checkout/login', ['middleware' => 'customer.guest', 'as' => 'checkout.login', 'uses' => 'HomeController@checkout_login']);
    Route::get('/checkout/login', [FrontHomeController::class,'checkout_login'])->middleware('customer.guest')->name('checkout.login');
    Route::group(['middleware' => 'customer.auth'], function () {
        // Route::get('/checkout/address', ['as' => 'checkout.address', 'uses' => 'HomeController@checkout_address']);
        Route::get('/checkout/address', [FrontHomeController::class,'checkout_address'])->name('checkout.address');
        // Route::post('/checkout/address', ['as' => 'checkout.address.store', 'uses' => 'HomeController@checkout_address_store']);
        Route::post('/checkout/address', [FrontHomeController::class,'checkout_address_store'])->name('checkout.address.store');
        // Route::post('/districts', ['as' => 'list.districts', 'uses' => 'HomeController@distric_list']);
        Route::post('/districts', [FrontHomeController::class,'distric_list'])->name('list.districts');
        // Route::get('/checkout/payments', ['as' => 'checkout.payment_options', 'uses' => 'HomeController@payment_options']);
        Route::get('/checkout/payments', [FrontHomeController::class,'payment_options'])->name('checkout.payment_options');
        // Route::post('/checkout/payment', ['as' => 'checkout.payment_options.store', 'uses' => 'HomeController@payment_options_store']);
        Route::post('/checkout/payment', [FrontHomeController::class,'payment_options_store'])->name('checkout.payment_options.store');
        Route::post('/fed-checkout', [FrontHomeController::class, 'fedPaycheckout'])->name('fed_checkout');
        Route::get('/checkout/payment/success/{sale}/{customer}', [FrontHomeController::class,'fed_payment_success'])->name('fed_payment_success');
        Route::get('/checkout/payment/failed/{sale}/{customer}', [FrontHomeController::class,'fed_payment_fail'])->name('fed_payment_fail');
        // Route::post('/checkout/payment/success', ['as' => 'checkout.payment.success', 'uses' => 'HomeController@payment_success']);
        // Route::post('/checkout/payment/success', [FrontHomeController::class,'payment_success'])->name('checkout.payment.success');
        Route::get('/invoice', [PdfGenerateController::class,'pdfview'])->name('bill.download');
        Route::get('/invoice/{id}/download', [PdfGenerateController::class,'viewBill'])->name('bill.download.specific');
        // Route::get('/profile', ['as' => 'profile', 'uses'=> 'HomeController@profile']);
        Route::get('/profile', [FrontHomeController::class,'profile'])->name('profile');
        // Route::post('/profile/update', ['as' => 'customer.profile.update', 'uses' => 'HomeController@profile_update']);
        Route::post('/profile/update', [FrontHomeController::class,'profile_update'])->name('customer.profile.update');
        // Route::get('/myorders', ['as' => 'myorders', 'uses' => 'HomeController@my_orders']);
        Route::get('/myorders', [FrontHomeController::class,'my_orders'])->name('myorders');
        // Route::post('/utr', ['as' => 'utr.update', 'uses' => 'HomeController@utr_update']);
        Route::post('/utr', [FrontHomeController::class,'utr_update'])->name('utr.update');
        Route::get('/cancel_order', ['as' => 'order.cancel', 'uses' => 'HomeController@cancel_order']);
        Route::get('/cancel_order', [FrontHomeController::class,'cancel_order'])->name('order.cancel');
        Route::get('/account/delete', ['as' => 'delete.account', 'uses' => 'HomeController@delete_account']);
        Route::get('/account/delete', [FrontHomeController::class,'delete_account'])->name('delete.account');
        // Route::post('/account/delete', ['as' => 'delete.account.act', 'uses' => 'HomeController@delete_account_act']);
        Route::post('/account/delete', [FrontHomeController::class,'delete_account_act'])->name('delete.account.act');
        // Route::get('/account/settings', ['as' => 'settings.account', 'uses' => 'HomeController@settings_account']);
        Route::get('/account/settings', [FrontHomeController::class,'settings_account'])->name('settings.account');
    });
    // Route::get('generate-pdf', 'PdfGenerateController@pdfview')->name('generate-pdf');
    Route::get('/generate-pdf', [PdfGenerateController::class,'pdfview'])->name('generate-pdf');
    // Route::post('/instamojo/webhook', ['as' => 'checkout.payment.webhook', 'uses' => 'HomeController@payment_webhook']);
    Route::post('/instamojo/webhook', [FrontHomeController::class,'payment_webhook'])->name('checkout.payment.webhook');
    Route::get('/pay_later/{sale}', [FrontHomeController::class,'pay_later'])->name('checkout.pay.later');
    // Route::get('/payment/online', ['as' => 'payment.online', 'uses' => 'HomeController@online_pay']);
    Route::get('/payment/online', [FrontHomeController::class,'online_pay'])->name('payment.online');
    // Route::get('/stores', ['as' => 'affiliates', 'uses'=> 'HomeController@affiliates']);
    Route::get('/stores', [FrontHomeController::class,'affiliates'])->name('affiliates');
    Route::group(['as' => 'clinic.'], function () {
    // Route::get('/clinic', ['as' => 'index', 'uses'=> 'ClinicController@index']);
    });
    // Route::post('/subscribe', ['as' => 'subscribe', 'uses' => 'HomeController@subscribe']);
Route::post('/subscribe', [FrontHomeController::class,'subscribe'])->name('subscribe');


// Route::get('/test_pay', ['as' => 'test_pay', 'uses'=> 'front\HomeController@test_pay']);
Route::get('/test_pay', [FrontHomeController::class,'test_pay'])->name('test_pay');
// Route::post('/test_pay_success', ['as' => 'test_pay_success', 'uses'=> 'front\HomeController@meTrnSuccess']);
Route::get('/test_pay_success', [FrontHomeController::class,'meTrnSuccess'])->name('test_pay_success');
// Route::post('/meTrnPay', ['as' => 'meTrnPay', 'uses'=> 'front\HomeController@meTrnPay']);
Route::post('/meTrnPay', [FrontHomeController::class,'meTrnPay'])->name('meTrnPay');

// Route::get('phonepe', ['as' => 'phonepe.request', 'uses'=> 'front\PhonePeController@phonepe']);
Route::get('/phonepe', [PhonePeController::class,'phonepe'])->name('phonepe.request');
// Route::any('phonepe-response', ['as' => 'phonepe.response', 'uses'=> 'front\PhonePeController@response']);
Route::any('/phonepe-response', [PhonePeController::class,'response'])->name('phonepe.response');
// Route::any('phonepe-failed', ['as' => 'phonepe.failed', 'uses'=> 'front\PhonePeController@phonepe-failed']);
Route::get('/phonepe-failed', [PhonePeController::class,'phonepe-failed'])->name('phonepe.failed');


Route::get('/{slug}', [FrontHomeController::class,'all_slug'])->name('all.slug');

Route::group(['as' => 'affiliate.'], function () {
    // Route::get('/{slug}/checkout/login', ['middleware' => 'customer_guest', 'as' => 'checkout.login', 'uses' => 'AffiliateController@checkout_login']);
    Route::get('/{slug}/checkout/login', [FrontAffiliateController::class,'checkout_login'])->middleware('customer.guest')->name('checkout.login');
    Route::group(['middleware' => 'customer.auth'], function () {
        // Route::get('/{slug}/checkout/address', ['as' => 'checkout.address', 'uses' => 'AffiliateController@checkout_address']);
        Route::get('/{slug}/checkout/address', [FrontAffiliateController::class,'checkout_address'])->name('checkout.address');
        // Route::post('/{slug}/checkout/address', ['as' => 'checkout.address.store', 'uses' => 'AffiliateController@checkout_address_store']);
        Route::post('/{slug}/checkout/address', [FrontAffiliateController::class,'checkout_address_store'])->name('checkout.address.store');
        // Route::get('/{slug}/checkout/payments', ['as' => 'checkout.payment_options', 'uses' => 'AffiliateController@payment_options']);
        Route::get('/{slug}/checkout/payments', [FrontAffiliateController::class,'payment_options'])->name('checkout.payment_options');
        // Route::post('/{slug}/checkout/payment', ['as' => 'checkout.payment_options.store', 'uses' => 'AffiliateController@payment_options_store']);
        Route::post('/{slug}/checkout/payment', [FrontAffiliateController::class,'payment_options_store'])->name('checkout.payment_options.store');
        // Route::post('/{slug}/checkout/payment/success', ['as' => 'checkout.payment.success', 'uses' => 'AffiliateController@payment_success']);
        // Route::post('/{slug}/checkout/payment/success', [FrontAffiliateController::class,'payment_success'])->name('checkout.payment.success');
        // Route::get('/{slug}/payment/online', ['as' => 'payment.online', 'uses' => 'AffiliateController@online_pay']);
        Route::get('/{slug}/payment/online', [FrontAffiliateController::class,'online_pay'])->name('payment.online');
        // Route::get('/{slug}/invoice', 'PdfGenerateController@pdfview')->name('bill.download');
        Route::get('/{slug}/invoice', [PdfGenerateController::class,'pdfview'])->name('bill.download');
        // Route::get('/{slug}/invoice/{id}/download', 'PdfGenerateController@viewBill')->name('bill.download.specific');
        Route::get('/{slug}/invoice/{id}/download', [PdfGenerateController::class,'viewBill'])->name('bill.download.specific');
        // Route::get('/{slug}/profile', ['as' => 'profile', 'uses'=> 'AffiliateController@profile']);
        Route::get('/{slug}/profile', [FrontAffiliateController::class,'profile'])->name('profile');
        // Route::post('/{slug}/profile/update', ['as' => 'customer.profile.update', 'uses' => 'AffiliateController@profile_update']);
        Route::post('/{slug}/profile/update', [FrontAffiliateController::class,'profile_update'])->name('customer.profile.update');
        // Route::get('/{slug}/myorders', ['as' => 'myorders', 'uses' => 'AffiliateController@my_orders']);
        Route::get('/{slug}/myorders', [FrontAffiliateController::class,'my_orders'])->name('myorders');
        // Route::post('/{slug}/utr', ['as' => 'utr.update', 'uses' => 'AffiliateController@utr_update']);
        Route::post('/slug}/utr', [FrontAffiliateController::class,'utr_update'])->name('utr.update');
        // Route::get('/{slug}/cancel_order', ['as' => 'order.cancel', 'uses' => 'AffiliateController@cancel_order']);
        Route::get('/{slug}/cancel_order', [FrontAffiliateController::class,'cancel_order'])->name('order.cancel');
        // Route::get('/{slug}/account/delete', ['as' => 'delete.account', 'uses' => 'AffiliateController@delete_account']);
        Route::get('/{slug}/account/delete', [FrontAffiliateController::class,'delete_account'])->name('delete.account');
        // Route::post('/{slug}/account/delete', ['as' => 'delete.account.act', 'uses' => 'AffiliateController@delete_account_act']);
        Route::post('/{slug}/account/delete', [FrontAffiliateController::class,'delete_account_act'])->name('delete.account.act');
        // Route::get('/{slug}/account/settings', ['as' => 'settings.account', 'uses' => 'AffiliateController@settings_account']);
        Route::get('/{slug}/account/settings', [FrontAffiliateController::class,'settings_account'])->name('settings.account');
    });
    Route::group(['as' => 'product.'], function () {
        // Route::post('/{slug}/product/search', ['as' => 'search', 'uses'=> 'AffiliateController@search_results']);
        Route::post('/{slug}/product/search', [FrontAffiliateController::class,'search_results'])->name('search_post');
        // Route::get('/{slug}/product/search', ['as' => 'search', 'uses'=> 'AffiliateController@search_results']);
        Route::get('/{slug}/product/search', [FrontAffiliateController::class,'search_results'])->name('search');
        // Route::post('/{slug}/product/search/type', ['as' => 'search.on_type', 'uses'=> 'AffiliateController@search_on_type']);
        Route::post('/{slug}/product/search/type', [FrontAffiliateController::class,'search_on_type'])->name('search.on_type');
        // Route::post('/{slug}/list-subcategories', ['as' => 'list.subcategories', 'uses' => 'AffiliateController@subcategory_list']);
        Route::post('/{slug}/list-subcategories', [FrontAffiliateController::class,'subcategory_list'])->name('list.subcategories');
        // Route::get('/{slug}/product/get_price', ['as' => 'get_price', 'uses'=> 'AffiliateController@get_price']);
        Route::get('/{slug}/product/get_price', [FrontAffiliateController::class,'get_price'])->name('get_price');
        // Route::get('/{slug}/cart', ['as' => 'cart', 'uses' => 'AffiliateController@cart_show']);
        Route::get('/{slug}/cart', [FrontAffiliateController::class,'cart_show'])->name('cart');
        // Route::get('/{slug}/add_cart', ['as' => 'add_cart', 'uses' => 'AffiliateController@cart_add']);
        Route::get('/{slug}/add_cart', [FrontAffiliateController::class,'cart_add'])->name('add_cart');
        // Route::post('/{slug}/product/oneclick_purchase', ['as' => 'oneclick.purchase', 'uses'=> 'AffiliateController@oneclick_purchase']);
        Route::post('/{slug}/product/oneclick_purchase', [FrontAffiliateController::class,'oneclick_purchase'])->name('oneclick.purchase');
        // Route::post('/{slug}/prescription', ['as' => 'prescription', 'uses'=> 'AffiliateController@prescription']);
        Route::post('/{slug}/prescription', [FrontAffiliateController::class,'prescription'])->name('prescription');
        // Route::get('/{slug}/{item}/update_cart', ['as' => 'update_cart', 'uses' => 'AffiliateController@cart_update']);
        Route::get('/{slug}/{item}/update_cart', [FrontAffiliateController::class,'cart_update'])->name('update_cart');
        // Route::get('/{slug}/{item}/delete_cart', ['as' => 'delete_cart', 'uses' => 'AffiliateController@cart_delete']);
        Route::get('/{slug}/{item}/delete_cart', [FrontAffiliateController::class,'cart_delete'])->name('delete_cart');
        // Route::get('/{slug}/clear_cart', ['as' => 'cart.clear', 'uses' => 'AffiliateController@cart_clear']);
        Route::get('/{slug}/clear_cart', [FrontAffiliateController::class,'cart_clear'])->name('cart.clear');
    });
    // Route::get('/{slug}/test', ['as' => 'test', 'uses'=> 'AffiliateController@test']);
    Route::get('/{slug}/test', [FrontAffiliateController::class,'test'])->name('test');
    // Route::get('/{slug}/services', ['as' => 'services', 'uses'=> 'AffiliateController@clinics']);
    Route::get('/{slug}/services', [FrontAffiliateController::class,'clinics'])->name('services');
    // Route::post('/{slug}/services', ['as' => 'services', 'uses'=> 'AffiliateController@clinic_search']);
    Route::post('/{slug}/services', [FrontAffiliateController::class,'clinic_search'])->name('services_post');
    // Route::get('/{slug}/about', ['as' => 'about', 'uses'=> 'AffiliateController@about_us']);
    Route::get('/{slug}/about', [FrontAffiliateController::class,'about_us'])->name('about');
    // Route::get('/{slug}/contact', ['as' => 'contact', 'uses'=> 'AffiliateController@contact']);
    Route::get('/{slug}/contact', [FrontAffiliateController::class,'contact'])->name('contact');
    /*Route::post('/{slug}/contact-send', ['as' => 'contact.send', 'uses' => 'AffiliateController@contact_send']);*/
    // Route::get('/{slug}/payments', ['as' => 'payments', 'uses'=> 'AffiliateController@payments']);
    Route::get('/{slug}/payments', [FrontAffiliateController::class,'payments'])->name('payments');
    // Route::get('/{slug}/disclaimer-policy', ['as' => 'disclaimer', 'uses'=> 'AffiliateController@disclaimer']);
    Route::get('/{slug}/disclaimer-policy', [FrontAffiliateController::class,'disclaimer'])->name('disclaimer');
    // Route::get('/{slug}/shipping', ['as' => 'shipping', 'uses'=> 'AffiliateController@shipping']);
    Route::get('/{slug}/shipping', [FrontAffiliateController::class,'shipping'])->name('shipping');
    // Route::get('/{slug}/cancellation', ['as' => 'cancellation', 'uses'=> 'AffiliateController@cancellation']);
    Route::get('/{slug}/cancellation', [FrontAffiliateController::class,'cancellation'])->name('cancellation');
    // Route::get('/{slug}/privacy_policy', ['as' => 'privacy_policy', 'uses'=> 'AffiliateController@privacy_policy']);
    Route::get('/{slug}/privacy_policy', [FrontAffiliateController::class,'privacy_policy'])->name('privacy_policy');
    // Route::get('/{slug}/terms_conditions', ['as' => 'terms_conditions', 'uses'=> 'AffiliateController@terms_conditions']);
    Route::get('/{slug}/terms_conditions', [FrontAffiliateController::class,'terms_conditions'])->name('terms_conditions');
    // Route::get('/{slug}/category/all', ['as' => 'category.all', 'uses'=> 'AffiliateController@category_all']);
    Route::get('/{slug}/category/all', [FrontAffiliateController::class,'category_all'])->name('category.all');
    // Route::get('/{slug}/category/featured', ['as' => 'category.featured', 'uses'=> 'AffiliateController@featured_products']);
    Route::get('/{slug}/category/featured', [FrontAffiliateController::class,'featured_products'])->name('category.featured');
    // Route::get('/{slug}/offers', ['as' => 'offer.products', 'uses'=> 'AffiliateController@offer_products']);
    Route::get('/{slug}/offers', [FrontAffiliateController::class,'offer_products'])->name('offer.products');
    // Route::get('/{slug}/brands', ['as' => 'brands', 'uses'=> 'AffiliateController@brands']);
    Route::get('/{slug}/brands', [FrontAffiliateController::class,'brands'])->name('brands');
    // Route::get('/{slug}/pay_later/{sale}', ['as' => 'checkout.pay.later', 'uses' => 'AffiliateController@pay_later']);
    Route::get('/{slug}/pay_later/{sale}', [FrontAffiliateController::class,'pay_later'])->name('checkout.pay.later');
    // Route::get('/{slug}/{item_slug}', ['as' => 'all.slug', 'uses'=> 'AffiliateController@all_slug']);
    Route::get('/{slug}/{item_slug}', [FrontAffiliateController::class,'all_slug'])->name('all.slug');


});

/* Front End End*/
