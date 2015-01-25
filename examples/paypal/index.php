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
        <form action="startPayment.php" method="post">
            <label for="thisUrl">
                This url:
                <input type="text" id="thisUrl" name="thisUrl" value="http://">
            </label>
            <label for="user">
                PayPal User:
                <input type="text" id="user" name="user">
            </label>
            <label for="pwd">
                PayPal Pwd:
                <input type="text" id="pwd" name="pwd">
            </label>
            <label for="signature">
                PayPal Signature:
                <input type="text" id="signature" name="signature">
            </label>
            <button type="submit">Go!</button>
        </form>
        <script>
            //Auto-fill url
            var url = document.URL;
            var urlInput = document.getElementById("thisUrl");
            urlInput.value = url;
        </script>
    </body>
</html>
