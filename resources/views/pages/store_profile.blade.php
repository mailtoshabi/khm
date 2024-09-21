@extends('layouts.store')
@section('title',!empty($store->site_title) ? $store->site_title : $store->name)
@section('description',!empty($store->site_description) ? $store->site_description : strip_tags(str_replace("&nbsp;"," ",$store->footer_description)))
@section('keywords',!empty($store->site_keywords) ? $store->site_keywords : $store->name)
@section('content')
        <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">
                    <p class="site_title">{{ $store->name }}</p>
            </a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#page-top">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" target="_blank" href="{{ route('store.brochure.show',$store->username) }}">Download</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>



    <section id="products" style="padding-bottom: 30px; padding-top: 150px; background: #C8F0E1;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-heading">Our Products</h2>
                <hr class="my-4">
            </div>

            <div class="col-sm-12" style=" padding-top: 30px;"> {{--background:#fff;--}}
                <div class="tab-content">
                    <?php
                    $allChilds = [];
                    $allChildIds = [];
                    ?>
                    @foreach($categories as $category)
                        @foreach($category->childs as $childcat)
                            @if(!in_array($childcat->id,$allChildIds))
                                <?php
                                $allChilds[] = $childcat;
                                $allChildIds[] = $childcat->id;
                                ?>
                            @endif
                        @endforeach
                    @endforeach

                    @foreach($allChilds as $child)
                        @if(in_array($child->id,$store_cats))
                            <div class="product_container col-md-4 col-lg-4 col-xs-6 col-sm-4">
                                {{--<a href="{{--{{ route('store.products.show',[$store->username,$child->id]) }}--}}" class="product_item" title="{{ $child->name }}">--}}
                                    <img src="{{ empty($child->image) ? asset('images/no-image.jpg') : asset($child->image) }}" class="img-responsive">
                                    <p class="product_name text-center" style="padding-top: 5px;">{{ $child->name }}</p> {{--{{ \Illuminate\Support\Str::limit($child->name, $limit = 20, $end = '...') }}--}}
                                    {{--<p class="text-center"><button class="btn btn-primary btn-xs">View Products</button></p>--}}
                                {{--</a>--}}
                            </div>
                        @endif
                    @endforeach
                    <div class="clearfix"></div>
                </div>
            </div>

        </div>


    </div>
</section>

    <header class="masthead text-center text-white d-flex" >
    <div class="container my-auto">
        <div class="row">
            <div class="col-lg-10 mx-auto" style="margin-top: 60px;">
                <h1 class="text-uppercase">
                    <strong>{{ $store->name }}</strong>
                    <br><small style="font-size: 40%;">{{ $store->short_description }}</small>
                </h1>
                <hr>
            </div>

            <div class="col-lg-8 mx-auto">
                @if(!empty($store->location) && !empty($store->district))
                    <p class=" mb-5" style="font-weight: bold;">{!!  nl2br($store->location ) !!} {!! !empty($store->location) ? '<br>' . $store->district : $store->district !!}</p> {{--text-faded--}}
                @endif
                <p class="mb-5" style="font-weight: bold;"> {{--text-faded--}}
                    @if(!empty($store->phone))
                        <i class="fa fa-phone"></i> {{ $store->phone }} &nbsp;
                    @endif
                    @if(!empty($store->email))
                        <br><i class="fa fa-envelope"></i> {{ $store->email }}
                    @endif
                    <br> <i class="fa fa-globe"></i> {{ config('app.domain') . '/' . $store->username }}
                </p>

            </div>

        </div>
    </div>
</header>

    <section class="bg-primary" id="about">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center about-us">
                    <h2 class="section-heading text-white">About Us</h2>
                    <hr class="light my-4">
                    <div class="text-faded mb-4" style="text-align: justify;">{!! $store->footer_description !!} </div>
                    <div class="text-faded mb-4 store_description" style="text-align: justify;">{!! $store->description !!} </div>
                    <?php $category_names = []; ?>
                    @foreach($categories as $category)
                        <?php $category_names[] = $category->name ?>
                    @endforeach
                    {{--<span style="color: white;">We are dealing with the products of <strong><i>{{ implode($category_names,', ') }}</i></strong></span>--}}
                    @if(!empty($store->brochure))
                        <br><p style="padding-top:30px;"><img src="{{ asset($store->brochure) }}"></p>
                    @endif

                    <br><br><a class="btn btn-light btn-xl js-scroll-trigger" href="#products">Products</a>
                </div>
            </div>
        </div>
    </section>

    <section id="download" class="bg-dark text-white" style="background: #4B0081 !important;">
        <div class="container text-center">
            <h2 class="mb-4">Download Our Latest Brochure</h2>
            <a class="btn btn-light btn-xl sr-button" target="_blank" href="{{ route('store.brochure.show',$store->username) }}">Download Now!</a>
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