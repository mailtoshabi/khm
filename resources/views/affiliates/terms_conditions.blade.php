@extends('layouts.affiliate')
@section('title','Terms & Conditions')
@section('description','')
@section('keywords','')
@section('content')
    <div class="wrapper sub_page_pt">

        <div class="container"> <!---->
            <div class="row">
                <div role="tabpanel">

                    <div class="col-sm-12" >
                        <h3 class="">Terms & Conditions</h3> {{--page_title--}}
                    </div>

                    <div class="col-sm-12">
                        <p>By accessing the Website (www.keralahealthmart.com) and Apps (Android & iOS), You have agreed to the terms set out in this Policy</p>
                        <br><p><strong>Governing law</strong><br>
                            This Agreement (and any further rules, polices, or guidelines incorporated by reference) shall be governed and construed in accordance with the laws of Kerala, India, without giving effect to any principles of conflicts of law.
                        </p>
                        <br><p><strong>Changes to this agreement</strong><br>
                            We reserve the right, at our sole discretion, to modify or replace these Terms and Conditions by posting the updated terms on the Site. Your continued use of the Site after any such changes constitutes your acceptance of the new terms and conditions.
                        </p>
                        <br><p>Please review this agreement periodically for changes. If you do not agree to any of this Agreement or any changes to this Agreement, do not use, access or continue to access the Site or discontinue any use of the Site immediately.</p>
                        <br><p><strong>Contact Us</strong><br>
                            If you have any questions about this Agreement, please contact us at <a href="mailto:support@keralahealthmart.com">support@keralahealthmart.com</a></p>
                        <br><p><strong>Other Links</strong><br>
                        <ul class="list">
                            <li><a href="{{ route('affiliate.shipping',$affiliate_slug) }}">Shipping/Membership Policy</a></li>
                            <li><a href="{{ route('affiliate.payments', $affiliate_slug) }}">Payments</a></li>
                            <li><a href="{{ route('affiliate.privacy_policy',$affiliate_slug) }}">Privacy Policy</a></li>
                            <li><a href="{{ route('affiliate.cancellation',$affiliate_slug) }}">Cancellation & Return Policy</a></li>
                        </ul>
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