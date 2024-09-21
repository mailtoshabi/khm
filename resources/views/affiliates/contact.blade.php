@extends('layouts.affiliate')
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

                            <div class="col-sm-6 contact-form" id="dvcontact-form">
                                <h3 style="margin-bottom: 20px;">Ask Us</h3>
                                <div id="form-message" class="text-success" style="display: none;">Email Has been sent Successfully</div>

                            </div>

                            <div class="col-sm-6 col-xs-12 first-box">
                                <h3 style="margin-bottom: 20px;">Email Us</h3>
                                <p style="margin-bottom: 34px;">
                                    <strong>
                                        @if(!empty($affiliate->contact_email))
                                            For trade enquires: {{ $affiliate->contact_email }}<br>
                                        @endif
                                    </strong>
                                </p>
                                @if(!empty($affiliate->contact_phone))
                                    <h3 style="margin-bottom: 20px;">Call Us</h3>
                                    <p><strong>{{ $affiliate->contact_phone }}</strong></p>
                                @endif
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
                /*email: {
                    required: true,
                    email: true
                },*/
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