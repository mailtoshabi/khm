<header id="khm-navbar" class="top-header">
    <div class="container">
        <div style="padding:5px 0;">
            <div id="scroll-hide-top" class="row row1 ">
                <ul class="pull-left hidden-xs" style="padding-left: 0;">
                    <li class="upper-links class_offer" style="margin-left: 15px;"><a class="links" href="{{ route('brands') }}" > Brands</a> </li>
                    <li class="upper-links class_offer"><a class="links" href="{{ route('services') }}" > Services</a> </li>
                    <li class="upper-links class_offer"><a class="links" href="{{ route('affiliates') }}" > Stores</a> </li>
                </ul>
                <ul class="pull-right hidden-xs">
                    @if(!empty(Utility::settings('admin_email')))
                        <li class="upper-links"><a class="links" href="mailto:{{ Utility::settings('admin_email') }}" ><i class="fa fa-envelope"></i>&nbsp;&nbsp;{{ Utility::settings('admin_email') }}</a></li>
                    @endif
                    @if(!empty(Utility::settings('admin_phone')))
                        <li class="upper-links"><a class="links" target="_blank" href="tel:+91{{ Utility::settings('admin_phone') }}"><i class="fa fa-phone"></i>&nbsp;&nbsp;{{ Utility::settings('admin_phone') }}</a>&nbsp;&nbsp;<a class="links" href="https://api.whatsapp.com/send?phone=91{{ str_replace(' ','',Utility::settings('admin_phone')) }}" target="_blank"><i class="fa fa-whatsapp"></i></a></li>
                    @endif
                </ul>

            </div>

            <div id="scroll-device" class="row row2">
                <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12" style="/*padding-right: 7px;*/">
                    <h2 style="margin:0px;">
                        <span class="smallnav menu">
                            <a href="{{ route('index') }}" > <img style="margin-top: -8px;" class="khm_logo" src="{{ asset('images/logo.png') }}" width="160" alt="kerala health mart"/></a>
                            @if (Auth::guard('customer')->guest())
                                <small class="pull-right" style="padding-top:2px; padding-right:5px;"><a class="small" data-toggle="modal" href="#myLoginModal" style="font-size: 14px;">Login & Signup</a></small>&nbsp;&nbsp;
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
                                        <li class="profile-li"><a class="profile-links" href="{{ route('profile') }}">My Profile</a></li>
                                        <li class="profile-li"><a class="profile-links" href="{{ route('myorders') }}">My Orders</a></li>
                                        <li class="profile-li"><a class="profile-links" href="{{ route('settings.account') }}">Account Settings</a></li>
                                    </ul>
                                </li>
                            @endif
                        </span>
                    </h2>
                    <div id="scroll-hide-top" class="row row1 ">
                        <ul class="hidden-lg hidden-sm hidden-md smallview pull-left" style="padding-right: 10px;">
                            <li class="upper-links" style="padding-left: 15px;"><a class="links" href="{{ route('brands') }}" > Brands</a> </li>
                            <li class="upper-links"><a class="links" href="{{ route('services') }}" > Services</a> </li>
                            <li class="upper-links"><a class="links" href="{{ route('affiliates') }}" > Stores</a> </li>
                        </ul>
                        <ul class="hidden-lg hidden-sm hidden-md smallview pull-right">
                            @if(!empty(Utility::settings('admin_email')))
                                <li class="upper-links" style="padding-right: 0px;"><a class="links" href="mailto:{{ Utility::settings('admin_email') }}" ><i class="fa fa-envelope"></i></a></li>
                            @endif
                            @if(!empty(Utility::settings('admin_phone')))
                                <li class="upper-links" style="padding-right: 19px;"><a class="links" href="https://api.whatsapp.com/send?phone=91{{ str_replace(' ','',Utility::settings('admin_phone')) }}" target="_blank"><i class="fa fa-whatsapp"></i></a>&nbsp;&nbsp;<a class="links" href="tel:+91{{ Utility::settings('admin_phone') }}" target="_blank"><i class="fa fa-phone"></i>&nbsp;&nbsp;{{ Utility::settings('admin_phone') }}</a></li>
                            @endif
                        </ul>
                    </div>
                    <h1 style="margin:0px;">
                        <span class="largenav">
                            <a href="{{ route('index') }}"><img class="khm_logo" src="{{ asset('images/logo.png') }}" width="160" alt="kerala health mart"/></a>
                      </span>
                    </h1>
                </div>
                <div class="khm-navbar-search smallsearch col-lg-6 col-md-4 col-sm-4 col-xs-10">
                    <div class="row">
                        <form action="{{ route('services') }}" method="POST" name="form-search" id="form-search" autocomplete="off">
                            {!! csrf_field() !!}
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding-right: 0;">
                                    <select class="khm-navbar-input mobile_search left" name="type_id" id="type_id" style="border-bottom: 2px solid #A6A5A5; border-radius: 3px 0 0 0;">
                                        <option value="">Select Type</option>
                                        @foreach($types as $id => $clinic_type)
                                            <option {{ isset($selected_type) && ($selected_type == $id) ? 'selected' : '' }} value="{{ $id }}">{{ $clinic_type }}</option>
                                        @endforeach
                                    </select>
                                    <select class="khm-navbar-input mobile_search" name="district_id" id="district_id" style="border-radius: 0 0 0 3px;">
                                        <option value="">Select District</option>
                                        @foreach($districts as $id => $district)
                                            <option {{ isset($selected_district) && ($selected_district == $id) ? 'selected' : '' }} value="{{ $id }}">{{ $district }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-10 big_search" style="padding-right: 0;">
                                    <input class="khm-navbar-input search_term" type="text" placeholder="Type City here" name="term" id="term" value="{{ isset($term) ? $term : '' }}" style="width: 100%; /*margin-left: 3px;*/">
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
                        <li class="upper-links"><a class="links signup-font" data-toggle="modal" href="#myLoginModal">Login</a></li>

                    @else
                        <li class="upper-links dropdown"><a class="links" href="#">My Account</a>
                            <i class="fa fa-chevron-down" style="color:rgba(256,256,256,.75); " ></i>
                            <ul class="dropdown-menu">
                                <li class="profile-li"><a class="profile-links" style="color:#2874f0;">Welcome {{ !(empty(Auth::guard('customer')->user()->customer_detail->name)) ? Auth::guard('customer')->user()->customer_detail->name : Auth::guard('customer')->user()->phone }}</a></li>
                                <li class="profile-li"><a class="profile-links" href="{{ route('profile') }}">My Profile</a></li>
                                <li class="profile-li"><a class="profile-links" href="{{ route('myorders') }}">My Orders</a></li>
                                <li class="profile-li"><a class="profile-links" href="{{ route('settings.account') }}">Account Settings</a></li>
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
                        <a href="{{ route('product.cart') }}" style="margin-top: 23px; position:relative;" class="pull-right">
                            <i class="fa fa-shopping-cart" style="font-size: 22px; color:#fff;"></i>
                            <span id="total_quantity_cart2" style="position: absolute;top: -9px;right: -2px;" class="label label-danger">{{ Cart::getTotalQuantity() }}</span>
                        </a>
                    </div>
                </div>
                <div class="cart largenav col-lg-2 col-md-2 col-sm-3" style="padding-right: 7px;">
                    <a href="{{ route('product.cart') }}" class="cart-button pull-right">
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

    });

</script>
<script>
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
            url: "{{ route('product.list.subcategories') }}",
            data:formdata,
            success: function(data){
                $("#subcat_id").html(data);
                $('#jq-loader').hide();
            }
        });
    }

</script>
@endpush
