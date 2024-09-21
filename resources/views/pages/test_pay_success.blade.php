<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction Status</title>
    <style>
        body{
            font-family:Verdana, sans-serif	;
            font-size::12px;
        }
        .wrapper{
            width:980px;
            margin:0 auto;
        }
        table{

        }
        tr{
            padding:5px
        }
        td{
            padding:5px;
        }
        input{
            padding:5px;
        }
    </style>
</head>
<body onload="getData();">
<div class="wrapper">
    <center> <H3> Transaction Status </H3></center>
    <table>
        <tr><!-- PG transaction reference number-->
            <td><label for="txnRefNo">Transaction Ref No. :</label></td>
            <td>{{ $response->getPgMeTrnRefNo() }}</td>
            <!-- Merchant order number-->
            <td><label for="orderId">Order No. :</label></td>
            <td>{{ $response->getOrderId() }} </td>
            <!-- Transaction amount-->
            <td><label for="amount">Amount :</label></td>
            <td>{{ $response->getTrnAmt() }}</td>
        </tr>
        <tr><!-- Transaction status code-->
            <td><label for="statusCode">Status Code :</label></td>
            <td>{{ $response->getStatusCode() }}</td>

            <!-- Transaction status description-->
            <td><label for="statusDesc">Status Desc :</label></td>
            <td>{{ $response->getStatusDesc() }}</td>

            <!-- Transaction date time-->
            <td><label for="txnReqDate">Transaction Request Date :</label></td>
            <td>{{ $response->getTrnReqDate() }}</td>
        </tr>
        <tr>
            <!-- Transaction response code-->
            <td><label for="responseCode">Response Code :</label></td>
            <td>{{ $response->getResponseCode() }}</td>

            <!-- Bank reference number-->
            <td><label for="statusDesc">RRN :</label></td>
            <td>{{ $response->getRrn() }}</td>
            <!-- Authzcode-->
            <td><label for="authZStatus">AuthZCode :</label></td>
            <td>{{ $response->getAuthZCode() }}</td>
        </tr>
        <tr>	<!-- Additional fields for merchant use-->
            <td><label for="addField1">Add Field 1 :</label></td>
            <td>{{ $response->getAddField1() }}</td>

            <td><label for="addField2">Add Field 2 :</label></td>
            <td>{{ $response->getAddField2() }}</td>

            <td><label for="addField3">Add Field 3 :</label></td>
            <td>{{ $response->getAddField3() }}</td>
        </tr>
        <tr>
            <td><label for="addField4">Add Field 4 :</label></td>
            <td>{{ $response->getAddField4() }}</td>

            <td><label for="addField5">Add Field 5 :</label></td>
            <td>{{ $response->getAddField5() }}</td>

            <td><label for="addField6">Add Field 6 :</label></td>
            <td>{{ $response->getAddField6() }}</td>
        </tr>
        <tr>
            <td><label for="addField7">Add Field 7 :</label></td>
            <td>{{ $response->getAddField7() }}</td>

            <td><label for="addField8">Add Field 8 :</label></td>
            <td>{{ $response->getAddField8() }}</td>
        </tr>

    </table>
</div>

</body>
</html>