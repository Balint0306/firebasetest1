<?php
header("Content-Type: text/html; charset=utf-8");
$messages = [
    ["sender" => "John Doe", "message" => "Hey, are you coming to the party?"],
    ["sender" => "Jane Smith", "message" => "Don't forget to buy milk!"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h1 { font-size: 24px; }
        .message {
            background: #f0f0f0;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
        }
        .sender { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Messages</h1>
    <?php foreach ($messages as $msg): ?>
        <div class="message">
            <div class="sender"><?= htmlspecialchars($msg['sender']) ?></div>
            <div><?= htmlspecialchars($msg['message']) ?></div>
        </div>
    <?php endforeach; ?>
</body>
</html>