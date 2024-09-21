@extends('layouts.affiliate')
@section('title','Featured Products')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class=""> <!--container-->
            <div class="row">
                <div role="tabpanel">
                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h3 class="page_title">Featured Products</h3>
                    </div>
                    <div class="col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab1">
                                @foreach($products as $product)
                                    <div class="product_container col-md-3 col-lg-2 col-xs-6 col-sm-4">
                                        <a href="{{ route('all.slug',$product->slug) }}" class="product_item" title="{{ $product->name }}">
                                            <img src="{{ empty($product->image) ? asset('images/no-image.jpg') : asset($product->image) }}" class="img-responsive">
                                            <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($product->name, $limit = 20, $end = '...') }}</p>
                                            <p class="text-center single_prod_price"><i class="fa fa-inr"></i>{{ $product->basicAffiliate()['price'] }} <del class="single_prod_mrp">{{ $product->basicProduct()['mrp'] }}</del>
                                                <span style="font-size:12px;" class="text-success hidden-xs hidden-sm">{{ !empty($product->basicProduct()['mrp']) && !empty($product->basicAffiliate()['price']) ? round($product->basicAffiliate()['off']).'% off' : '' }}</span> <span style="font-size:12px;" class="text-success hidden-md hidden-lg row"><span class="col-md-12">{{ !empty($product->basicProduct()['mrp']) && !empty($product->basicAffiliate()['price']) ? round($product->basicAffiliate()['off']).'% off' : '' }}</span></span>
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

    </div>
    <!-- Wrapper -->
@endsection

@push('page_scripts')
<script>
    $(document).ready(function() {

    });
</script>
@endpush