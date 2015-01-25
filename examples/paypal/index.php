<!DOCTYPE html>
<html>
    <head>
        <title>Start Paypal Example</title>
        <style type="text/css">
            html {
                text-align: center;
            }
            body {
                display: inline-block;
                width: 100%;
                max-width: 800px;
                text-align: left;
            }
            label {
                display: block;
            }
        </style>
    </head>
    <body>
        <form action="startPayment.php">
            <label for="thisUrl">
                This url:
                <input type="text" id="thisUrl" value="http://">
            </label>
            <label for="user">
                PayPal User:
                <input type="text" id="user">
            </label>
            <label for="pwd">
                PayPal Pwd:
                <input type="text" id="pwd">
            </label>
            <label for="signature">
                PayPal Signature:
                <input type="text" id="signature">
            </label>
        </form>
    </body>
</html>
