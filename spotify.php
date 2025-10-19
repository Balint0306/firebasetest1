<?php
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