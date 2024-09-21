@extends('layouts.app')
@section('title',!empty($product->site_title) ? $product->site_title : $product->name)
@section('description',!empty($product->site_description) ? $product->site_description : strip_tags(str_replace("&nbsp;"," ",Utility::productShortDescription($product->id))))
@section('keywords',!empty($product->site_keywords) ? $product->site_keywords : $product->name)
@section('og_content')
    <meta property="og:title" content="{{ $product->name }} - Buy online at {{ config('app.name') }}"/>
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="{{ route('all.slug',$slug) }}"/>
    <meta property="og:site_name" content="{{ config('app.name') }}"/>
    <meta property="og:description" content="{{ strip_tags(Utility::productShortDescription($product->id)) }}"/>
    <meta property="og:image" content="{{ empty($product->image) ? asset('images/no-image.jpg') : asset($product->image) }}"/>
    <meta property="og:image:width" content="206"/>
    <meta property="og:image:height" content="224"/>
@endsection
@section('content')
    <div class="wrapper">
        <div class="container">
            <div class="card">
                <div class="container-fliud">
                    <div class="wrapper row">
                        <div class="preview col-md-5">

                            <div class="preview-pic tab-content prod_detail_border_img" >
                                <div class="figure">
                                    <img src="{{ empty($product->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . Utility::KHM_BIG_IMAGE_SIZE . '_' . $product->image) }}" style="border-bottom: 1px solid #f0f0f0;" />
                                    {{-- <iframe width="560" height="315" src="https://www.youtube.com/embed/pBRVAdHhIsg?si=M0dlnCAJp9TeQiTL" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe> --}}
                                </div>
                                @if(!empty($product->images))
                                    <div class="thumbnails">
                                        <img src="{{ empty($product->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $product->image) }}" data-image="{{ empty($product->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . Utility::KHM_BIG_IMAGE_SIZE . '_' . $product->image) }}" style="border-bottom: 1px solid #f0f0f0;" />
                                        @foreach($product->images as $otherImage)
                                            <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $otherImage) }}" data-image="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' .  Utility::KHM_BIG_IMAGE_SIZE . '_' . $otherImage) }}" alt="Kerala Health Mart" />
                                        @endforeach
                                    </div>
                                @endif
                                <!--SOCIAL START-->
                                <div class="social-cont">
                                    <div class="col-md-12">
                                        <div class="social_share">
                                            <span class="text-right"><p>Share <i class="fa fa-share"></i></p></span>
                                            <a class="social-icon facebook" target="blank" data-toggle="tooltip" data-placement="top" title="Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}&amp;src=sdkpreparse">
                                                <i class="fa fa-facebook"></i>
                                            </a>

                                            <a class="social-icon twitter" target="blank" data-toggle="tooltip" data-placement="top" title="Twitter" href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}">
                                                <i class="fa fa-twitter"></i>
                                            </a>
                                            <a class="social-icon email" target="blank" data-toggle="tooltip" data-placement="top" title="EMail" href="mailto:?subject=I wanted you to see the product {{ $product->name }} in {{ config('app.name') }}&amp;body=Check out this product {{ Request::fullUrl() }}.">
                                                <i class="fa fa-envelope-o"></i>
                                            </a>
                                            <a class="social-icon whatsapp" target="blank" data-toggle="tooltip" data-placement="top" title="Whatsapp" href="https://api.whatsapp.com/send?text=Checkout the product {{ str_replace("&"," and ",$product->name) }} in {{ urlencode(Request::fullUrl()) }}">
                                                <i class="fa fa-whatsapp"></i>
                                            </a>

                                        </div>
                                    </div>

                                    <div class="clearfix">

                                    </div>
                                </div>
                                <!--SOCIAL END-->
                            </div>

                        </div>
                        <div class="details col-md-7">
                            <div id="form-product-detail">
                            <div class="prod_detail_border">
                            <h3 class="product-title">{{ $product->name }} </h3>
                            @if(!empty(($product->brand())))
                                <p><a class="brand" href="{{ route('all.slug',$product->brand()->slug) }}" target="_blank">{{ $product->brand()->name }}</a></p>
                            @endif
                            <p ><span id="prod_stock"></span> <span id="stock_notify" style="display: none;"><a href="{{ route('contact') }}">Notify us</a></span> &nbsp;<button id="one_click" class="one_click btn " type="button" @if(Auth::guard('customer')->guest()) data-toggle="modal" href="#oneClickModal" @endif style="background: #259B76;"><i class="fa fa-check-circle"></i> Enquire Now</button></p>
                            @if(!empty($product->brochure))
                                <a class="product-brochure" href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY_BROCHURE .  '/' . $product->brochure) }}" target="_blank">Download Product Brochure</a>
                            @endif
                            {{--<h5 class="price">Brand: <a href="">{{ $product->brands }}</a></h5>--}}
                            <h5 class="sizes">Type/Size &nbsp;&nbsp;
                                <select name="type_size" id="type_size" class="selectpicker change_type_size">
                                    @foreach($product->product_types as $type_size)
                                        <option value="{{ $type_size->id }}" {{ $product->min_size() == $type_size->id ? 'selected' : '' }} >{{ $type_size->name }}</option>
                                    @endforeach
                                </select>
                            </h5>
                            <h5 class="colors">Quantity &nbsp;&nbsp;
                                <input type="text" class="form-control product_quantity change_quantity" name="quantity" id="quantity" value="{{ $product->min_quantity() }}" />
                                &nbsp;&nbsp;<small>{{ $product->unit_om }}</small>
                                {{--&nbsp;&nbsp;&nbsp;<a data-toggle="tooltip" class="red-tooltip" title="Grab more discount with more quantity"><i class="fa fa-info-circle" style="color:#666;font-size: 15px; cursor: pointer;"></i></a>--}}
                                @if ((Auth::guard('customer')->user()) && (Auth::guard('customer')->user()->is_access))
                                    <a id="discount_details" class="red-tooltip" ><i class="fa fa-info-circle" style="color:#666;font-size: 15px; cursor: pointer;"></i></a>
                                @endif
                            </h5>
                            <p style="padding-top: 15px;">
                            <span class="price">
                                <span id="special_price">Price</span>&nbsp;&nbsp;<i class="fa fa-inr"></i><span id="item_price">{{ str_pad($product->min_price(),2,"0") }}</span>
                                <span class="mrp" ><del id="mrp_price"></del></span>
                                <span class="discount_perc text-success" id="discount_perc"></span>
                                <a id="price_details" class="red-tooltip" ><i class="fa fa-info-circle" style="color:#666;font-size: 15px; cursor: pointer;"></i></a>
                            </span>
                            </p>

                            <div class="row" style="padding:0px;">
                                <div id="product_detail_btn" class="action">
                                    <div class="col-md-4 col-sm-6 " style="">
                                        <button id="buy-now" class="add-to-cart btn btn-default add_cart" data-cart="1" type="button" @if(empty($product->prod_stock) || ($product->prod_stock==0)) disabled @endif><span class="fa fa-heart"></span> Buy Now</button>
                                    </div>

                                    <div class="col-md-4 col-sm-6 ">
                                        <button id="add-to-cart" class="add-to-cart btn btn-default add_cart" data-cart="0" type="button" disabled="disabled"><span class="fa fa-shopping-cart"></span> add to cart</button>
                                    </div>
                                </div>
                            </div>

                                <div class="row" style="padding-top: 10px;">
                                    <div class="col-md-4 col-sm-6  prod_dtl_align">
                                        <a id="go-to-cart" href="{{ route("product.cart") }}" >Go to Cart</a>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                                @if(!empty($product->description))
                                    <div class="prod_detail_border" style="margin-top: 20px; min-height: 200px;">
                                        {{--<h4>Product Description</h4>--}}
                                        <div class="product-description">{!! $product->description !!} </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($product->video))
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page_title">Watch the Video</h3>
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $product->youtube_code }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>

                    </div>
                </div>
            @endif


            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page_title">Related Products</h3>

                    @foreach($relatedProducts as $relatedProduct)
                        <div class="product_container col-md-3 col-lg-2 col-xs-6 col-sm-4">
                            <a href="{{ route('all.slug',$relatedProduct->slug) }}" class="product_item" title="{{ $relatedProduct->name }}">
                                <img src="{{ empty($relatedProduct->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $relatedProduct->image) }}" class="img-responsive">
                                <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($relatedProduct->name, $limit = 20, $end = ' ..') }}</p>
                                <p class="text-center single_prod_price"><i class="fa fa-inr"></i>{{ $relatedProduct->min_price() }} <del class="single_prod_mrp">{{ $relatedProduct->min_mrp() }}</del> <span style="font-size:12px;" class="text-success hidden-xs hidden-sm">{{ !empty($relatedProduct->min_price()) && !empty($relatedProduct->min_mrp()) ? round((($relatedProduct->min_mrp() - $relatedProduct->min_price())/$relatedProduct->min_mrp())*100,0).'% off' : '' }}</span><span style="font-size:12px;" class="text-success hidden-md hidden-lg row"><span class="col-md-12">{{ !empty($relatedProduct->min_price()) && !empty($relatedProduct->min_mrp()) ? round((($relatedProduct->min_mrp()-$relatedProduct->min_price())/$relatedProduct->min_mrp())*100,0).'% off' : '' }}</span></span></p>

                            </a>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>

    </div>
    <!-- Wrapper -->.
@endsection

@push('page_html')
    @include('partial.oneclick-modal',['product_id' => $product->id])
@endpush

@push('page_style')

    <!-- Demo styles -->
    <style>

        .thumbnails {
            /* display: flex; */
            /* flex-direction: column; */
            width: auto;
            /* height: 200px; */
            /* position: absolute;
            left: 10%;
            top: 5%; */
        }

        .thumbnails img {
            /* margin: 0 20px 20px; */
            opacity: 1;
            transition: 0.3s;
            float: left;
            width: 20%;
        }

        img {
            max-width: 100%;
            max-height: 100%;
        }

        .mainDiv {
            /* padding: 40px 0; */
            position: relative;
            /* flex-direction: row; */
        }

        .figure {
            /* max-width: 800px;
            margin: 0 auto 40px;
            position: absolute;
            left: 28%;
            top: 5%; */
        }

        .figure img {
            /* max-width: 100%;
            min-width: 100%;
            height: 650px;
            width: 650px; */
        }

        iframe {
            width: 100%;
        }
    </style>
@endpush

@push('page_scripts')
<script>
    // When webpage will load, everytime below
    // function will be executed
    $(document).ready(function () {

        // If user clicks on any thumbanil,
        // we will get it's image URL
        $('.thumbnails img').on({
            click: function () {
                let thumbnailURL = $(this).attr('src');

                // Replace main image's src attribute value
                // by clicked thumbanail's src attribute value
                $('.figure img').fadeOut(200, function () {
                    $(this).attr('src', thumbnailURL);
                }).fadeIn(200);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {

        refreshPrice('yes');
        $('#form-product-detail').on('keyup','.change_quantity', function() {
            setTimeout(
                    function()
                    {
                        refreshPrice('yes');
                    }, 1200);
        }).on('change','.change_type_size', function() {
            refreshPrice('yes');
        });

        $(document).on('refresh.complete', function (e,data) {
            var special_price = data.price;
            if(data.type.mrp != 0) {
                var mrp_price = data.type.mrp;
            }else {
                mrp_price = data.price;
            }
            var price_disc = data.type.discount;
            var price_details = "Special Price : <i class='fa fa-inr'></i>" + special_price + "<br> MRP Price : <i class='fa fa-inr'></i>" + mrp_price  + "<br> Discount : " + price_disc + "%" ;
            $('#price_details').tooltip("destroy").tooltip({title: price_details, html:true});
            $('#discount_details').tooltip("destroy").tooltip({title: data.prices, html:true});
        });

        $(".add_cart").click(function(e){
            e.preventDefault();

            var goCart = $(this).data('cart');

            var product_id = "{{ $product->id }}";
            var product_name = "{{ $product->name }}";
            var product_price = parseFloat($('#item_price').text());
            var product_quantity = parseInt($('#quantity').val());
            var product_type = $('#type_size').val();
            var product_image = "{{ $product->image }}";
            var url = "{{ route('product.add_cart') }}";
            var cartData = {id: product_id, name: product_name, price: product_price, quantity: product_quantity, type: product_type, product_image:product_image};
            var ladda = Ladda.create(document.querySelector("#add-to-cart"));
            var ladda2 = Ladda.create(document.querySelector("#buy-now"));
            ladda.start();
            ladda2.start();
            $.ajax({
                type: "GET",
                url: url,
                data: cartData,
                success: function (data) {
                    $('#mycart-total-quantity').text(data.cart_total);
                    $('#total_quantity_cart2').text(data.cart_total);
                    $('#total_quantity_cart3').text(data.cart_total);
                    /*refreshPrice('no');*/

                    if(data.stock_status==0) {

                        $('#prod_stock').text('Out of Stock')
                                .addClass('text-danger')
                                .removeClass('text-success');
                        $('#stock_notify').show();
                        ladda.stop();
                        ladda2.stop();
                        $('#add-to-cart').attr('disabled','disabled');
                        $('#buy-now').attr('disabled','disabled');
                        $('#msg-bg').show().html('<p class="text-danger">Out of stock or Less in stock..!</p>');
                        $("#msg-bg").fadeOut( 2500, function() {
                            // Animation complete.
                        });
                    }else {
                        if(goCart == 1) {
                            window.location.replace("{{ route('product.cart') }}");
                        }else {
                            $('#prod_stock').text('In Stock')
                                    .addClass('text-success')
                                    .removeClass('text-danger');
                            $('#stock_notify').hide();
                            ladda.stop();
                            ladda2.stop();
                            $('#add-to-cart').removeAttr('disabled');
                            $('#buy-now').removeAttr('disabled');
                            $('#msg-bg').show().html('<p class="text-success">Item added to cart..!</p>');
                            $("#msg-bg").fadeOut( 2500, function() {
                                // Animation complete.
                            });
                        }

                    }

                },
                error : function(jqXHR, textStatus, errorThrown) {

                },
                complete : function(jqXHR, textStatus) {

                }
            });

        });
    });

    function refreshPrice(ld) {

        var product_id = '{{ $product->id }}';
        var type_size = parseInt($('#type_size').val());
        var quantity = parseInt($('#quantity').val());
        var url = '{{route('product.get_price')}}';
        var formData = {product_id: product_id,type_size: type_size, quantity: quantity};

        if(ld=='no') {}else {
            var ladda = Ladda.create(document.querySelector("#add-to-cart"));
            var ladda2 = Ladda.create(document.querySelector("#buy-now"));
        }

        if(isNaN(quantity)) {
            quantity = 0;
        }

        if(quantity != '' || quantity !=0) {
            if(ld=='no') {}else {
                ladda.start();
                ladda2.start();
            }
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function (data) {
                    $('#item_price').text(data.price);
                    if(data.type.mrp != 0) {
                        $('#mrp_price').text(data.type.mrp);
                        $('#discount_perc').text(data.type.discount+'% off');
                    }

                    $('#quantity').val(data.quantity);

                    if(ld=='no') {}else {
                        ladda.stop();
                        ladda2.stop();
                    }
                    if(data.type.stock_status==0) {
                        $('#prod_stock').text('Out of Stock')
                        .addClass('text-danger')
                        .removeClass('text-success');
                        $('#stock_notify').show();
                        $('#add-to-cart').attr('disabled','disabled');
                        $('#buy-now').attr('disabled','disabled');
                    }else {
                        $('#prod_stock').text('In Stock')
                                .addClass('text-success')
                                .removeClass('text-danger');
                        $('#stock_notify').hide();
                        $('#add-to-cart').removeAttr('disabled');
                        $('#buy-now').removeAttr('disabled');
                    }

                    /*$('#type_size').focus();*/
                    $(document).trigger('refresh.complete', data);
                }
            });
        }
    }

    function refreshStock() {
        var product_id = '{{ $product->id }}';
        var type_size = parseInt($('#type_size').val());
        var quantity = parseInt($('#quantity').val());
        var url = '{{route('product.get_stock')}}';
        var formData = {product_id: product_id,type_size: type_size, quantity: quantity};


        if(quantity != '') {
            $.ajax({
                type: "GET",
                url: url,
                data: formData,
                success: function (data) {
                    $('#item_price').text(data.price);
                    /*$('#type_size').focus();*/
                }
            });
        }
        else {

        }

    }
</script>
@if(Auth::guard('customer')->guest())
@else
<script>

    $(document).ready(function(){
        $('#one_click').click(function() {
            var showData = '<p class="text-success">Thank you for choosing Kerala Healthmart. Your purchase order is confirmed. Our executive will contact you soon.<br> </p>';
            var product_id =  '{{ $product->id }}';
            var phone =  '{{ Auth::guard('customer')->user()->phone }}';

            var url = '{{route('product.oneclick.purchase')}}';
            var formData = {product_id: product_id,phone: phone};

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    success: function (data) {
                        $('#msg-bg').show().html(showData);
                        $("#msg-bg").fadeOut( 12000, function() {
                            // Animation complete.
                        });
                    }
                });



        });
    });

</script>
@endif
@endpush
