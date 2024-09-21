@extends('layouts.affiliate')
@section('title','Account Settings')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" style="margin: 0 10px;">
                        <h3 class="page_title">Account Settings</h3>
                    </div>

                    <div class="col-sm-12">
                        <p>
                           Click here to <a href="{{ route('affiliate.delete.account',$affiliate_slug) }}">delete your Account</a>
                        </p>
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
            var url = "{{ route('affiliate.delete.account.act',$affiliate_slug) }}";
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
