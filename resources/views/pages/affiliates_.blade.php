@extends('layouts.app')
@section('title','Affiliates')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper ">
        @if(!empty(json_decode($sliders)))
            <div id="myCarousel" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators hidden-xs hidden-sm">
                        <?php $no_slider1 = 1; ?>
                        @foreach($sliders as $slider)
                            <li data-target="#myCarousel" data-slide-to="{{ $no_slider1-1 }}" class="{{ $no_slider1==1 ? 'active' : '' }}"></li>
                            <?php $no_slider1++; ?>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        <?php $no_slider = 1; ?>
                        @foreach($sliders as $slider)
                            <div class="item {{ $no_slider==1 ? 'active' : '' }}"> <img src="{{ asset($slider->image) }}" class="hm_slider" alt="{{ config('app.name') }}" style="width: 100%;">

                            </div>
                            <?php $no_slider++; ?>
                        @endforeach
                    </div>
                    <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
                    <a class="right carousel-control" href="#myCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
                </div>


        @endif
        <div class="container">
            <div class="row">
                <div class="col-sm-12 contact-form" style="margin-top:15px;" id="dvdealer-form">
                    <p>Fill here to make your store online</p>
                    <div id="form-message" class="text-success" style="display: none;">Request has been sent Successfully. We will contact you soon.</div>
                    <form class="form" id="dealer-form" role="form" method="POST" action="{{ route('dealer.send') }}" data-plugin="ajaxForm">
                        {!! csrf_field() !!}
                        <input id="type_dealer" name="type_dealer" value="{{ Utility::SLIDER_TYPE_STORE }}" type="hidden" />
                        <div class="row">
                            <div class="col-xs-7 col-md-4 form-group">
                                <input class="form-control" id="name_dealer" name="name" placeholder="Full Name" type="text" />
                            </div>
                            <div class="col-xs-5 col-md-3 form-group">
                                <input class="form-control" id="phone_dealer" name="phone" placeholder="Mobile Number" type="text" />
                            </div>
                            <div class="col-xs-7 col-md-4 form-group">
                                <input class="form-control" id="company_dealer" name="company" placeholder="Company Name" type="text" />
                            </div>
                            <div class="col-xs-5 col-md-1 form-group">
                                <button class="btn btn-primary btn-md" style="width: 100%;" type="submit">Submit</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
            <p>Links of healthcare online stores</p> {{--sub_page_pt--}}

            <div class="row destacados">

                @if(isset($affiliates) && !empty($affiliates))
                    @foreach($affiliates as $affiliate)
                        <div class="col-md-4">
                            <div class="result_cont">
                                <a href="{{ route('all.slug',$affiliate->slug) }}" target="_blank" title="{{ $affiliate->name }}" style="font-weight: bold; font-size: 18px;">{{ $affiliate->name }}</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>No affiliates found..!!</p>
                @endif
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

