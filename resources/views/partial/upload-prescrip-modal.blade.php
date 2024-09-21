<div class="modal fade" id="myPrescriptionModal">
    <div class="modal-dialog ">
        <form class="login modal-content" action="{{ route('prescription') }}" method="post" id="prescription_form" role="form" data-plugin="ajaxForm" autocomplete="off" enctype="multipart/form-data">
            <!--<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h3>Login to MyWebsite.com</h3>
            </div>-->
            <div class="modal-body row" >
                <div class="close_btn">
                    <button type="button" class="close" data-dismiss="modal">✕</button>
                </div>
                <div class="col-md-6 col-sm-12 col-xs-12" style="padding: 12px 6%;">
                    {{--<h2 style="font-size: 23px">Kerala Health Mart</h2>--}}
                    <h3 style="font-size: 18px;">Upload Prescription</h3>
                    <p>Please upload prescription details here.</p>
                </div>
                <div class="right-modal-content col-md-6 col-sm-12 col-xs-12 signup_modal_pad" >
                    <div class="form-group @if ($errors->has('phone_prescription')) has-error @endif" style="margin-bottom: 0px;">
                        <div style="position: relative;">
                            <input type="text" name="phone_prescription" id="phone_prescription" placeholder="Enter Mobile Number" />

                            @if ($errors->has('phone_prescription'))
                            <span class="help-block">
                              {{ $errors->first('phone_prescription') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group @if ($errors->has('image_prescription')) has-error @endif" style="margin-bottom: 0; margin-top: 10px;">
                        <div style="position: relative;">
                            <input type="file" name="image_prescription" id="image_prescription" placeholder="Upload Image" />

                            @if ($errors->has('image_prescription'))
                                <span class="help-block">
                              {{ $errors->first('image_prescription') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div id="" class="form-group" >
                        <input id="check_prescription" type="submit" value="Submit Details" class="btn btn-lg" />
                    </div>

            </div>
            </div>

        </form>
    </div>
</div>
@push('page_scripts')
<script>
    $(document).ready(function() {
        var myPrescriptionModal = $('#myPrescriptionModal');
        myPrescriptionModal.on('af.success','#prescription_form',function(e,data) {
            $('#jq-loader').hide();
            $('#myPrescriptionModal').modal('hide');
            var showData = '<p class="text-success">You have been submitted successfully. Our executive will contact you soon.<br> </p>';
            $('#msg-bg').show().html(showData);
            $("#msg-bg").fadeOut( 12000, function() {
                // Animation complete.
            });
        });

        
        jQuery.validator.addMethod("validphone", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            isPhone = this.optional(element) || phone_number.length == 10 &&
                    phone_number.match(/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/);
            return isPhone;
        }, "Please specify a valid Phone");

        var $validator = $('#prescription_form').validate({
            rules: {
                phone_prescription: {
                    required: true,
                    validphone: true
                },
                image_prescription : {
                    required : true,
                    extension: "jpg|jpeg|png"
                }
            },
            messages: {
                phone_prescription: {
                    required: "Phone is required"
                },
                image_prescription: {
                    required: "Select Prescription Image",
                }
            }
        });


    });

    
</script>
@endpush