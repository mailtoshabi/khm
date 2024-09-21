@extends('layouts.app')
@section('title','Contact Us')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">
                    <div class="col-sm-12" style="margin-top: 30px;">
                        <div class="row">

                            <div class="col-sm-6 col-xs-12 second-box">
                                <h3 style="margin-bottom: 20px;">Write Us</h3>
                                <h5><b>KERALA HEALTH MART</b></h5>
                                <h5 style="line-height: 20px;">10/226F Elamaram Road<br>  Edavannappara - Malappuram District</h5>
                                <h5>KERALA-673645</h5>
                                <br>

                                <h3 style="margin-bottom: 20px;">Email Us</h3>
                                <p style="margin-bottom: 34px;">
                                    <strong>For trade enquires: {{ Utility::settings('admin_email') }}<br>
                                        For complaints / issues: support@keralahelathmart.com</strong>
                                </p>

                                <h3 style="margin-bottom: 20px;">Call Us</h3>
                                <p><strong>{{ Utility::settings('admin_phone') }}</strong></p>

                            </div>
                            <div class="col-sm-6 contact-form" style="margin-top:15px;" id="dvcontact-form">
                                <h3 style="margin-bottom: 20px;">Ask Us</h3>
                                <div id="form-message" class="text-success" style="display: none;">Email Has been sent Successfully</div>
                                <form class="form" id="contact-form" role="form" method="POST" action="{{ route('contact.send') }}" data-plugin="ajaxForm">
                                    {!! csrf_field() !!}
                                    <div class="row">
                                        <div class="col-xs-6 col-md-6 form-group">
                                            <input class="form-control" id="name_contact" name="name" placeholder="Enter Your Name" type="text" />
                                        </div>
                                        <div class="col-xs-6 col-md-6 form-group">
                                            <input class="form-control" id="phone_contact" name="phone" placeholder="Enter Your Mobile" type="text" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 form-group">
                                            <textarea class="form-control" id="product_details" name="product_details" placeholder="Required Product Details" rows="4"></textarea>
                                            <br />
                                            <textarea class="form-control" id="message" name="message" placeholder="Message" rows="5"></textarea>
                                            <br />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-12 form-group">
                                            <button class="btn btn-primary btn-md" type="submit">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-sm-6 col-xs-12 first-box">

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
        var $validator = $('#contact-form').validate({
            rules: {
                name: {
                    required: true
                },
                phone: {
                    required: true
                },
                message: {
                    required: true
                }
            },
            messages: {

            }
        });

        $('#dvcontact-form').on('af.complete','#contact-form',function() {
            $('#form-message').show();
            $('#contact-form').fadeOut(350);
        });
    });
</script>
@endpush

@push('page_style')
<style>

</style>

@endpush