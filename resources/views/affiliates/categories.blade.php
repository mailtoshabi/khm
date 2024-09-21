@extends('layouts.affiliate')
@section('title','All Categories')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div id="myCarousel2">  <!--class="container"-->
            <div class="row">
                <div role="tabpanel">
                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h3 class="page_title">All Categories</h3>
                    </div>
                </div>

                <div role="tabpanel">
                    {{-- @foreach($categories as $category)
                        @if(!empty(json_decode($category->childs))) --}}
                            {{-- <div class="col-sm-12" style="background:#fff; border-bottom:1px solid #DBDBDB;">
                                <div class="row" style="margin: 0 10px;">
                                    <div class="col-xs-10 col-sm-10 col-md-10 col-lg-10">
                                        <h3 class="page_title">{{ $category->name }}</h3>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="margin-top: 25px;">
                                        <div class="pull-right">
                                            <button class="btn btn-primary btn-md hidden-xs hidden-sm" onclick="location.href='{{ route('affiliate.all.slug',[$affiliate_slug, $category->slug]) }}'" >View All</button>
                                            <button class="btn btn-primary btn-xs hidden-md hidden-lg" onclick="location.href='{{ route('affiliate.all.slug',[$affiliate_slug, $category->slug]) }}'">View All</button>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-sm-12" style="background:#fff; padding-top: 10px;">
                                <div class="tab-content">
                                    @foreach($categories as $category)
                                        <div class="col-md-3 col-lg-2 col-xs-6 col-sm-4">
                                            <a href="{{ route('affiliate.all.slug',[$affiliate_slug, $category->slug]) }}" class="product_item" title="{{ $category->name }}">
                                                <img src="{{ empty($category->image) ? asset('images/no-image_cat.jpg') : asset(Utility::DEFAULT_STORAGE . App\Models\Category::FILE_DIRECTORY .  '/' . $category->image) }}" class="img-responsive" >
                                                <p class="product_name text-center" style="padding-top: 5px;">{{ \Illuminate\Support\Str::limit($category->name, $limit = 20, $end = '...') }}</p>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        {{-- @endif
                    @endforeach --}}

                </div>
            </div>
        </div>

    </div>
    <!-- Wrapper -->
@endsection

@push('page_scripts')
<script>
    $(document).ready(function(){
        // Add smooth scrolling to all links
        $("a").on('click', function(event) {

            // Make sure this.hash has a value before overriding default behavior
            if (this.hash !== "") {
                // Prevent default anchor click behavior
                event.preventDefault();

                // Store hash
                var hash = this.hash;

                // Using jQuery's animate() method to add smooth page scroll
                // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                $('html, body').animate({
                    scrollTop: $(hash).offset().top
                }, 800, function(){

                    // Add hash (#) to URL when done scrolling (default click behavior)
                    window.location.hash = hash;
                });
            } // End if
        });
    });
</script>
@endpush
