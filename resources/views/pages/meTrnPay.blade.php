<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Normal Transaction</title>
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
<body>
<form action="{{ Utility::FED_FORM_ACTION }}" method="post" name="txnSubmitFrm">
    <h4 align="center">Redirecting To Payment Please Wait..</h4>
    <h4 align="center">Please Do Not Press Back Button OR Refresh Page</h4>
    <input type="hidden" size="200" name="merchantRequest" id="merchantRequest" value="{{ $merchantRequest }}"  />
    <input type="hidden" name="MID" id="MID" value="{{ $getMid }}"/>
</form>
<script  type="text/javascript">
    //submit the form to the worldline
    document.txnSubmitFrm.submit();
</script>
</body>
</html>