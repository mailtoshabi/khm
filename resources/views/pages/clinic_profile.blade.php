@extends('layouts.clinic')
@section('title',!empty($clinic->site_title) ? $clinic->site_title : $clinic->user->name)
@section('description',!empty($clinic->site_description) ? $clinic->site_description : strip_tags(str_replace("&nbsp;"," ",$clinic->footer_description)))
@section('keywords',!empty($clinic->site_keywords) ? $clinic->site_keywords : $clinic->user->name)
@section('content')
        <!-- Navigation -->
<nav class="navbar navbar-expand-lg fixed-top text-uppercase" id="mainNav" style="background: #8AD5FD;">
    <div class="container">
        <a class="navbar-brand js-scroll-trigger" href="#gmap" style="line-height: 20px; text-align: center; padding-left: 10px;">
            {{ $clinic->user->name }}
            <small id="location_text" style="font-size: 15px;"><br><i style="font-size: 16px;" class="fa fa-map-marker"></i> <b>{{ $clinic->get_city() }}</b>, {{ $clinic->get_district() . ' DT' }} {{ !empty($clinic->pin)? ' - ' . $clinic->pin :'' }}</small>
        </a>

        <div class="mobile_view">
            <a href="tel:{{ $clinic->phone }}" ><i class="fa fa-phone"></i> Call</a>
            <a href="#gmap"><i class="fa fa-map-marker"></i> Direction</a>
            <div class="share-wrapper bottom">
                <a class="share-action"><i class="fa fa-share"></i> Share</a>
                <div class="share-container rc10">
                    <a target="blank" class="share-btn tl icon-facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}&amp;src=sdkpreparse"></a>
                    <a target="blank" class="share-btn tr icon-twitter" href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}"></a>
                    <a target="blank" class="share-btn bl fa fa-envelope" href="mailto:?subject=Welcome to {{ str_replace("&"," and ",$clinic->user->name) }} &amp;body=Check out {{ str_replace("&"," and ",$clinic->user->name) }} on {{ Request::fullUrl() }}."></a>
                    <a target="blank" class="share-btn br fa fa-whatsapp" href="https://api.whatsapp.com/send?text=Checkout {{ str_replace("&"," and ",$clinic->user->name) }} on {{ urlencode(Request::fullUrl()) }}"></a>
                </div>
            </div>
        </div>
        <div class="mb-0 larger_view" style="float:right;">
            <a href="#" class="header_ph"><i class="fa fa-phone"></i> {{ $clinic->phone }}<span><br>Call for booking</span></a>
            <div class="social_share_big" style="float:right; text-align: center; padding-right: 10px;">
                <a class="social-icon facebook" target="blank" data-toggle="tooltip" data-placement="top" title="Share on Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}&amp;src=sdkpreparse">
                    <i class="fa fa-facebook"></i>
                </a>

                <a class="social-icon twitter" target="blank" data-toggle="tooltip" data-placement="top" title="Share on Twitter" href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}">
                    <i class="fa fa-twitter"></i>
                </a>
                <a class="social-icon email" target="blank" data-toggle="tooltip" data-placement="top" title="Send via EMail" href="mailto:?subject=Welcome to {{ str_replace("&"," and ",$clinic->user->name) }} &amp;body=Check out {{ str_replace("&"," and ",$clinic->user->name) }} on {{ Request::fullUrl() }}.">
                    <i class="fa fa-envelope-o"></i>
                </a>
                <a class="social-icon whatsapp" target="blank" data-toggle="tooltip" data-placement="top" title="Share on Whatsapp" href="https://api.whatsapp.com/send?text=Checkout {{ str_replace("&"," and ",$clinic->user->name) }} on {{ urlencode(Request::fullUrl()) }}">
                    <i class="fa fa-whatsapp"></i>
                </a>
                <a href="" style="color: #114dba;"><span><br>Share</span></a>
            </div>

            {{--<a href="#gmap"><i class="fa fa-map-marker"></i> Our Location<span><br>Get Direction</span></a></p>--}}
        </div>
        <div class="clearfix"></div>
    </div>
</nav>

<section class="masthead mb-0" id="about" style="background:#128b9e"> {{-- bg-blue bg-primary--}}
    <div class="container">

        {{--<hr class="star-light mb-5">--}}
        <div class="row">
            <div class="col-lg-5 clinic_address" style="text-align: center;">

                @if(!empty($clinic->images))
                    <div class="row">

                        <div class="col-md-12">
                            <div class="row clinic_images portfolio">

                                <div id="clinic-slider" class="flexslider" style="width: 100%;">
                                    <ul class="slides">
                                        @foreach($clinic->images as $otherImage)
                                            <li>
                                                <img src="{{ asset(Utility::DEFAULT_STORAGE . $otherImage['original']) }}" alt="">
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <div class="clearfix"></div>
                    </div>
                @endif

            </div>
            <div class="col-lg-7 doctor_container">
                <h3 class="text-center text-uppercase">
                    <select class="form-control select2" id="doctors" name="doctors" onchange="getDoctorList(this.value)">
                        {{--<option value="">Select Doctor</option>--}}
                        @foreach($clinic->doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }} - {{ $doctor->designation }}</option>
                        @endforeach
                    </select>
                </h3>
                <div id="dv_doctors" >

                </div>

            </div>
        </div>
    </div>
    </div>
</section>

<!-- Treatment Offered Section -->


<!-- About Section -->
<section class="bg-primary text-white mb-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 ml-auto about_us about_mob">
                <h3 class="text-center text-uppercase text-white">About Us</h3>
                {!! $clinic->description !!}
            </div>
            <div id="opening_hours" class="col-lg-4 ml-auto about_mob">
                <h3 class="text-center text-uppercase text-white">Opening Hours</h3>
                @if($clinic->is_oh)
                    <div class="business-hours">
                        <ul class="list-unstyled opening-hours">
                            <li class="{{ !empty($clinic->oh_mon) ? $clinic->oh_mon : 'closed' }}">Monday <span class="pull-right">{{ !empty($clinic->oh_mon) ? $clinic->oh_mon : 'Closed' }}</span></li>
                            <li class="{{ !empty($clinic->oh_tue) ? $clinic->oh_tue : 'closed' }}">Tuesday <span class="pull-right">{{ !empty($clinic->oh_tue) ? $clinic->oh_tue : 'Closed' }}</span></li>
                            <li class="{{ !empty($clinic->oh_wed) ? $clinic->oh_wed : 'closed' }}">Wednesday <span class="pull-right">{{ !empty($clinic->oh_wed) ? $clinic->oh_wed : 'Closed' }}</span></li>
                            <li class="{{ !empty($clinic->oh_thu) ? $clinic->oh_thu : 'closed' }}">Thursday <span class="pull-right">{{ !empty($clinic->oh_thu) ? $clinic->oh_thu : 'Closed' }}</span></li>
                            <li class="{{ !empty($clinic->oh_fri) ? $clinic->oh_fri : 'closed' }}">Friday <span class="pull-right">{{ !empty($clinic->oh_fri) ? $clinic->oh_fri : 'Closed' }}</span></li>
                            <li class="{{ !empty($clinic->oh_sat) ? $clinic->oh_sat : 'closed' }}">Saturday <span class="pull-right">{{ !empty($clinic->oh_sat) ? $clinic->oh_sat : 'Closed' }}</span></li>
                            <li class="{{ !empty($clinic->oh_sun) ? $clinic->oh_sun : 'closed' }}">Sunday <span class="pull-right">{{ !empty($clinic->oh_sun) ? $clinic->oh_sun : 'Closed' }}</span></li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="col-lg-4 ml-auto contact_us">
                <h3 class="text-center text-uppercase text-white">Contact Us</h3>
                <h4 class="text-uppercase mb-2 name_clinic">{{ $clinic->user->name }}</h4>
                <p class="lead mb-0">{{ !empty($clinic->location)? $clinic->location . ', ' :'' }} {{ $clinic->get_city() }}
                    <br>{{ $clinic->get_district() }} District<br> Kerala{{ !empty($clinic->pin)? ' - ' . $clinic->pin :'' }}</p>
                @if(!empty($clinic->phone))
                    <p class="mb-0"><i class="fa fa-phone"></i> {{ $clinic->phone }}</p>
                @endif
                @if(!empty($clinic->contact_email))
                    <p class="mb-0"><i class="fa fa-envelope"></i> {{ $clinic->contact_email }}</p>
                @endif
            </div>
        </div>

    </div>
</section>
@if($clinic->is_oh)

@endif
<div class="text-center" id="gmap">
    <div class="col-lg-12" style="padding: 0">
        <iframe src="{{ $clinic->location_link }}" height="450" frameborder="0" style="border:0; width:100%" allowfullscreen></iframe>
    </div>
</div>
@if(!empty($clinic->footer_description))
        <!-- Footer Description-->
<section class="mb-0" style="background: #131A22; color: #767676;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 ml-auto about_us about_mob">
                {!! $clinic->footer_description !!}
            </div>
        </div>
    </div>
</section>
@endif
<footer>
<!-- Copyright -->
<div class="copyright py-4 text-center text-white">
    <div class="container">
        <small>Powered by <a href="https://keralahealthmart.com">Kerala Health Mart</a></small>
    </div>
</div>
</footer>
<!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
<div class="scroll-to-top d-lg-none position-fixed ">
    <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top">
        <i class="fa fa-chevron-up"></i>
    </a>
</div>


<!-- Portfolio Modals -->

<!-- Portfolio Modal  -->

@if(!empty(json_decode($clinic->treatments)))
<?php $treatment_no2 = 1; ?>
@foreach($clinic->treatments as $clinic_treatment)
        <!-- Modal -->
<div id="treatmentModal_{{ $treatment_no2 }}" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $clinic_treatment->name }}</h4>
            </div>
            <div class="modal-body">
                {!! $clinic_treatment->description !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<?php $treatment_no2++; ?>
@endforeach
@endif

@endsection
@push('page_scripts')
<script src="{{ asset('clinic/js/jquery.flexslider.js') }}"></script>
<script>
    $(document).ready(function() {

        getDoctorList('{{ $doctor_default }}');


    });

    $(window).load(function(){
        $('.flexslider').flexslider({
            animation: "slide",
            animationSpeed: 1200,
            slideshowSpeed: 2000,
            start: function(slider){

            }
        });

    });

    function getDoctorList(val) {
        $('#jq-loader').show();
        var url = '{{ route('clinic.getdoctors',$slug) }}';
        var formdata = {doctor_id: val};
        $.ajax({
            type: "GET",
            url: url,
            data: formdata,
            success: function (data) {
                $("#jq-loader").fadeOut(50, function() {
                    $('#dv_doctors').empty().append(data.content).find("#doctor-slider").flexslider({
                        animation: "slide",
                        animationSpeed: 1200,
                        slideshow: false,
                        start: function(slider){

                        }
                    });/*.lightSlider({
                     item: 1,
                     loop:true,
                     keyPress:true,
                     auto: false
                     });*/
                    $('[data-toggle="tooltip"]').tooltip();
                    /*getTreatmentImage(data.treatment_default);*/
                });

            },
            error : function(jqXHR, textStatus, errorThrown) {

            },
            complete : function(jqXHR, textStatus) {
            }
        });

    }
</script>

@endpush
@push('page_style')
<link rel="stylesheet"  href="{{ asset('clinic/css/flexslider.css') }}"/>
@endpush