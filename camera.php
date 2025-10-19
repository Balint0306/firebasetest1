<?php
header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera</title>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; background: #000; color: #fff; }
        h1 { font-size: 24px; }
        .camera-view {
            width: 100%;
            height: 300px;
            background: #333;
            border: 2px solid #555;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 20px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <h1>Camera App</h1>
    <div class="camera-view">
        [ Camera Feed ]
    </div>
</body>
</html>