<header id="khm-navbar" class="top-header">
    <div class="container">
        <div style="padding:5px 0;">
            <div id="scroll-hide-top" class="row row1 ">
                <ul class="pull-right hidden-xs">
                    @if(!empty($affiliate->contact_email))
                        <li class="upper-links"><a class="links" href="mailto:{{ $affiliate->contact_email }}" ><i class="fa fa-envelope"></i>&nbsp;&nbsp;{{ $affiliate->contact_email }}</a></li>
                    @endif
                    <li class="upper-links" style="padding-right: 12px;"> @if(!empty($affiliate->contact_whatsapp)) <a class="links" href="https://api.whatsapp.com/send?phone=91{{ str_replace(' ','',$affiliate->contact_whatsapp) }}" target="_blank"><i class="fa fa-whatsapp"></i></a>&nbsp;&nbsp; @endif @if(!empty($affiliate->contact_phone)) <a class="links" href="tel:+91{{ $affiliate->contact_phone }}" target="_blank"><i class="fa fa-phone"></i> {{ $affiliate->contact_phone }}</a> @endif</li>

                </ul>
            </div>
            <div id="scroll-device" class="row row2">
                <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12" style="padding-right: 7px;">
                    <h2 style="margin:0px;">
                        <span class="smallnav menu">
                            <a href="{{ route('all.slug',$affiliate_slug) }}" > <img style="margin-top: -8px;" class="khm_logo" src="{{ empty($affiliate->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Affiliate::FILE_DIRECTORY .  '/' . $affiliate->image['original']) }}" width="160" alt="{{ $affiliate->user->name }}"/></a>
                            @if (Auth::guard('customer')->guest())
                                {{-- <small class="pull-right" style="padding-top:2px; padding-right:5px;"><a class="small" data-toggle="modal" href="#myLoginModal" style="font-size: 14px;">Login</a></small>&nbsp;&nbsp; --}}
                                <small class="pull-right" style="padding-top:2px; padding-right:5px;"><a class="small" data-toggle="modal" href="{{ route('customer.redirect.gmail') }}" style="font-size: 14px;">Login</a></small>&nbsp;&nbsp;
                            @else
                                <li class="dropdown pull-right" style="padding-right: 5px; line-height: 25px;"><a class="links" href="{{ route('customer.logout') }}" style="font-size: 12px; font-weight: normal;" onclick="event.preventDefault(); document.getElementById('logout-form2').submit();" >
                                        Logout
                                    </a>
                                    <form id="logout-form2" action="{{ route('customer.logout') }}" method="POST" style="display: none;" autocomplete="off">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                                <li class="dropdown pull-right" style="padding-right: 5px; line-height: 25px;"><a class="links" href="#" style="font-size: 12px; font-weight: normal;" >My Account</a><i class="fa fa-chevron-down" style="color:rgba(256,256,256,.75); font-size: 10px;" ></i>
                                    <ul class="dropdown-menu">
                                        <li class="profile-li"><a class="profile-links" href="#">Welcome {{ !(empty(Auth::guard('customer')->user()->customer_detail->name)) ? Auth::guard('customer')->user()->customer_detail->name : Auth::guard('customer')->user()->phone }}</a></li>
                                        <li class="profile-li"><a class="profile-links" href="{{ route('affiliate.profile',$affiliate_slug) }}">My Profile</a></li>
                                        <li class="profile-li"><a class="profile-links" href="{{ route('affiliate.myorders',$affiliate_slug) }}">My Orders</a></li>
                                        <li class="profile-li"><a class="profile-links" href="{{ route('affiliate.settings.account',$affiliate_slug) }}">Account Settings</a></li>
                                    </ul>
                                </li>


                            @endif
                        </span>
                    </h2>
                    <div id="scroll-hide-top" class="row row1 ">
                        <ul class="hidden-lg hidden-sm hidden-md smallview pull-left" style="padding-right: 10px;">
                            @if(!empty($affiliate->city))<li class="upper-links "><a class="links" href="#" style="padding-left: 9px;font-size: 15px;" > {{ $affiliate->city }}</a> </li>@endif

                        </ul>
                        <ul class="hidden-lg hidden-sm hidden-md smallview pull-right">
                            {{-- @if(!empty($affiliate->contact_email))
                                <li class="upper-links" style="padding-right: 0;"><a class="links" href="mailto:{{ $affiliate->contact_email }}" ><i class="fa fa-envelope"></i></a></li>
                            @endif --}}
                            <li class="upper-links" style="padding-right: 19px;"> @if(!empty($affiliate->contact_whatsapp)) <a class="links" href="https://api.whatsapp.com/send?phone=91{{ str_replace(' ','',$affiliate->contact_whatsapp) }}" target="_blank"><i class="fa fa-whatsapp"></i></a>&nbsp;&nbsp; @endif @if(!empty($affiliate->contact_phone)) <a class="links" href="tel:+91{{ $affiliate->contact_phone }}" target="_blank">{{ $affiliate->contact_phone }}</a> @endif</li>
                            {{-- <i class="fa fa-phone"></i>  --}}
                        </ul>
                    </div>
                    <h1 style="margin:0; margin-top: -13px;">
                        <span class="largenav">
                            <a href="{{ route('all.slug',$affiliate_slug) }}"><img class="khm_logo" src="{{ empty($affiliate->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Affiliate::FILE_DIRECTORY .  '/' . $affiliate->image['original']) }}" width="160" alt="{{ $affiliate->user->name }}" />
                                @if(!empty($affiliate->city))<p style="font-size: 17px;color: white;">{{ $affiliate->city }}</p>@endif
                            </a>
                      </span>
                    </h1>
                </div>
                <div class="khm-navbar-search smallsearch col-lg-6 col-md-4 col-sm-4 col-xs-10">
                    <div class="row">
                        <form action="{{ route('affiliate.product.search_post',$affiliate_slug) }}" method="POST" name="form-search" id="form-search" autocomplete="off">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3" style="padding-right: 0; ">
                                    <select class="khm-navbar-input mobile_search left" name="cat_id" id="cat_id" style="border-bottom: 2px solid #A6A5A5; border-radius: 3px 0 0 0;" > {{-- onChange="getsubCategory(this.value);" --}}
                                        <option value="">All</option>
                                        @foreach($mainCategories as $index => $mainCategory)
                                            <option value="{{ $mainCategory->id }}" {{ (isset($selected_cat)) && ($selected_cat==$index) ? 'selected' : '' }}>{{ $mainCategory->name }}</option>
                                        @endforeach
                                    </select>
                                    {{-- <select class="khm-navbar-input mobile_search" name="subcat_id" id="subcat_id" style="border-radius: 0 0 0 3px;">
                                        <option value="">Select Sub Category</option>
                                    </select> --}}
                                </div>
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 big_search" style="padding-left: 0; padding-right: 0;">
                                    <input class="khm-navbar-input search_term" type="text" placeholder="Search for products..." name="term" id="term" value="{{ isset($term) ? $term : '' }}" style="width: 100%;">
                                </div>
                                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2" style="padding-left: 0;">
                                    <button type="submit" class="khm-navbar-button" style="width: 100%;">
                                        <i class="fa fa-search" style="font-size: 18px; color:#000;"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="search_loading" style="display: none;">
                                <img src="{{ asset('images/search-loading.gif') }}" >
                                <img src="{{ asset('images/search-loading.gif') }}" >
                            </div>

                            <div class="divResult" style="display: none">

                            </div>

                        </form>

                    </div>
                </div>
                <ul class="col-lg-2 col-md-3 col-sm-2 col-xs-2 hidden-xs" style="text-align: right; padding-top: 10px;">
                    @if (Auth::guard('customer')->guest())
                        {{-- <li class="upper-links"><a class="links signup-font" data-toggle="modal" href="#myLoginModal">Login</a></li> --}}
                        <li class="upper-links"><a class="links signup-font" href="{{ route('customer.redirect.gmail') }}">Login</a></li>

                    @else
                        <li class="upper-links dropdown"><a class="links" href="#">My Account</a>
                            <i class="fa fa-chevron-down" style="color:rgba(256,256,256,.75); " ></i>
                            <ul class="dropdown-menu">
                                <li class="profile-li"><a class="profile-links" style="color:#2874f0;">Welcome {{ !(empty(Auth::guard('customer')->user()->customer_detail->name)) ? Auth::guard('customer')->user()->customer_detail->name : Auth::guard('customer')->user()->phone }}</a></li>
                                <li class="profile-li"><a class="profile-links" href="{{ route('affiliate.profile',$affiliate_slug) }}">My Profile</a></li>
                                <li class="profile-li"><a class="profile-links" href="{{ route('affiliate.myorders',$affiliate_slug) }}">My Orders</a></li>
                                <li class="profile-li"><a class="profile-links" href="{{ route('affiliate.settings.account',$affiliate_slug) }}">Account Settings</a></li>
                            </ul>
                        </li>
                        <li class="upper-links">
                            <a class="links" href="{{ route('customer.logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <form id="logout-form" action="{{ route('customer.logout') }}" method="POST" style="display: none;" autocomplete="off">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    @endif
                </ul>
                <div class="cart smallnav col-md-2 col-sm-1 col-xs-1" style="margin-top: 3px;">
                    <div class="row">
                        <a href="{{ route('affiliate.product.cart',$affiliate_slug) }}" style="margin-top: 23px; position:relative;" class="pull-right">
                            <i class="fa fa-shopping-cart" style="font-size: 22px; color:#fff;"></i>
                            <span id="total_quantity_cart2" style="position: absolute;top: -9px;right: -2px;" class="label label-danger">{{ Cart::getTotalQuantity() }}</span>
                        </a>
                    </div>
                </div>
                <div class="cart largenav col-lg-2 col-md-2 col-sm-3" style="padding-right: 7px;">
                    <a href="{{ route("affiliate.product.cart",$affiliate_slug) }}" class="cart-button pull-right">
                        <i class="fa fa-shopping-cart" style="font-size: 18px;"></i>&nbsp;&nbsp;My Cart
                        <span id="mycart-total-quantity" class="item-number "><span id="total_quantity_cart">{{ Cart::getTotalQuantity() }}</span></span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</header>
@push('page_scripts')
<script>
    $(document).ready(function() {
        var container = $("#mySidenav");

        $('.menu-bars').click(function(e){
            e.stopPropagation();
        });

        $('body,html').click(function(e){

            if (!container.is(e.target) && container.has(e.target).length === 0) {
                container.attr("style", "width:0px;");
            }

        });

        $(".search_term").keyup(function()
        {
            var inputSearch = $(this).val();
            var inputdepartment = $('#cat_id').val();
            var dataString = {term: inputSearch, cat_id: inputdepartment, _token:$("input[name='_token']").val()};
            var $divResult = $(".divResult");

            $divResult.empty();
            if(inputSearch!='')
            {
                $('#search_loading').show();

                $.ajax({
                    type: "POST",
                    url: "{{ route('affiliate.product.search.on_type',[$affiliate_slug]) }}",
                    data: dataString,
                    cache: false,
                    success: function(data){
                        if(data != '') {
                            jQuery(".divResult").fadeIn();
                            $('#search_loading').hide();
                            $divResult.html(data);
                        }else {
                            $('#search_loading').hide();
                            jQuery(".divResult").fadeOut();
                            $divResult.empty().html('<p>Item not available</p>');
                        }
                    }
                });

            }else {
                $('#search_loading').hide();
                jQuery(".divResult").fadeOut();
                $divResult.empty().html('<p>Item not available</p>');
            }

            return false;
        });

        jQuery(".divResult").on("click",function(e){
            var $clicked = $(e.target);
            var $name = $clicked.find('.name').html();
            var decoded = $("<div/>").html($name).text();
            $('#term').val(decoded);
        });
        jQuery(document).on("click", function(e) {
            var $clicked = $(e.target);
            if (! $clicked.hasClass("search")){
                jQuery(".divResult").fadeOut();
            }
        });

    });

</script>
{{-- <script>
    function getsubCategory(val,subcat) {
        if (typeof subcat === "undefined" || subcat === null) {
            subcat = "";
        }
        if(subcat == "") {
            var formdata = { category_id: val };
        }else {
            formdata = {
                category_id: val,
                subcategory_id: subcat
            };
        }

        $('#jq-loader').show();
        $.ajax({
            type: "POST",
            url: "{{ route('affiliate.product.list.subcategories',$affiliate_slug) }}",
            data:formdata,
            success: function(data){
                $("#subcat_id").html(data);
                $('#jq-loader').hide();
            }
        });
    }

</script> --}}
@endpush
