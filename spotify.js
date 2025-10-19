document.addEventListener('DOMContentLoaded', () => {
    // Views
    const homeView = document.getElementById('home-view');
    const playlistView = document.getElementById('playlist-view');
    const videoView = document.getElementById('video-view');

    // Media players
    const audioPlayer = document.getElementById('audio-player');
    const videoPlayer = document.getElementById('video-player');

    // Clickable items
    const clickableItems = document.querySelectorAll('.playlist-item, .shelf-item');
    const backButton = document.getElementById('back-button');
    const closeVideoButton = document.getElementById('close-video-button');
    const mainNavLinks = document.querySelectorAll('.nav-item');

    // Playlist view elements
    const playlistCover = document.getElementById('playlist-cover');
    const playlistName = document.getElementById('playlist-name');
    const songList = document.getElementById('song-list');

    // Now Playing bar elements (Mini Player)
    const miniPlayer = document.getElementById('mini-player');
    const currentAlbumArt = document.getElementById('current-album-art');
    const currentSongEl = document.getElementById('current-song');
    const currentArtistEl = document.getElementById('current-artist');
    const playPauseBtn = document.getElementById('play-pause-button');
    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');

    // Data stores
    let songsData = {};
    let playlistsData = {};
    
    // Player state
    let currentPlaylistId = null;
    let currentSongIndex = -1;
    let activePlayer = null; // 'audio' or 'video'

    // --- DATA FETCHING ---
    Promise.all([
        fetch('data/songs.json').then(res => res.json()),
        fetch('data/playlists.json').then(res => res.json())
    ]).then(([songs, playlists]) => {
        songsData = songs;
        playlistsData = playlists;
    });

    // --- NAVIGATION ---
    clickableItems.forEach(item => {
        item.addEventListener('click', (e) => {
            const playlistId = e.currentTarget.dataset.playlistId;
            showPlaylist(playlistId);
        });
    });

    backButton.addEventListener('click', showHomeView);
    mainNavLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // Basic navigation simulation
            if (e.currentTarget.querySelector('span').textContent === 'Kezdőlap') {
                showHomeView();
            }
        });
    });

    function showHomeView() {
        homeView.classList.remove('hidden');
        playlistView.classList.add('hidden');
        videoView.classList.add('hidden');
    }

    closeVideoButton.addEventListener('click', () => {
        videoPlayer.pause();
        videoView.classList.add('hidden');
        playlistView.classList.remove('hidden'); 
    });

    // --- VIEW RENDERING ---
    function showPlaylist(playlistId) {
        const playlist = playlistsData.find(p => p.id == playlistId);
        const songs = songsData[playlistId] || [];
        
        currentPlaylistId = playlistId;

        playlistCover.src = playlist.cover;
        playlistName.textContent = playlist.name;

        songList.innerHTML = '';
        songs.forEach((song, index) => {
            const songEl = document.createElement('div');
            songEl.classList.add('song');
            songEl.innerHTML = `
                 <div class="song-details">
                    <div class="song-title">${song.title}</div>
                    <div class="song-artist">${song.artist}</div>
                </div>
                ${song.type === 'video' ? '<i class="fas fa-video"></i>' : '<i class="fas fa-ellipsis-v"></i>'}
            `;
            songEl.addEventListener('click', () => {
                playSongFromPlaylist(playlistId, index);
            });
            songList.appendChild(songEl);
        });

        homeView.classList.add('hidden');
        playlistView.classList.remove('hidden');
        playlistView.scrollTop = 0;
    }

    // --- PLAYER LOGIC ---
    function playSongFromPlaylist(playlistId, songIndex) {
        currentPlaylistId = playlistId;
        currentSongIndex = songIndex;
        const song = songsData[playlistId][songIndex];

        // Stop any currently playing media
        audioPlayer.pause();
        videoPlayer.pause();
        
        const playlist = playlistsData.find(p => p.id == playlistId);
        updateMiniPlayerUI(song.title, song.artist, playlist.cover);
        updateMediaSession(song, playlist);

        if (song.type === 'video') {
            activePlayer = 'video';
            videoPlayer.src = song.url;
            videoPlayer.play();
            videoView.classList.remove('hidden');
            playlistView.classList.add('hidden');
        } else {
            activePlayer = 'audio';
            audioPlayer.src = song.url;
            audioPlayer.play();
        }
        
        setPlayIcon(false); // Set to pause icon
    }

    function updateMiniPlayerUI(title, artist, albumArt) {
        currentSongEl.textContent = title;
        currentArtistEl.textContent = artist;
        currentAlbumArt.src = albumArt;
        miniPlayer.classList.remove('hidden');
    }
    
    function setPlayIcon(isPaused) {
        if (isPaused) {
            playPauseBtn.classList.remove('fa-pause');
            playPauseBtn.classList.add('fa-play');
        } else {
            playPauseBtn.classList.remove('fa-play');
            playPauseBtn.classList.add('fa-pause');
        }
    }

    function togglePlayPause() {
         if (!activePlayer) return;

        const player = (activePlayer === 'video') ? videoPlayer : audioPlayer;

        if (player.paused) {
            player.play();
        } else {
            player.pause();
        }
    }

    function playNext() {
        const currentPlaylist = songsData[currentPlaylistId];
        if (currentPlaylist && currentSongIndex < currentPlaylist.length - 1) {
            playSongFromPlaylist(currentPlaylistId, currentSongIndex + 1);
        }
    }

    function playPrevious() {
        if (currentSongIndex > 0) {
            playSongFromPlaylist(currentPlaylistId, currentSongIndex - 1);
        } else { // If first song, restart it
            const player = (activePlayer === 'video') ? videoPlayer : audioPlayer;
            player.currentTime = 0;
            player.play();
        }
    }

    // --- EVENT LISTENERS ---
    playPauseBtn.addEventListener('click', togglePlayPause);
    nextButton.addEventListener('click', playNext);
    prevButton.addEventListener('click', playPrevious);
    
    miniPlayer.addEventListener('click', (e) => {
        // Don't do anything if a control button was clicked
        if (e.target.tagName === 'I') return;

        if (activePlayer === 'video') {
            videoView.classList.remove('hidden');
            playlistView.classList.add('hidden'); 
            homeView.classList.add('hidden');
        }
    });

    audioPlayer.addEventListener('play', () => setPlayIcon(false));
    audioPlayer.addEventListener('pause', () => setPlayIcon(true));
    audioPlayer.addEventListener('ended', playNext);

    videoPlayer.addEventListener('play', () => setPlayIcon(false));
    videoPlayer.addEventListener('pause', () => setPlayIcon(true));
    videoPlayer.addEventListener('ended', () => {
        playNext();
        // After video ends, hide it and show playlist
        videoView.classList.add('hidden');
        playlistView.classList.remove('hidden');
    });

    // --- MEDIA SESSION API INTEGRATION ---
    function updateMediaSession(song, playlist) {
        if ('mediaSession' in navigator) {
            navigator.mediaSession.metadata = new MediaMetadata({
                title: song.title,
                artist: song.artist,
                album: playlist.name,
                artwork: [
                    { src: playlist.cover, sizes: '256x256', type: 'image/png' },
                ]
            });

            navigator.mediaSession.setActionHandler('play', togglePlayPause);
            navigator.mediaSession.setActionHandler('pause', togglePlayPause);
            navigator.mediaSession.setActionHandler('previoustrack', playPrevious);
            navigator.mediaSession.setActionHandler('nexttrack', playNext);
        }
    }
});
