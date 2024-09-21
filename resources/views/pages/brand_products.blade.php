@extends('layouts.app')
@section('title',!empty($brand->site_title) ? $brand->site_title : $brand->name)
@section('description',!empty($brand->site_description) ? $brand->site_description : $brand->name)
@section('keywords',!empty($brand->site_keywords) ? $brand->site_keywords : $brand->name)
@section('content')
    <div class="wrapper ">
        @if(!empty($brand->images))
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                <!-- Indicators -->
                <ol class="carousel-indicators hidden-xs hidden-sm">
                    <?php $no_slider1 = 1; ?>
                    @foreach($brand->images as $slider)
                        <li data-target="#myCarousel" data-slide-to="{{ $no_slider1-1 }}" class="{{ $no_slider1==1 ? 'active' : '' }}"></li>
                        <?php $no_slider1++; ?>
                    @endforeach
                </ol>
                <div class="carousel-inner">
                    <?php $no_slider = 1; ?>
                    @foreach($brand->images as $slider)
                        <div class="item {{ $no_slider==1 ? 'active' : '' }}"> <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Brand::FILE_DIRECTORY .  '/' . $slider) }}" class="hm_slider" alt="{{ $brand->name }}" style="width:100%">

                        </div>
                        <?php $no_slider++; ?>
                    @endforeach
                </div>
                <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
                <a class="right carousel-control" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
            </div>
        @endif
        <div class=""> <!--container-->
            <div class=""> {{--row--}}
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px; text-align: center;">
                        <h3 class="page_title">{{ $brand->name }}</h3>
                        <img src="{{ empty($brand->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Brand::FILE_DIRECTORY .  '/' . $brand->image) }}" class="img-responsive" style="display: inline; background: white; padding: 10px;" alt="{{ $brand->name }}">
                        <p style="padding-bottom: 15px; text-align: left;"><a href="{{ route('brands') }}"><< All brands</a></p>
                    </div>

                    <div class="col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab1">
                                @foreach($products as $product)
                                    <div class="product_container col-md-3 col-lg-2 col-xs-6 col-sm-4">
                                        <a href="{{ route('all.slug',$product->slug) }}" class="product_item" title="{{ $product->name }}">
                                            <img src="{{ empty($product->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $product->image) }}" class="img-responsive" alt="{{ $product->name }}">
                                            <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($product->name, $limit = 20, $end = '...') }}</p>
                                            <p class="text-center single_prod_price"><i class="fa fa-inr"></i>{{ $product->min_price() }} <del class="single_prod_mrp">{{ $product->min_mrp() }}</del>
                                                <span style="font-size:12px;" class="text-success hidden-xs hidden-sm">{{ !empty($product->min_mrp()) && !empty($product->min_price()) ? round((($product->min_mrp()-$product->min_price())/$product->min_mrp())*100,0).'% off' : '' }}</span> <span style="font-size:12px;" class="text-success hidden-md hidden-lg row"><span class="col-md-12">{{ !empty($product->min_mrp()) && !empty($product->min_price()) ? round((($product->min_mrp()-$product->min_price())/$product->min_mrp())*100,0).'% off' : '' }}</span></span>
                                            </p>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="container">
            <div class="col-sm-12 text-center ">
                {{-- {{ $products->links() }} --}}

                {{ $products->links('vendor.pagination.custom') }}
            </div>
        </div>

    </div>
    <!-- Wrapper -->
@endsection

@push('page_scripts')
<script>
    $(document).ready(function() {

    });
</script>
@endpush
