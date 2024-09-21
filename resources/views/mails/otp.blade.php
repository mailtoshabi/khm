@extends('layouts.email')
@section('title', 'OTP')
@section('content')
        <!-- START CENTERED WHITE CONTAINER -->
<span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">This is preheader text. Some clients will show this text as a preview.</span>
<table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;">

    <!-- START MAIN CONTENT AREA -->
    <tr>
        <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Hi,</p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">You have been successfully registered at {{ config('app.name') }}.</p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Your One Time Password (OTP) for Verification of your customer account is</p>
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                            <tbody>
                            <tr>
                                <td align="left" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                        <tbody>
                                        <tr>
                                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center; display: inline-block; color: #ffffff; border: solid 1px #3498db; box-sizing: border-box; text-decoration: none; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize;">
                                                    @if(Session::has('kerala_h_m_o_t_p'))
                                                    {{ Session::get('kerala_h_m_o_t_p')}}
                                                    @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"> The same has also been sent to your registered mobile number. If the password expired, kindly regenerate the password by <a href="#">login</a> to your account.</p>
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">This is a system generated email, so kindly do not reply.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <!-- END MAIN CONTENT AREA -->
</table>
@endsection

@section('footer')
<!-- START FOOTER -->
    <div class="footer" style="clear: both; Margin-top: 10px; text-align: center; width: 100%;">
        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
            <tr>
                <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                    <span class="apple-link" style="color: #999999; font-size: 12px; text-align: center;">Kerala Health Mart, sales@keralahealthmart, Mob:9048544800</span>
                    <br> In case of any assistance <a href="#" style="text-decoration: underline; color: #999999; font-size: 12px; text-align: center;">Contact Us</a>.
                </td>
            </tr>
            <tr>
                <td class="content-block powered-by" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;">
                    Thank You for registering services of  <a href="{{ config('app.website_url') }}" style="color: #999999; font-size: 12px; text-align: center; text-decoration: none;">{{ config('app.domain') }}</a>.
                </td>
            </tr>
        </table>
    </div>
<!-- END FOOTER -->
@endsection
@push('page_style')

@endpush