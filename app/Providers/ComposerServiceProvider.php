<?php

namespace App\Providers;

use App\Http\Utilities\Utility;
use App\Models\Affiliate;
use App\Models\AllSlug;
use App\Models\Category;
use App\Models\ClinicType;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
// use Auth;
use Illuminate\Support\Facades\Auth;
// use Utility;

class ComposerServiceProvider extends ServiceProvider
{

    /*public function __construct($app, Request $request)
    {
        parent::__construct($app);

        $this->request = $request;
    }*/

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        view()->composer(
            'admin.includes.sidemenu', 'App\Http\ViewComposers\MenuBarComposer'
        );


        view()->composer(['admin.includes.header','admin.includes.sidemenu'], function ($view) {
            $user = User::find(Auth::id());
            $auth_user = [];
            $auth_user['name'] = $user->name;
            $auth_user['email'] = $user->email;
            $auth_user['logo'] = $user->user_detail->profile_pic;
            $view->with('auth_user',$auth_user);
        });

        view()->composer(['admin.includes.side-menu-item'], function ($view) {
            $new_sale = Sale::where('status',Utility::SALE_STATUS_NEW)->get();
            $view->with('new_sale_count',$new_sale->count());
        });

        view()->composer(['includes.headers'], function ($view) use ($request) {
            // $mainCategories = Category::where('is_active',1)->has('parents',0)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->orderBy('order_no','asc')->pluck('name','id');
            // $view->with('mainCategories',$mainCategories);

            $categories = Category::where('is_active',1)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->orderBy('order_no','asc')->pluck('name','id');
            $view->with('categories',$categories);
        });

        view()->composer(['affiliates.includes.headers'], function ($view) use ($request) {
            $affiliate = Affiliate::find(session('kerala_h_m_affiliate'));

            // $mainCategories = $affiliate->main_categories()->where('is_active',1)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->orderBy('name','asc')->get();
            // $view->with('mainCategories',$mainCategories);



            $categories = Category::where('is_active',1)->whereNotIn('id', [Utility::CATEGORY_ID_OFFER])->orderBy('order_no','asc')
            ->join('affiliate_category', function ($join) {
                $join->on('affiliate_category.category_id', '=', 'categories.id')
                    ->where('affiliate_category.affiliate_id', session('kerala_h_m_affiliate'));
            })
            ->get();

            $view->with('mainCategories',$categories);
        });

        // view()->composer(['includes.headers_services','affiliates.includes.headers_services','affiliates.includes.headers_service_affiliate'], function ($view) use ($request) {
        //     $types=ClinicType::pluck('name','id');
        //     $districts = DB::table('districts')->where('state_id',Utility::STATE_ID_KERALA)->pluck('name','id');
        //     $view->with(['types'=>$types,'districts'=> $districts]);
        // });

        view()->composer(['includes.nav','affiliates.includes.nav'], function ($view) {

            $categoryLists = Category::where('is_active',1)->has('parents',0)->take(11)->orderBy('order_no', 'asc')->get();
            foreach($categoryLists as $categoryList) {
                $all_slug = AllSlug::where('causer_id',$categoryList->id)->where('causer_type', 'App\Models\Category')->first();
                $categoryList->slug = $all_slug->slug;
                foreach($categoryList->childs as $child) {
                    $all_slug_child = AllSlug::where('causer_id',$child->id)->where('causer_type', 'App\Models\Category')->first();
                    $child->slug = $all_slug_child->slug;
                }
            }
            $view->with('categoryLists',$categoryLists);
        });

        view()->composer([
            'layouts.affiliate','layouts.service_affiliate','affiliates.includes.headers','affiliates.includes.headers_service_affiliate','affiliates.includes.nav','affiliates.includes.footer','affiliates.includes.oneclick-modal','affiliates.home',
            'affiliates.brands','affiliates.brand_products','affiliates.categories','affiliates.products','affiliates.product_detail','affiliates.search_products',
            'affiliates.cart','affiliates.checkout_login','affiliates.checkout_address','affiliates.payment_options',
            'affiliates.profile','affiliates.myorders','affiliates.account_settings','affiliates.delete_account','affiliates.terms_conditions','affiliates.contact','affiliates.pay_success'
        ], function ($view) {
            $causer_type = 'App\Models\Affiliate';
            $affiliate_slug = AllSlug::where('causer_id',session('kerala_h_m_affiliate'))->where('causer_type',$causer_type)->first();
            $view->with('affiliate_slug',$affiliate_slug->slug);
        });

        view()->composer([
            'affiliates.includes.headers','affiliates.includes.headers_service_affiliate','affiliates.includes.footer','affiliates.includes.transfer-payment-modal','affiliates.home','affiliates.product_detail','layouts.affiliate','layouts.service_affiliate',
            'affiliates.about','affiliates.contact','affiliates.payment_options','affiliates.pay_success','affiliates.includes.oneclick-modal'
        ], function ($view) {
            $affiliate = Affiliate::find(session('kerala_h_m_affiliate'));
            $view->with('affiliate',$affiliate);
        });

        view()->composer(['pages.home'], function ($view) {

        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
