@extends('layouts.affiliate')
@section('title',$affiliate->site_title)
@section('description',$affiliate->site_description)
@section('keywords',$affiliate->site_keywords)
@section('og_content')
    <meta property="og:title" content="{{ $affiliate->site_title }}"/>
    <meta property="og:description" content="{{ $affiliate->site_description }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="{{ config('app.website_url') }}/{{ $affiliate_slug }}"/>
    <meta property="og:site_name" content="{{ $affiliate->user->name }}"/>
@endsection
@section('content')
<div class="wrapper">
    @if(!empty(json_decode($banners)))
        <div id="myCarousel" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators hidden-xs hidden-sm">
                <?php $no_banner1 = 1; ?>
                @foreach($banners as $banner)
                    <li data-target="#myCarousel" data-slide-to="{{ $no_banner1-1 }}" class="{{ $no_banner1==1 ? 'active' : '' }}"></li>
                        <?php $no_banner1++; ?>
                @endforeach
            </ol>
            <div class="carousel-inner">
                <?php $no_banner = 1; ?>
                @foreach($banners as $banner)
                    <div class="item {{ $no_banner==1 ? 'active' : '' }}"> <img src="{{ asset($banner->image) }}" class="hm_banner" alt="{{ config('app.name') }}">
                        @if(!empty($banner->link))
                            <div class="container">
                                <div class="carousel-caption">
                                    <p class="hidden-xs hidden-sm"><a class="btn btn-lg btn-primary" href="{{ $banner->link }}" target="_blank" role="button" style="background-color: rgb(66,139,202,.85);">Buy Now</a></p>
                                    <p class="hidden-md hidden-lg"><a class="btn btn-sm btn-primary" href="{{ $banner->link }}" target="_blank" role="button" style="background-color: rgb(66,139,202,.85);">Buy Now</a></p>
                                </div>
                            </div>
                        @endif
                    </div>
                        <?php $no_banner++; ?>
                @endforeach
            </div>
            <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
            <a class="right carousel-control" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
        </div>
    @endif

    <!-- Page Content -->
    <div class="" id="myCarousel2">
        @if(!empty(json_decode($offerProudcts)))
            <div class="corousel-container" data-plugin="khm-corousel" id="dvoffer-corousel" >
                <div class="row">
                    <div class="col-md-12" >
                        <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12">
                            <div class="sm-bg home_product_valign">
                                <h3 class="col-md-12 col-xs-8 text-center home_cat_head">Offer of the day
                                    {{--<small class="text-center" style="padding-bottom: 10px; display: block;">Get before it expires</small>--}}
                                </h3>
                                <div class="col-md-12 col-xs-4 product_name text-center" style="margin-top: 1px;">
                                    <button class="btn btn-primary btn-md hidden-xs hidden-sm" onclick="location.href='{{ route('affiliate.offer.products',[$affiliate_slug]) }}'">View All</button>
                                    <button class="btn btn-primary btn-sm hidden-md hidden-lg" onclick="location.href='{{ route('affiliate.offer.products',[$affiliate_slug]) }}'">View All</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 col-lg-10 col-sm-12 col-xs-12">
                            <div class="carousel slide" id="offer-corousel">
                                <div class="carousel-inner">
                                    <?php $offerprodNo = 1; ?>
                                    @foreach($offerProudcts as $offerProudct)
                                        <div class="item {{ $offerprodNo==1 ? 'active' : '' }}">
                                            <div class="col-md-3 col-lg-3 col-xs-6 col-sm-3 hm_prod_container">
                                                <a href="{{ route('affiliate.all.slug',[$slug, $offerProudct->slug]) }}" class="product_item" title="{{ $offerProudct->name }}">
                                                    <img src="{{ empty($offerProudct->image) ? asset('images/no-image.jpg') : asset($offerProudct->image) }}" class="img-responsive">
                                                    <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($offerProudct->name, $limit = 20, $end = '...') }}</p>
                                                    <p class="text-center single_prod_price"><i class="fa fa-inr"></i>{{ $offerProudct->basicAffiliate()['price'] }} <del class="single_prod_mrp">{{ $offerProudct->basicProduct()['mrp'] }}</del> <span style="font-size:12px;" class="text-success hidden-xs hidden-sm">{{ !empty($offerProudct->basicAffiliate()['price']) && !empty($offerProudct->basicProduct()['mrp']) ? round($offerProudct->basicAffiliate()['off']).'% off' : '' }}</span> <span style="font-size:12px;" class="text-success hidden-md hidden-lg "><span class="">{{ !empty($offerProudct->basicAffiliate()['price']) && !empty($offerProudct->basicProduct()['mrp']) ? round($offerProudct->basicAffiliate()['off']).'% off' : '' }}</span></span></p>
                                                    {{--<p class="text-center"><button class="btn btn-primary btn-xs">View Details</button></p>--}}
                                                </a>
                                            </div>
                                        </div>
                                        <?php $offerprodNo++; ?>
                                    @endforeach
                                </div>
                                <a class="left carousel-control" href="#offer-corousel" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>
                                <a class="right carousel-control" href="#offer-corousel" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
                            </div>

                        </div>
                    </div>
                </div><!--row-->
            </div><!-- dvoffer-corousel -->
        @endif
        @if(!empty(json_decode($featuredProudcts)))
            {{--<div class="corousel-container" data-plugin="khm-corousel" id="dvfeatured-corousel" >
                <div class="row">
                    <div class="col-md-12" >
                        <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12">
                            <div class="sm-bg home_product_valign">
                                <h3 class="col-md-12 col-xs-8 text-center home_cat_head">Featured Products
                                    --}}{{--<small class="text-center" style="padding-bottom: 10px; display: block; font-size:13px;">You may love to buy it..!</small>--}}{{--
                                </h3>
                                <div class="col-md-12 col-xs-4 product_name text-center" style="margin-top: 1px;">
                                    <button class="btn btn-primary btn-md hidden-xs hidden-sm" onclick="location.href='{{ route('affiliate.category.featured',$affiliate_slug) }}'">View All</button>
                                    <button class="btn btn-primary btn-sm hidden-md hidden-lg" onclick="location.href='{{ route('affiliate.category.featured',$affiliate_slug) }}'">View All</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 col-lg-10 col-sm-12 col-xs-12">
                            <div class="carousel slide" id="featured-corousel" data-ride="carousel">
                                <div class="carousel-inner" role="listbox">
                                    <?php $featuredprodNo = 1; ?>
                                    @foreach($featuredProudcts as $featuredProudct)
                                        <div class="item {{ $featuredprodNo==1 ? 'active' : '' }}">
                                            <div class="col-md-3 col-lg-3 col-xs-6 col-sm-3 hm_prod_container">
                                                <a href="{{ route('affiliate.all.slug',[$slug, $featuredProudct->slug]) }}" class="product_item" title="{{ $featuredProudct->name }}">
                                                    <img src="{{ empty($featuredProudct->image) ? asset('images/no-image.jpg') : asset($featuredProudct->image) }}" class="img-responsive">
                                                    <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($featuredProudct->name, $limit = 20, $end = '...') }}</p>
                                                    <p class="text-center single_prod_price"><i class="fa fa-inr"></i>{{ $featuredProudct->basicAffiliate()['price'] }} <del class="single_prod_mrp">{{ $featuredProudct->basicProduct()['mrp'] }}</del> <span style="font-size:12px;" class="text-success hidden-xs hidden-sm">{{ !empty($featuredProudct->basicProduct()['mrp']) && !empty($featuredProudct->basicAffiliate()['price']) ? round($featuredProudct->basicAffiliate()['off']).'% off' : '' }}</span> <span style="font-size:12px;" class="text-success hidden-md hidden-lg "><span class="">{{ !empty($featuredProudct->min_mrp()) && !empty($featuredProudct->basicAffiliate()['price']) ? round($featuredProudct->basicAffiliate()['off']).'% off' : '' }}</span></span></p>
                                                    --}}{{--<p class="text-center"><button class="btn btn-primary btn-xs">View Details</button></p>--}}{{--
                                                </a>
                                            </div>
                                        </div>
                                        <?php $featuredprodNo++; ?>
                                    @endforeach
                                </div>
                                <a class="left carousel-control" href="#featured-corousel" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>
                                <a class="right carousel-control" href="#featured-corousel" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
                            </div>

                        </div>
                    </div>
                </div><!--row-->
            </div>--}}
                <!-- dvoffer-corousel -->
        @endif
            <?php $allProdIdLists = []; ?>
        @foreach($mainCategories as $mainCategory)
            @if(!empty($mainCategory->products))
                <div class="corousel-container" data-plugin="khm-corousel" id="dvallproduct-corousel" >
                <div class="row">
                    <div class="col-md-12" >
                        <div class="col-md-2 col-lg-2 col-sm-12 col-xs-12">
                            <div class="sm-bg home_product_valign">
                                <h3 class="col-md-12 col-xs-8 text-center home_cat_head">{{ $mainCategory->name }}
                                    {{--<small class="text-center" style="padding-bottom: 10px; display: block;">Products for {{ $mainCategory->name }}</small>--}}
                                </h3>
                                <div class="col-md-12 col-xs-4 product_name text-center" style="margin-top: 1px;">
                                    <button class="btn btn-primary btn-md hidden-xs hidden-sm" onclick="location.href='{{ route('affiliate.category.show',[$affiliate_slug, $mainCategory->id]) }}'">View All</button>
                                    <button class="btn btn-primary btn-sm hidden-md hidden-lg" onclick="location.href='{{ route('affiliate.category.show',[$affiliate_slug, $mainCategory->id]) }}'">View All</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 col-lg-10 col-sm-12 col-xs-12">
                            <div class="carousel slide" id="all-corousel-{{ $mainCategory->id }}">
                                <div class="carousel-inner">
                                    <?php

                                    $allprodNo = 1;
                                    ?>
                                    @foreach($mainCategory->products as $allProudct)
                                        @if((!in_array($allProudct->id,$allProdIdLists)) && ($allProudct->is_home))
                                            @if($allprodNo < 9)
                                                <div class="item {{ $allprodNo==1 ? 'active' : '' }}">
                                                    <div class="col-md-3 col-lg-3 col-xs-6 col-sm-3 hm_prod_container">
                                                        <a href="{{ route('affiliate.all.slug',[$slug, $allProudct->slug]) }}" class="product_item" title="{{ $allProudct->name }}">
                                                            <img src="{{ empty($allProudct->image) ? asset('images/no-image.jpg') : asset($allProudct->image) }}" class="img-responsive">
                                                            <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($allProudct->name, $limit = 20, $end = '...') }}</p>
                                                            <p class="text-center single_prod_price"><i class="fa fa-inr"></i>{{ $allProudct->basicAffiliate()['price'] }} <del class="single_prod_mrp">{{ $allProudct->basicProduct()['mrp'] }}</del> <span style="font-size:12px;" class="text-success hidden-xs hidden-sm">{{ !empty($allProudct->basicAffiliate()['price']) && !empty($allProudct->basicProduct()['mrp']) ? round($allProudct->basicAffiliate()['off']).'% off' : '' }}</span><span style="font-size:12px;" class="text-success hidden-md hidden-lg "><span class="">{{ !empty($allProudct->basicAffiliate()['price']) && !empty($allProudct->basicProduct()['mrp']) ? round($allProudct->basicAffiliate()['off']).'% off' : '' }}</span></span></p>
                                                            {{--<p class="text-center"><button class="btn btn-primary btn-xs">View Details</button></p>--}}
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php
                                                    array_push($allProdIdLists,$allProudct->id);
                                                    $allprodNo++;
                                                ?>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                                <a class="left carousel-control" href="#all-corousel-{{ $mainCategory->id }}" data-slide="prev"><i class="glyphicon glyphicon-chevron-left"></i></a>
                                <a class="right carousel-control" href="#all-corousel-{{ $mainCategory->id }}" data-slide="next"><i class="glyphicon glyphicon-chevron-right"></i></a>
                            </div>

                        </div>
                    </div>
                </div><!--row-->
        </div><!-- dvallproduct-corousel -->
            @endif
        @endforeach

    </div> <!--myCarousel2-->
</div>
<!-- Wrapper -->
@endsection

@push('page_style')
<style>
/*    .container {
        margin-bottom: 0px;
    }*/
</style>
@endpush