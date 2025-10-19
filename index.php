<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Simulator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        body {
            background-color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
        }
        .phone-wrapper {
            transform: scale(0.9);
        }
        .phone {
            width: 410px;
            height: 850px;
            background: #111;
            border-radius: 60px;
            border: 12px solid #000;
            box-shadow: 0 30px 80px rgba(0,0,0,0.6);
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .phone-screen {
            background: #fff;
            flex-grow: 1;
            border-radius: 48px;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        #app-iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        #home-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, #8e44ad, #3498db);
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            z-index: 1;
            position: absolute;
        }
        #app-view {
            display:none; 
            height:100%;
            z-index: 2;
            position: absolute;
            width: 100%;
        }
        .app-grid {
             display: grid;
             grid-template-columns: repeat(4, 1fr);
             gap: 20px;
        }
        .app-icon {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
        }
        .app-icon .icon-bg {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
            color: white;
            margin-bottom: 8px;
            background-size: cover;
        }
        .app-icon span {
            color: white;
            font-size: 14px;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }
        .notch {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 160px;
            height: 30px;
            background: #000;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 20px;
            z-index: 200;
        }
        .home-indicator {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 140px;
            height: 5px;
            background: rgba(255,255,255,0.5);
            border-radius: 5px;
            z-index: 201;
        }
    </style>
</head>
<body>

<div class="phone-wrapper">
    <div class="phone">
        <div class="notch"></div>
        <div class="phone-screen">
            <div id="home-screen">
                 <div class="status-bar" style="background: transparent; color: white;">
                    <div class="time" id="phone-time">9:41</div>
                    <div class="icons">
                        <i class="fas fa-signal"></i>
                        <i class="fas fa-wifi"></i>
                        <i class="fas fa-battery-full"></i>
                    </div>
                </div>
                <div class="app-grid">
                    <div class="app-icon" id="spotify-app-icon">
                        <div class="icon-bg" style="background-color: #1DB954;">
                            <i class="fab fa-spotify"></i>
                        </div>
                        <span>Spotify</span>
                    </div>
                </div>
            </div>
            <div id="app-view">
                 <iframe id="app-iframe" src="about:blank" allow="microphone; camera; autoplay; encrypted-media;"></iframe>
            </div>
        </div>
        <div class="home-indicator" id="home-bar"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const spotifyIcon = document.getElementById('spotify-app-icon');
        const homeScreen = document.getElementById('home-screen');
        const appView = document.getElementById('app-view');
        const appIframe = document.getElementById('app-iframe');
        const homeBar = document.getElementById('home-bar');
        const timeEl = document.getElementById('phone-time');

        function openSpotify() {
            // Force reload by adding a unique timestamp (cache buster)
            appIframe.src = 'spotify.php?v=' + new Date().getTime();
            homeScreen.style.display = 'none';
            appView.style.display = 'block';
        }

        function goHome() {
            appView.style.display = 'none';
            homeScreen.style.display = 'flex';
            // Clear the iframe to stop music and free up resources
            appIframe.src = 'about:blank';
        }
        
        function updateTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            timeEl.textContent = `${hours}:${minutes}`;
        }

        spotifyIcon.addEventListener('click', openSpotify);
        homeBar.addEventListener('click', goHome);
        
        updateTime();
        setInterval(updateTime, 1000);
    });
</script>

</body>
</html>