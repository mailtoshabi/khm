@extends('layouts.store')
@section('title',!empty($store->site_title) ? $store->site_title : $store->name . " Products")
@section('description',!empty($store->site_description) ? $store->site_description : strip_tags(str_replace("&nbsp;"," ",$store->footer_description)))
@section('keywords',!empty($store->site_keywords) ? $store->site_keywords : $store->name)
@section('content')
        <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="{{ route('all.slug',$store->username) }}">
                    <p class="site_title">{{ $store->name }}</p>
            </a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="{{ route('all.slug',$store->username) }}">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="bg-primary" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 mx-auto text-center about-us">
                    <h2 class="section-heading text-white">{{ $category }}</h2>
                    <hr class="light my-4">
                </div>
                <div class="col-sm-12">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="tab1">
                            @foreach($products as $product)
                                <div class="product_container col-md-3 col-lg-3 col-xs-6 col-sm-4">
                                    <a href="{{ route('all.slug',$product->slug) }}" class="product_item" target="_blank" title="{{ $product->name }}">
                                        <img src="{{ empty($product->image) ? asset('images/no-image.jpg') : asset($product->image) }}" class="img-responsive">
                                        <p class="product_name store_product text-center" style="padding-top: 5px;">{{ $product->name }}</p>
                                        <p class="text-center single_prod_price"><del class="single_prod_mrp"><i class="fa fa-inr"></i>{{ $product->min_mrp() }}</del> <i class="fa fa-inr"></i>{{ $product->min_price() }}
                                            <span style="font-size:12px;" class="text-success">{{ !empty($product->min_mrp()) && !empty($product->min_price()) ? round((($product->min_mrp()-$product->min_price())/$product->min_mrp())*100,0).'% off' : '' }}</span>
                                        </p>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact">
        <div class="container">

            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-heading">Get In Touch!</h2>
                    <hr class="my-4">
                    <p class="mb-5">Give us a call or send us an email and we will get back to you as soon as possible!</p>
                </div>
            </div>

            <div style="padding: 0 20%;">
                <div class="row text-center">
                <div class="col-sm-5 col-xs-12" id="contact_icons">
                    <div class="row">
                        <div class="col-sm-6 col-xs-6 first-box">
                            <h1 style="font-size: 4.5rem;"><span class="fa fa-envelope"></span></h1>

                        </div>
                        <div class="col-sm-6 col-xs-6 second-box">
                            <h1 style="font-size: 4.5rem;"><span class="fa fa-phone"></span></h1>

                        </div>
                        <div class="col-sm-6 col-xs-6 third-box">
                            <h1 style="font-size: 4.5rem;"><span class="fa fa-map-marker"></span></h1>


                        </div>
                        <div class="col-sm-6 col-xs-6 fourth-box">
                            <h1 style="font-size: 4.5rem;"><span class="fa fa-globe"></span></h1>


                        </div>
                    </div>
                </div>
                <div class="col-sm-7 col-xs-12">
                        <p class="site_title">{{ $store->name }}</p>

                    @if(!empty($store->location) && !empty($store->district))
                        <p class="">{!!  nl2br($store->location ) !!} {{ !empty($store->location) ? ', ' . $store->district : $store->district  }}</p>
                    @endif

                    @if(!empty($store->phone))
                        Phone : {{ $store->phone }} &nbsp;
                    @endif
                    @if(!empty($store->email))
                        <br> Email : {{ $store->email }}
                    @endif
                    <br> {{ config('app.domain') . '/' . $store->username }}
                </div>
            </div>
            </div>

        </div>
    </section>
@endsection
@push('page_scripts')
<script>
    $(document).ready(function() {


    });
</script>

@endpush
@push('page_style')
<style>
    @media (max-width: 425px) {
        #contact_icons {
            display: none;
        }
    }

    .first-box{padding:10px;background:#9C0;}
    .second-box{padding:10px; background:#39F;}
    .third-box{padding:10px;background:#F66;}
    .fourth-box{padding:10px;background:#6CC;}

    .store_description h6, .store_description h5, .store_description h4 {
        line-height: 30px;
    }
</style>

@endpush