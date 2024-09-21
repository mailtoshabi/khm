@extends('layouts.affiliate')
@section('title','Partner Store/Affiliate program')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">
                    <div class="col-sm-12">
                        <h3>Partner Store / Affiliate program</h3>
                        <p><strong>Reach 35 million customers looking to buy your products in Kerala. Start / Grow your business with the Health care leader in Kerala E-commerce and Direct selling. </strong></p>
                        <p>To start your online store / to become an affiliate, please feel free to connect:</p>
                        <p><strong>Call & Whatsapp : {{ Utility::settings('admin_phone') }}<br>
                        </strong></p>

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

@push('page_style')
<style>
    .table td, .table th {
        text-align: center;
    }
</style>
@endpush