@extends('layouts.affiliate')
@section('title','Products')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class=""> <!--container-->
            <div class=""> {{--row--}}
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h3 class="page_title">Products</h3>
                    </div>

                    <div class="col-sm-12">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="tab1">
                                @foreach($brands as $brand)
                                    <div class=" col-md-3 col-lg-2 col-xs-6 col-sm-4" style="background: #fff; margin-bottom: 5px; border-right: 1px solid lightgrey;">
                                        <a href="{{ route('affiliate.all.slug',[$affiliate_slug, $brand->slug]) }}" class="product_item" title="{{ $brand->name }}" style="padding: 5px;">
                                            <img src="{{ empty($brand->image) ? asset('images/no-image.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Brand::FILE_DIRECTORY .  '/' . $brand->image) }}" class="img-responsive" alt="{{ $brand->name }}">
                                            <p class="product_name text-center" style="padding-top: 5px;">{{ $brand->name }}</p>
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
