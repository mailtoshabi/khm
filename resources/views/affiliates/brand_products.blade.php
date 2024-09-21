@extends('layouts.affiliate')
@section('title',!empty($brand->site_title) ? $brand->site_title : $brand->name)
@section('description',!empty($brand->site_description) ? $brand->site_description : $brand->name)
@section('keywords',!empty($brand->site_keywords) ? $brand->site_keywords : $brand->name)
@section('content')
    <div class="wrapper sub_page_pt">

        <div class=""> <!--container-->
            <div class=""> {{--row--}}
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px; text-align: center;">
                        <h3 class="page_title">{{ $brand->name }}</h3>
                        <img src="{{ empty($brand->image) ? asset('images/no-image.jpg') : asset($brand->image) }}" class="img-responsive" style="display: inline; background: white; padding: 10px;" alt="{{ $brand->name }}">
                        <p style="padding-bottom: 15px; text-align: left;"><a href="{{ route('affiliate.brands',$affiliate_slug) }}"><< All brands</a></p>
                    </div>

                    <div class="col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab1">
                                @foreach($products as $product)
                                    <div class="product_container col-md-3 col-lg-2 col-xs-6 col-sm-4">
                                        <a href="{{ route('affiliate.all.slug',[$affiliate_slug,$product->slug]) }}" class="product_item" title="{{ $product->name }}">
                                            <img src="{{ empty($product->image) ? asset('images/no-image.jpg') : asset($product->image) }}" class="img-responsive" alt="{{ $product->name }}">
                                            <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($product->name, $limit = 20, $end = '...') }}</p>
                                            <p class="text-center single_prod_price"><i class="fa fa-inr"></i>{{ $product->basicAffiliate()['price'] }}
                                                @if(!empty($product->basicProduct()['mrp']) && !empty($product->basicAffiliate()['price']) && ($product->basicProduct()['mrp'] != $product->basicAffiliate()['price']))
                                                    <del class="single_prod_mrp">{{ $product->basicProduct()['mrp'] }}</del>
                                                    <span style="font-size:12px;" class="text-success hidden-xs hidden-sm">{{ round($product->basicAffiliate()['off']).'% off' }}</span> <span style="font-size:12px;" class="text-success hidden-md hidden-lg row"><span class="col-md-12">{{ round($product->basicAffiliate()['off']).'% off' }}</span></span>
                                                @endif
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
            <div class="col-sm-12 text-center">
                {!! $products->links() !!}
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