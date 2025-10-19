document.addEventListener('DOMContentLoaded', () => {
    // --- VIEWS ---
    const homeView = document.getElementById('home-view');
    const playlistView = document.getElementById('playlist-view');
    const videoView = document.getElementById('video-view');

    // --- MEDIA PLAYERS ---
    const audioPlayer = document.getElementById('audio-player');
    const videoPlayer = document.getElementById('video-player');

    // --- BUTTONS & CLICKABLE ITEMS ---
    const clickableItems = document.querySelectorAll('.playlist-item, .shelf-item');
    const backButton = document.getElementById('back-button');
    const mainNavLinks = document.querySelectorAll('.nav-item');

    // --- PLAYLIST VIEW ELEMENTS ---
    const playlistCover = document.getElementById('playlist-cover');
    const playlistName = document.getElementById('playlist-name');
    const songList = document.getElementById('song-list');

    // --- MINI PLAYER (NOW PLAYING) ELEMENTS ---
    const miniPlayer = document.getElementById('mini-player');
    const currentAlbumArt = document.getElementById('current-album-art');
    const currentSongEl = document.getElementById('current-song');
    const currentArtistEl = document.getElementById('current-artist');
    const miniPlayPauseBtn = document.getElementById('play-pause-button');
    const miniPrevButton = document.getElementById('prev-button');
    const miniNextButton = document.getElementById('next-button');

    // --- VIDEO VIEW ELEMENTS ---
    const closeVideoButton = document.getElementById('close-video-button');
    const videoPlaylistName = document.getElementById('video-playlist-name');
    const videoTitle = document.getElementById('video-title');
    const videoArtist = document.getElementById('video-artist');
    const videoPlayPauseBtn = document.getElementById('video-play-pause-button');
    const videoNextButton = document.getElementById('video-next-button');
    const videoPrevButton = document.getElementById('video-prev-button');
    const videoProgressBarContainer = document.getElementById('video-progress-bar-container');
    const videoProgress = document.getElementById('video-progress');
    const videoCurrentTime = document.getElementById('video-current-time');
    const videoDuration = document.getElementById('video-duration');

    // --- DATA STORES & STATE ---
    let songsData = {};
    let playlistsData = {};
    let currentPlaylistId = null;
    let currentSongIndex = -1;
    let activePlayer = null; // 'audio' or 'video'

    // ==========================================================================
    // INITIALIZATION
    // ==========================================================================

    // Fetch all necessary data when the app loads
    Promise.all([
        fetch('data/songs.json').then(res => res.json()),
        fetch('data/playlists.json').then(res => res.json())
    ]).then(([songs, playlists]) => {
        songsData = songs;
        playlistsData = playlists;
        // You could initialize the first view here if needed
    });

    // ==========================================================================
    // NAVIGATION
    // ==========================================================================

    clickableItems.forEach(item => {
        item.addEventListener('click', (e) => {
            const playlistId = e.currentTarget.dataset.playlistId;
            showPlaylist(playlistId);
        });
    });

    backButton.addEventListener('click', showHomeView);

    // Close video view but keep playing in background
    closeVideoButton.addEventListener('click', () => {
        videoView.classList.remove('visible');
    });

    mainNavLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetPage = e.currentTarget.querySelector('span').textContent;
            if (targetPage === 'Kezdőlap') {
                showHomeView();
            }
            // TODO: Implement other pages like Search, Library
            mainNavLinks.forEach(l => l.classList.remove('active'));
            e.currentTarget.classList.add('active');
        });
    });

    function showHomeView() {
        playlistView.classList.add('hidden');
        homeView.classList.remove('hidden');
        videoView.classList.remove('visible');
    }
    
    function openPlayerFullScreen() {
         if (activePlayer === 'video') {
            videoView.classList.add('visible');
        } else {
            // TODO: Future enhancement: a full screen view for the audio player
        }
    }

    // ==========================================================================
    // VIEW RENDERING
    // ==========================================================================

    function showPlaylist(playlistId) {
        const playlist = playlistsData.find(p => p.id == playlistId);
        if (!playlist) return; 

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
            songEl.addEventListener('click', () => playItem(playlistId, index));
            songList.appendChild(songEl);
        });

        homeView.classList.add('hidden');
        playlistView.classList.remove('hidden');
        playlistView.scrollTop = 0;
    }

    // ==========================================================================
    // PLAYER LOGIC (The Core)
    // ==========================================================================

    function playItem(playlistId, songIndex) {
        currentPlaylistId = playlistId;
        currentSongIndex = songIndex;
        const item = songsData[playlistId][songIndex];
        const playlist = playlistsData.find(p => p.id == playlistId);

        // Stop any currently playing media before starting new one
        audioPlayer.pause();
        videoPlayer.pause();

        updateMiniPlayerUI(item.title, item.artist, playlist.cover, item.type);
        updateMediaSession(item, playlist);

        if (item.type === 'video') {
            activePlayer = 'video';
            videoPlayer.src = item.url;
            videoPlayer.play();
            updateVideoPlayerUI(item, playlist);
            videoView.classList.add('visible');
        } else {
            activePlayer = 'audio';
            audioPlayer.src = item.url;
            audioPlayer.play();
            videoView.classList.remove('visible'); // Hide video view if audio starts
        }
        
        updatePlayPauseIcons(false); // Set all icons to 'pause'
    }

    function togglePlayPause() {
        if (!activePlayer) return;
        const player = (activePlayer === 'video') ? videoPlayer : audioPlayer;
        player.paused ? player.play() : player.pause();
    }

    function playNext() {
        const currentPlaylist = songsData[currentPlaylistId];
        if (currentPlaylist && currentSongIndex < currentPlaylist.length - 1) {
            playItem(currentPlaylistId, currentSongIndex + 1);
        } else {
             // Optional: Stop or loop when playlist ends
        }
    }

    function playPrevious() {
        if (currentSongIndex > 0) {
            playItem(currentPlaylistId, currentSongIndex - 1);
        } else { 
            const player = (activePlayer === 'video') ? videoPlayer : audioPlayer;
            player.currentTime = 0;
            player.play();
        }
    }

    // ==========================================================================
    // UI UPDATES
    // ==========================================================================

    function updateMiniPlayerUI(title, artist, albumArt, type) {
        currentSongEl.textContent = title;
        currentArtistEl.textContent = artist;
        currentAlbumArt.src = albumArt;
        miniPlayer.classList.remove('hidden');
    }

    function updateVideoPlayerUI(item, playlist) {
        videoPlaylistName.textContent = playlist.name;
        videoTitle.textContent = item.title;
        videoArtist.textContent = item.artist;
    }

    function updatePlayPauseIcons(isPaused) {
        const miniIcon = miniPlayPauseBtn.classList;
        const videoIcon = videoPlayPauseBtn.classList;
        
        miniIcon.remove('fa-play', 'fa-pause');
        videoIcon.remove('fa-play', 'fa-pause');

        if (isPaused) {
            miniIcon.add('fa-play');
            videoIcon.add('fa-play');
        } else {
            miniIcon.add('fa-pause');
            videoIcon.add('fa-pause');
        }
    }
    
    function formatTime(seconds) {
        const min = Math.floor(seconds / 60);
        const sec = Math.floor(seconds % 60).toString().padStart(2, '0');
        return `${min}:${sec}`;
    }

    // ==========================================================================
    // EVENT LISTENERS
    // ==========================================================================

    // --- Mini Player Controls ---
    miniPlayPauseBtn.addEventListener('click', togglePlayPause);
    miniNextButton.addEventListener('click', playNext);
    miniPrevButton.addEventListener('click', playPrevious);
    miniPlayer.addEventListener('click', (e) => {
        if (e.target.tagName !== 'I') { // Open full screen if not clicking a button
            openPlayerFullScreen();
        }
    });

    // --- Full Screen Video Controls ---
    videoPlayPauseBtn.addEventListener('click', togglePlayPause);
    videoNextButton.addEventListener('click', playNext);
    videoPrevButton.addEventListener('click', playPrevious);

    // --- Audio Player Events ---
    audioPlayer.addEventListener('play', () => updatePlayPauseIcons(false));
    audioPlayer.addEventListener('pause', () => updatePlayPauseIcons(true));
    audioPlayer.addEventListener('ended', playNext);

    // --- Video Player Events ---
    videoPlayer.addEventListener('play', () => updatePlayPauseIcons(false));
    videoPlayer.addEventListener('pause', () => updatePlayPauseIcons(true));
    videoPlayer.addEventListener('ended', playNext);
    videoPlayer.addEventListener('loadedmetadata', () => {
        videoDuration.textContent = formatTime(videoPlayer.duration);
    });
    videoPlayer.addEventListener('timeupdate', () => {
        const progress = (videoPlayer.currentTime / videoPlayer.duration) * 100;
        videoProgress.style.width = `${progress}%`;
        videoCurrentTime.textContent = formatTime(videoPlayer.currentTime);
    });
    videoProgressBarContainer.addEventListener('click', (e) => {
        const rect = videoProgressBarContainer.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const newTime = (clickX / rect.width) * videoPlayer.duration;
        videoPlayer.currentTime = newTime;
    });


    // ==========================================================================
    // BROWSER MEDIA SESSION INTEGRATION (for lock screen controls)
    // ==========================================================================

    function updateMediaSession(item, playlist) {
        if ('mediaSession' in navigator) {
            navigator.mediaSession.metadata = new MediaMetadata({
                title: item.title,
                artist: item.artist,
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
