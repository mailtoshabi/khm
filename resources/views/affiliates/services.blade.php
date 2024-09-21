@extends('layouts.service_affiliate')
@section('title','Services')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12">
                        <h3>Services</h3>
                        @if(isset($clinics) && !empty($clinics))
                            <div class="destacados">
                                @foreach($clinics as $clinic)
                                    <div class="col-md-12">
                                        <div class="result_cont clinic">
                                            <div class="col-md-7">
                                            <a href="{{ route('all.slug',$clinic->slug) }}" target="_blank" class="" title="">
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
                                                        <a class="social-icon email" target="blank" data-toggle="tooltip" data-placement="top" title="" href="mailto:?subject=Welcome to {{ $clinic->name }} &amp;body=Check out {{ str_replace("&"," and ",$clinic->name) }} on {{ urlencode(route('all.slug',$clinic->slug)) }}" data-original-title="Send via EMail">
                                                            <i class="fa fa-envelope-o"></i>
                                                        </a>
                                                        <a class="social-icon whatsapp" target="blank" data-toggle="tooltip" data-placement="top" title="" href="https://api.whatsapp.com/send?text=Checkout {{ str_replace("&"," and ",$clinic->name) }} on {{ urlencode(route('all.slug',$clinic->slug)) }}" data-original-title="Share on Whatsapp">
                                                            <i class="fa fa-whatsapp"></i>
                                                        </a>
                                                        <small style="color: #114dba; display: block;">SHARE</small>
                                                    </span>


                                                </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-12 text-justify" style="padding-top: 20px;">
                            </div>
                        @else
                            <p>No services found..!!</p>
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

    });
</script>
@endpush