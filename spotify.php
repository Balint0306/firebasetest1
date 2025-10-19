<?php
    header("Content-Type: text/html; charset=utf-8");
    $playlists_json = file_get_contents('data/playlists.json');
    $playlists = json_decode($playlists_json, true);
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

            <section class="quick-picks">
                 <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="playlist-item" data-playlist-id="<?php echo $playlists[$i]['id']; ?>">
                        <img src="<?php echo $playlists[$i]['cover']; ?>" alt="<?php echo $playlists[$i]['name']; ?>">
                        <span><?php echo $playlists[$i]['name']; ?></span>
                    </div>
                <?php endfor; ?>
            </section>

            <section class="made-for-you">
                <h2>Neked készült válogatások</h2>
                <div class="shelf">
                    <?php for ($i = 4; $i < count($playlists); $i++): ?>
                         <div class="shelf-item" data-playlist-id="<?php echo $playlists[$i]['id']; ?>">
                            <img src="<?php echo $playlists[$i]['cover']; ?>" alt="<?php echo $playlists[$i]['name']; ?>">
                            <p class="shelf-item-title"><?php echo $playlists[$i]['name']; ?></p>
                        </div>
                    <?php endfor; ?>
                </div>
            </section>

             <section class="recently-played">
                <h2>Nemrég hallgatott</h2>
                <div class="shelf">
                     <div class="shelf-item" data-playlist-id="1">
                        <img src="images/liked-songs.png" alt="Kedvelt dalok">
                        <p class="shelf-item-title">Kedvelt dalok</p>
                    </div>
                    <div class="shelf-item" data-playlist-id="3">
                        <img src="images/top-hits.png" alt="Today's Top Hits">
                        <p class="shelf-item-title">Today's Top Hits</p>
                    </div>
                </div>
            </section>
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
            <video id="video-player" playsinline></video>
            <button id="close-video-button"><i class="fas fa-chevron-down"></i></button>
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