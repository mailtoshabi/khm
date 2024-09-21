@extends('layouts.app')
@section('title','Delete Account')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h3 class="page_title">Delete User Account</h3>
                    </div>

                    <div class="col-sm-12">
                        @if(isset($_GET['error_msg']))
                            <p class="delete_accnt_warning">
                                You have active sales entries, which is not closed or cancelled. Either you wait to receive the products or cancel the order by going <a href="{{ route('myorders') }}">My Orders</a> page. Contact us at support@keralahealthmart.com if you face any problem.
                            </p>
                        @endif
                        <h4>
                            Do you want to permanently delete your account?
                        </h4>
                        <p>
                            Your account will be deleted permanently if you click the delete button below and all the data belongs to you will be deleted, You can never re-activate the same account and you won't ever have access to it again.
                        </p>

                        <div class="form-group col-md-12 text-center" style="padding-right: 0px; padding-top:20px;">
                            {{--<button id="payment_option_btn" class="place_order btn btn-sm ladda-button" type="button" style="color: #fff; font-weight: bold">CHECK OUT &nbsp;&nbsp;<span class="fa fa-angle-right"></span></button>--}}
                            <button id="delete_account" type="submit" class="btn btn-lg btn-danger" style="color: #fff; font-weight: bold" ><span class="">DELETE MY ACCOUNT</span></button>
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
        $("#delete_account").click(function(e){
            e.preventDefault();

            var customer_id = "{{ Auth::guard('customer')->user()->id }}";
            var formdata = {customer_id: customer_id};
            var url = "{{ route('delete.account.act') }}";
            $.ajax({
                type: "POST",
                url: url,
                data: formdata,
                success: function (data) {
                    /*console.log(data);*/
                    window.location.replace(data);
                },
                error : function(jqXHR, textStatus, errorThrown) {

                },
                complete : function(jqXHR, textStatus) {
                }
            });

        });
    });
</script>
@endpush
