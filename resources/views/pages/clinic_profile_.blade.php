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

                            <div id="clinic-slider" class="content-slider" style="width:100%">
                                @foreach($clinic->images as $otherImage)
                                    <img class="img-fluid hasborder" src="{{ asset(Utility::DEFAULT_STORAGE . $otherImage['original']) }}" alt="">
                                @endforeach
                            </div>

                            <?php /*$imageno=1;*/ ?>
                            {{--@foreach($clinic->images as $otherImage)
                                <div class="col {{ ($imageno==1) || ($imageno==3) ? 'pr-0' : '' }} ">
                                    <a class="portfolio-item" href="#portfolio-modal-{{ $imageno }}">
                                        <div class="portfolio-item-caption d-flex position-absolute h-100 w-100">
                                            <div class="portfolio-item-caption-content my-auto w-100 text-center text-white">
                                                <i class="fas fa-search-plus fa-3x"></i>
                                            </div>
                                        </div>
                                        <img class="img-fluid" src="{{ asset(Utility::DEFAULT_STORAGE . $otherImage['thumb']) }}" alt="">
                                    </a>
                                </div>
                                    @if($imageno==2)
                                        <div class="w-100"></div>
                                    @endif--}}
                                <?php /*$imageno++;*/ ?>
                            {{--@endforeach--}}


                        </div>

                    </div>
                    <div class="clearfix"></div>
                </div>
                @endif
                {{--<div class="row">
                    <div class="col-md-12">
                    <h4 class="text-uppercase mt-2 mb-2 name_clinic">{{ $clinic->user->name }}</h4>
                    <p class="lead mb-0">{{ !empty($clinic->location)? $clinic->location . ', ' :'' }} {{ $clinic->city }}
                        <br>{{ $clinic->district }} District, Kerala {{ $clinic->pin }}</p>
                    </div>
                </div>--}}
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
<!-- Opening Hours Section -->
{{--<section id="opening_hours" class="demo-bg text-white mb-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 ml-auto">
                <h3 class="text-center text-uppercase text-white">Opening Hours</h3>
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
            </div>
        </div>

    </div>
</section>--}}
@endif
<!-- Footer -->
<footer class="footer text-center" id="gmap">
    <div class="col-lg-12" style="padding: 0">
        <iframe src="{{ $clinic->location_link }}" height="450" frameborder="0" style="border:0; width:100%" allowfullscreen></iframe>
    </div>
</footer>
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
<!-- Copyright -->
<div class="copyright py-4 text-center text-white">
    <div class="container">
        <small>Powered by <a href="https://keralahealthmart.com">Kerala Health Mart</a></small>
    </div>
</div>

<!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
<div class="scroll-to-top d-lg-none position-fixed ">
    <a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top">
        <i class="fa fa-chevron-up"></i>
    </a>
</div>


<!-- Portfolio Modals -->

<!-- Portfolio Modal  -->
<?php $imageno2=1; ?>
{{--@foreach($clinic->images as $otherImage)--}}
    {{--<div class="portfolio-modal mfp-hide" id="portfolio-modal-{{ $imageno2 }}">
    <div class="portfolio-modal-dialog bg-white">
        <a class="close-button d-md-block portfolio-modal-dismiss" href="#"> --}}{{--d-none--}}{{--
            <i class="fa fa-3x fa-times"></i>
        </a>
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-12 mx-auto">
                    <img style="margin-top: 15px; margin-bottom: 15px;" src="{{ asset(Utility::DEFAULT_STORAGE . $otherImage['original']) }}" alt="">
                </div>
            </div>
        </div>
    </div>
</div>--}}
    <?php $imageno2++; ?>
{{--@endforeach--}}


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
<script src="{{ asset('clinic/js/lightslider.js') }}"></script>
<script>
    $(document).ready(function() {

        getDoctorList('{{ $doctor_default }}');
        /*getTreatmentImage('{{ $treatment_default }}');*/

        $("#clinic-slider").lightSlider({
            item: 1,
            loop:true,
            keyPress:true,
            auto: true
        });

        $(document).find("#doctor-slider").lightSlider({
            item: 1,
            loop:true,
            keyPress:true,
            auto: false
        });

        $("[id^='treatment_slider_']").lightSlider({
            item: 1,
            loop:true,
            keyPress:true
        });

        /*$(document).on("change", 'select[id="doctors"]', function(e) {
            console.log('my test');
            var url = $(this).data('target');

        });*/

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
                    $('#dv_doctors').empty().append(data.content).find("#doctor-slider").lightSlider({
                        item: 1,
                        loop:true,
                        keyPress:true,
                        auto: false
                    });
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

    /*function getTreatmentImage(val) {
        $('#jq-loader').show();
        var url = '{{--{{ route('clinic.gettreatmentimg',$slug) }}--}}';
        var formdata = {treatment_id: val};
        $.ajax({
            type: "GET",
            url: url,
            data: formdata,
            success: function (data) {
                $("#jq-loader").fadeOut(50, function() {
                    $('#dv_treat_img').empty().append(data);
                    $("#content-slider").lightSlider({
                        item: 1,
                        loop:true,
                        keyPress:true,
                    });
                });

            },
            error : function(jqXHR, textStatus, errorThrown) {

            },
            complete : function(jqXHR, textStatus) {
            }
        });
    }*/
</script>

@endpush
@push('page_style')
    <link rel="stylesheet"  href="{{ asset('clinic/css/lightslider.css') }}"/>
<style>
    #doctor-slider {
        width:100%;
        height:auto !important;
    }
</style>
@endpush