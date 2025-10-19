<?php
header("Content-Type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
     <style>
        body { font-family: sans-serif; padding: 20px; }
        h1 { font-size: 24px; }
        .setting {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <h1>Settings</h1>
    <div class="setting">
        <span>Wi-Fi</span>
        <span>On</span>
    </div>
    <div class="setting">
        <span>Bluetooth</span>
        <span>Off</span>
    </div>
    <div class="setting">
        <span>Airplane Mode</span>
        <span>Off</span>
    </div>

</body>
</html>