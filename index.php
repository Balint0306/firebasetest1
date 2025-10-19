<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Mobile UI</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="phone">
        <div class="notch"></div>
        <div class="screen">
            <div class="app-grid">
                <div class="app" data-app="weather">
                    <div class="app-icon">&#9728;&#65039;</div>
                    <div class="app-name">Weather</div>
                </div>
                <div class="app" data-app="camera">
                    <div class="app-icon">&#128247;</div>
                    <div class="app-name">Camera</div>
                </div>
                <div class="app" data-app="messages">
                    <div class="app-icon">&#128172;</div>
                    <div class="app-name">Messages</div>
                </div>
                 <div class="app" data-app="settings">
                    <div class="app-icon">&#9881;&#65039;</div>
                    <div class="app-name">Settings</div>
                </div>
            </div>
            <div class="app-content">
                <iframe id="app-frame"></iframe>
                <button class="close-button">X</button>
            </div>
        </div>
        <div class="home-indicator"></div>
    </div>
    <script src="script.js"></script>
</body>
</html>