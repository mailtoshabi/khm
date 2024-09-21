<div class="modal fade" id="myPaymentModal">
    <div class="modal-dialog ">
        <form class="login modal-content" action="" method="post" id="payment_utr_form" role="form" data-plugin="ajaxForm" autocomplete="off" enctype="multipart/form-data">
            <!--<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h3>Login to MyWebsite.com</h3>
            </div>-->
            <div class="modal-body row" style="background: white;">
                <div class="close_btn">
                    <button type="button" class="close" data-dismiss="modal">✕</button>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12" style="padding: 12px 6%; background: #2874f0;">
                    {{--<h2 style="font-size: 23px">Kerala Health Mart</h2>--}}
                    <h3 style="font-size: 18px;">Payment Options</h3>
                    <p>Transfer the bill amount by using either of the following methods and Share your UTR Number.</p>
                    <p> >> To Bank account shown below.</p>
                    <div style="padding-left: 20px; color: darkblue; font-weight: bold;">
                        <p><strong>
                            Account Name : {{ Utility::settings('account_name') }}<br>
                            Account Number : {{ Utility::settings('account_number') }}<br>
                            Payee bank : {{ Utility::settings('bank_name') }}<br>
                            IFSC Code : {{ Utility::settings('ifsc_code') }} <br>
                            Branch : {{ Utility::settings('bank_branch') }}</strong><br>
                            </strong></p>
                    </div>
                    <br>
                    <p> >> To UPI ID shown below.</p>
                    <div style="padding-left: 20px; color: darkblue; font-weight: bold;">
                        {{ Utility::settings('upi_id') }}
                    </div>
                    <br>
                    <p> >> To Google Pay Account shown below.</p>
                    <div style="padding-left: 20px; color: darkblue; font-weight: bold;">
                        {{ Utility::settings('google_pay') }}
                    </div>
                    <br>
                    <div id="" class="form-group" >
                        <a href="https://api.whatsapp.com/send?phone=91{{ str_replace(' ','',Utility::settings('admin_phone')) }}" target="_blank" id="submit_utr" style="color: yellow; font-weight: bold; font-size: 17px; text-decoration: none; "><i class="fa fa-whatsapp"></i> <u>Share payment details on Whatsapp</u></a>
                    </div>
                </div>

            </div>

        </form>
    </div>
</div>
@push('page_scripts')

@endpush
