@extends('layouts.app')
@section('title','search '.$term)
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class=""> <!--container-->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h3 class="page_title">Search Results: <small>{{ $term }}</small></h3>
                    </div>

                    <div class="col-sm-12">
                        @if(!empty(json_decode($products)))

                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab1">
                                @foreach($products as $product)
                                    <div class="product_container col-md-3 col-lg-2 col-xs-6 col-sm-4">
                                        <a href="{{ route('all.slug',$product->slug) }}" class="product_item" title="{{ $product->name }}">
                                            <img src="{{ empty($product->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $product->image) }}" class="img-responsive">
                                            <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($product->name, $limit = 20, $end = '...') }}</p>
                                            <p class="text-center single_prod_price"><i class="fa fa-inr"></i>{{ $product->min_price() }} <del class="single_prod_mrp">{{ $product->min_mrp() }}</del>
                                                <span style="font-size:12px;" class="text-success hidden-xs hidden-sm">{{ !empty($product->min_mrp()) && !empty($product->min_price()) ? round((($product->min_mrp()-$product->min_price())/$product->min_mrp())*100,0).'% off' : '' }}</span> <span style="font-size:12px;" class="text-success hidden-md hidden-lg row"><span class="col-md-12">{{ !empty($product->min_mrp()) && !empty($product->min_price()) ? round((($product->min_mrp()-$product->min_price())/$product->min_mrp())*100,0).'% off' : '' }}</span></span>
                                            </p>
                                            {{--<p class="text-center"><button class="btn btn-primary btn-xs">View Details</button></p>--}}
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                        </div>



                        @else
                            <p class="text-center" style="font-size: 18px;">No products found..!!</p>
                        @endif
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
        var selected_cat = "{{ $selected_cat }}";
    });
</script>
@endpush
