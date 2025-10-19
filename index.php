<?php
// --- ROUTER ---
// Check if the 'view' parameter is set to 'app'
if (isset($_GET['view']) && $_GET['view'] === 'app') {
    
    // --- START of spotify.php Logic ---

    // Allow the page to be framed by the main simulator page (on the same origin)
    header("Content-Security-Policy: frame-ancestors 'self'");
    header("Content-Type: text/html; charset=utf-8");

    $playlists = []; // Initialize as an empty array by default
    $playlists_path = 'data/playlists.json';

    // Safely try to read and decode the playlists file
    if (file_exists($playlists_path)) {
        $playlists_json = @file_get_contents($playlists_path); // Use @ to suppress warnings
        if ($playlists_json) {
            $decoded_playlists = json_decode($playlists_json, true);
            // Check if JSON decoding was successful
            if (json_last_error() === JSON_ERROR_NONE) {
                $playlists = $decoded_playlists;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify</title>
    <link rel="stylesheet" href="spotify.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="spotify-container">
        <main id="home-view">
            <header>
                <h1>Jó estét</h1>
                <div class="header-icons">
                    <i class="fa-regular fa-bell"></i>
                    <i class="fa-regular fa-clock"></i>
                    <i class="fa-solid fa-gear"></i>
                </div>
            </header>

            <?php if (!empty($playlists)): ?>
                <section class="quick-picks">
                    <?php for ($i = 0; $i < 4 && isset($playlists[$i]); $i++): ?>
                        <div class="playlist-item" data-playlist-id="<?php echo $playlists[$i]['id']; ?>">
                            <img src="<?php echo htmlspecialchars($playlists[$i]['cover']); ?>" alt="<?php echo htmlspecialchars($playlists[$i]['name']); ?>">
                            <span><?php echo htmlspecialchars($playlists[$i]['name']); ?></span>
                        </div>
                    <?php endfor; ?>
                </section>

                <section class="made-for-you">
                    <h2>Videóklipek</h2>
                    <div class="shelf">
                        <?php 
                            $video_playlist = null;
                            foreach ($playlists as $p) { if ($p['id'] == 5) { $video_playlist = $p; break; } }
                            if ($video_playlist):
                        ?>
                        <div class="shelf-item" data-playlist-id="<?php echo $video_playlist['id']; ?>">
                            <img src="<?php echo htmlspecialchars($video_playlist['cover']); ?>" alt="<?php echo htmlspecialchars($video_playlist['name']); ?>">
                            <p class="shelf-item-title"><?php echo htmlspecialchars($video_playlist['name']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="recently-played">
                    <h2>Nemrég hallgatott</h2>
                    <div class="shelf">
                        <div class="shelf-item" data-playlist-id="1">
                            <img src="images/liked-songs.png" alt="Kedvelt dalok">
                            <p class="shelf-item-title">Kedvelt dalok</p>
                        </div>
                        <?php 
                            $discover_weekly = null;
                            foreach ($playlists as $p) { if ($p['id'] == 6) { $discover_weekly = $p; break; } }
                            if ($discover_weekly):
                        ?>
                        <div class="shelf-item" data-playlist-id="<?php echo $discover_weekly['id']; ?>">
                            <img src="<?php echo htmlspecialchars($discover_weekly['cover']); ?>" alt="<?php echo htmlspecialchars($discover_weekly['name']); ?>">
                            <p class="shelf-item-title"><?php echo htmlspecialchars($discover_weekly['name']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php else: ?>
                <div style="padding: 20px; text-align: center; color: #b3b3b3;">
                    <p>Nem sikerült betölteni a lejátszási listákat.</p>
                    <p>Lehet, hogy a 'data/playlists.json' fájl hiányzik vagy hibás.</p>
                </div>
            <?php endif; ?>
        </main>

        <section id="playlist-view" class="hidden">
            <div class="playlist-header-background"></div>
            <div class="playlist-content">
                <button id="back-button"><i class="fas fa-arrow-left"></i></button>
                <div class="playlist-header">
                    <img id="playlist-cover" src="">
                    <h2 id="playlist-name"></h2>
                </div>
                <div id="song-list"></div>
            </div>
        </section>

        <!-- Video Player View -->
        <section id="video-view" class="hidden">
             <div class="video-header">
                <button id="close-video-button"><i class="fas fa-chevron-down"></i></button>
                <div class="video-title-container">
                    <span class="video-playing-from">Lejátszás innen:</span>
                    <span id="video-playlist-name"></span>
                </div>
            </div>
            <video id="video-player" playsinline></video>
            <div class="video-info">
                <h2 id="video-title"></h2>
                <p id="video-artist"></p>
            </div>
            <div class="video-controls">
                <div class="progress-bar-container" id="video-progress-bar-container">
                    <div id="video-progress" class="progress"></div>
                </div>
                <div class="time-container">
                    <span id="video-current-time">0:00</span>
                    <span id="video-duration">0:00</span>
                </div>
                <div class="main-controls">
                    <i id="video-prev-button" class="fas fa-step-backward"></i>
                    <i id="video-play-pause-button" class="fas fa-pause"></i>
                    <i id="video-next-button" class="fas fa-step-forward"></i>
                </div>
            </div>
        </section>

        <footer>
             <!-- Hidden Audio Player -->
            <audio id="audio-player"></audio>

            <div id="mini-player" class="now-playing hidden">
                 <img id="current-album-art" src="" alt="Album art">
                <div class="song-info">
                    <span id="current-song"></span>
                    <span id="current-artist"></span>
                </div>
                <div class="player-controls">
                    <i id="prev-button" class="fas fa-step-backward"></i>
                    <i id="play-pause-button" class="fas fa-play"></i>
                    <i id="next-button" class="fas fa-step-forward"></i>
                </div>
            </div>

            <nav class="main-nav">
                <a href="#" class="nav-item active">
                    <i class="fas fa-home"></i>
                    <span>Kezdőlap</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-search"></i>
                    <span>Keresés</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-book"></i>
                    <span>Saját könyvtár</span>
                </a>
            </nav>
        </footer>
    </div>
    <script src="spotify.js"></script>
</body>
</html>
<?php
    // --- END of spotify.php Logic ---
    exit; // Stop execution to not render the phone simulator
}

// If we are here, it means ?view=app was not in the URL.
// So, we render the phone simulator.
?>
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
            // Force reload by pointing to a URL with the view=app parameter and a cache buster
            appIframe.src = 'index.php?view=app&v=' + new Date().getTime();
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
