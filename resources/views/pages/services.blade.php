@extends('layouts.service')
@section('title','Services')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper ">

        <div class="container"> <!---->

            <h3 class="" style="font-weight: bold;">Online healthcare Services</h3> {{--sub_page_pt--}}
            <div role="tabpanel">
                <div class="col-sm-12">

                        @if(isset($clinics) && !empty($clinics))
                            <div class="row destacados">
                                @foreach($clinics as $clinic)
                                    <div class="col-md-12">
                                        <div class="result_cont clinic">
                                            <div class="col-md-7">
                                                <a href="{{ route('all.slug',$clinic->slug) }}" target="_blank" class="" title="">
                                                    {{--<img src="http://lorempixel.com/200/200/abstract/1/" alt="Texto Alternativo" class="img-circle img-thumbnail">--}}
                                                    <p>{{ $clinic->user->name }}</p>
                                                    <small style="width: 100%;">{{ $clinic->get_city() . ', ' }} {{ Utility::district_name($clinic->district) }} {{ !empty($clinic->pin)? ' - ' . $clinic->pin :'' }}</small>
                                                </a>
                                            </div>
                                            <div class="col-md-5">

                                                <span><a href="#"><i class="fa fa-phone"></i> {{ !empty($clinic->phone) ? $clinic->phone : '' }}<br> <small>CALL FOR BOOKING</small></a></span>


                                                    <span class="social_div" >
                                                        <a class="social-icon facebook" target="blank" data-toggle="tooltip" data-placement="top" title="" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('all.slug',$clinic->slug)) }}&amp;src=sdkpreparse" data-original-title="Share on Facebook">
                                                            <i class="fa fa-facebook"></i>
                                                        </a>

                                                        <a class="social-icon twitter" target="blank" data-toggle="tooltip" data-placement="top" title="" href="https://twitter.com/intent/tweet?url={{ urlencode(route('all.slug',$clinic->slug)) }}" data-original-title="Share on Twitter">
                                                            <i class="fa fa-twitter"></i>
                                                        </a>
                                                        <a class="social-icon email" target="blank" data-toggle="tooltip" data-placement="top" title="" href="mailto:?subject=Welcome to {{ $clinic->user->name }} &amp;body=Check out {{ str_replace("&"," and ",$clinic->user->name) }} on {{ urlencode(route('all.slug',$clinic->slug)) }}" data-original-title="Send via EMail">
                                                            <i class="fa fa-envelope-o"></i>
                                                        </a>
                                                        <a class="social-icon whatsapp" target="blank" data-toggle="tooltip" data-placement="top" title="" href="https://api.whatsapp.com/send?text=Checkout {{ str_replace("&"," and ",$clinic->user->name) }} on {{ urlencode(route('all.slug',$clinic->slug)) }}" data-original-title="Share on Whatsapp">
                                                            <i class="fa fa-whatsapp"></i>
                                                        </a>
                                                        <small style="color: #114dba; display: block;">SHARE</small>
                                                    </span>


                                            </div>
                                            {{--<a href="{{ route('all.slug',$clinic->slug) }}" target="_blank" class="btn btn-primary" title="Enlace">View Details »</a>--}}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-12 text-justify" style="padding-top: 20px;">
                                {{--{!! $clinics->links() !!}--}}
                            </div>
                        @else
                            <p>No services found..!!</p>
                        @endif

                    </div>

            </div>
            <div class="row">
                <div class="col-sm-12 contact-form" style="margin-top:5px;" id="dvdealer-form">
                    {{--<h4 style="margin-top: 0; background: blue; padding: 5px; color: white;">Fill here to make your store online, Now itself</h4>--}}
                    <div id="form-message" class="text-success" style="display: none;">Request has been sent Successfully. We will contact you soon.</div>
                    <form class="form" id="dealer-form" role="form" method="POST" action="{{ route('dealer.send') }}" data-plugin="ajaxForm">
                        {!! csrf_field() !!}
                        <input id="type_dealer" name="type_dealer" value="{{ Utility::SLIDER_TYPE_CLINIC }}" type="hidden" />
                        <div class="row">
                            <div class="col-xs-6 col-md-4 form-group form-group-pr-5">
                                <input class="form-control" id="name_dealer" name="name" placeholder="Full Name" type="text" />
                            </div>
                            <div class="col-xs-6 col-md-4 form-group form-group-pl-5">
                                <input class="form-control" id="phone_dealer" name="phone" placeholder="Mobile Number" type="text" />
                            </div>
                            {{--<div class="col-xs-7 col-md-4 form-group">
                                <input class="form-control" id="company_dealer" name="company" placeholder="Company Name" type="text" />
                            </div>--}}
                            <div class="col-xs-12 col-md-4 form-group">
                                <button class="btn btn-primary btn-md" style="width: 100%; font-weight:bold;" type="submit">CLICK here to make your Service online NOW</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-12" style="margin: 10px 0;">
                    <h4>You can get a demo Online healthcare Clinic website with 14 days free trial. Don't wait, apply now itself. <a href="https://bit.ly/3c85WQX">CLICK HERE TO APPLY</a></h4>
                </div>
            </div>
        </div>
        <div class="corousel-container" data-plugin="" id="dvdealer_corousel" style="background: none; box-shadow: none; margin: 11px; margin-top: 0; padding-top: 0; margin-bottom: 0;">
            <div class="row">
                <div class="" >
                    <div class="">
                        <div class="carousel slide" id="dealer-corousel" >
                            <div class="carousel-inner">

                                <div class="item active">
                                    <div class="col-md-6 col-lg-6 col-xs-12 col-sm-6 hm_prod_container">
                                        <iframe class="img-responsive store_vedio" width="100%" src="https://www.youtube.com/embed/QPLPkay8rLA?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                </div>

                                <div class="item">
                                    <div class="col-md-6 col-lg-6 col-xs-12 col-sm-6 hm_prod_container">
                                        <iframe class="img-responsive store_vedio" width="100%" src="https://www.youtube.com/embed/M6IYjpINqeE?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                </div>
                                @if(!empty(json_decode($sliders)))
                                    <?php $no_slider1 = 1; ?>
                                    @foreach($sliders as $slider)
                                        <div class="item {{ $no_slider1==1 ? '' : '' }}">
                                            <div class="col-md-6 col-lg-6 col-xs-12 col-sm-6 hm_prod_container">
                                                <img src="{{ asset($slider->image) }}" class="img-responsive">
                                            </div>
                                        </div>
                                        <?php $no_slider1++; ?>
                                    @endforeach
                                @endif
                            </div>
                            <a class="left carousel-control" href="#dealer-corousel" data-slide="prev" ><i class="glyphicon glyphicon-chevron-left"></i></a>
                            <a class="right carousel-control" href="#dealer-corousel" data-slide="next" ><i class="glyphicon glyphicon-chevron-right"></i></a>
                        </div>

                    </div>
                </div>
            </div><!--row-->
        </div><!-- dvdealer-corousel -->

    </div>
    <!-- Wrapper -->
@endsection

@push('page_scripts')
<script>
    $(document).ready(function() {
        jQuery.validator.addMethod("validphone", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            isPhone = this.optional(element) || phone_number.length == 10 &&
                    phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
            return isPhone;
        }, "Please specify a valid Phone");

        var $validator = $('#dealer-form').validate({
            rules: {
                name: {
                    required: true
                },
                phone: {
                    required: true,
                    validphone: true
                }/*,
                 company: {
                 required: true
                 }*/
            },
            messages: {
                name: {
                    required: "Enter Name"
                },
                phone: {
                    required: "Enter Phone"
                }

            }
        });

        $('#dvdealer-form').on('af.complete','#dealer-form',function() {
            $('#form-message').show();
            $('#dealer-form').fadeOut(350);
        });
    });
</script>
@endpush