@extends('layouts.affiliate')
@section('title','Pay Online')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" >
                        <h3 class="">Pay Online</h3> {{--page_title--}}
                    </div>

                    <div class="col-sm-12">
                        <p style="text-align: center; margin-top: 25px; font-size: 20px;"><a onclick="event.preventDefault(); refreshPage();" href="#">Retry Payment</a> {{--OR <a href="#" onclick="event.preventDefault(); goBack()">Go Back</a>--}}</p>
                        <script src="https://js.instamojo.com/v1/checkout.js"></script>
                        <script>
                            Instamojo.open("{{ Request::get('request_url') }}");
                        </script>

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
    function refreshPage() {
        location.reload();
    }
</script>
@endpush