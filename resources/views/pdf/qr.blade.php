<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>QR Code PDF</title>
    <style>
        body {
            text-align: center;
            font-family: sans-serif;
        }

        .qr svg {
            width: 300px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="qr">
        {{ $qr }}
    </div>
</body>

</html>