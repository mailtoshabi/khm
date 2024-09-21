@extends('layouts.app')
@section('title',!empty($category->site_title) ? $category->site_title : $category->name)
@section('description',!empty($category->site_description) ? $category->site_description : $category->name)
@section('keywords',!empty($category->site_keywords) ? $category->site_keywords : $category->name)
@section('content')
    <div class="wrapper sub_page_pt">

        <div class=""> <!--container-->
            <div class=""> {{--row--}}
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px;">
                        @if(!empty($category->parent_name()))
                        <h3 class="page_title">{{ $category->parent_name() }} : <span style="font-size: 17px;">{{ $category->name }}</span></h3>
                        @else
                        <h3 class="page_title">{{ $category->name }}</h3>
                        @endif
                    </div>

                    <div class="col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab1">
                                @foreach($products as $product)
                                    <div class="product_container col-md-3 col-lg-2 col-xs-6 col-sm-4">
                                        <a href="{{ route('all.slug',$product->slug) }}" class="product_item" title="{{ $product->id }}">
                                            <img src="{{ empty($product->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Product::FILE_DIRECTORY .  '/' . $product->image) }}" class="img-responsive">
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
