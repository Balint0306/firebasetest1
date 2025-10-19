document.addEventListener('DOMContentLoaded', () => {
    // --- VIEWS ---
    const homeView = document.getElementById('home-view');
    const playlistView = document.getElementById('playlist-view');
    const videoView = document.getElementById('video-view');
    const settingsView = document.getElementById('settings-view');

    // --- MEDIA PLAYERS ---
    const audioPlayer = document.getElementById('audio-player');
    const videoPlayer = document.getElementById('video-player');

    // --- BUTTONS & CLICKABLE ITEMS ---
    const clickableItems = document.querySelectorAll('.playlist-item, .shelf-item');
    const backButton = document.getElementById('back-button');
    const mainNavLinks = document.querySelectorAll('.nav-item');
    const settingsButton = document.getElementById('settings-button');
    const settingsBackButton = document.getElementById('settings-back-button');

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
    let lastActiveView = homeView; // To remember which view to return to

    // ==========================================================================
    // INITIALIZATION
    // ==========================================================================

    Promise.all([
        fetch('data/songs.json').then(res => res.json()),
        fetch('data/playlists.json').then(res => res.json())
    ]).then(([songs, playlists]) => {
        songsData = songs;
        playlistsData = playlists.reduce((acc, p) => ({ ...acc, [p.id]: p }), {});
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
    settingsButton.addEventListener('click', showSettingsView);
    settingsBackButton.addEventListener('click', () => showView(lastActiveView));

    closeVideoButton.addEventListener('click', () => {
        videoView.classList.add('hidden');
        showView(playlistView);
    });

    mainNavLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const targetPage = e.currentTarget.querySelector('span').textContent;
            if (targetPage === 'Kezdőlap') {
                showHomeView();
            }
            mainNavLinks.forEach(l => l.classList.remove('active'));
            e.currentTarget.classList.add('active');
        });
    });
    
    function showView(viewToShow) {
        [homeView, playlistView, videoView, settingsView].forEach(v => v.classList.add('hidden'));
        viewToShow.classList.remove('hidden');
        lastActiveView = viewToShow;
    }

    function showHomeView() {
        showView(homeView);
    }
    
    function showSettingsView() {
        showView(settingsView);
    }

    function openPlayerFullScreen() {
        if (activePlayer === 'video') {
            showView(videoView);
        }
    }

    // ==========================================================================
    // VIEW RENDERING
    // ==========================================================================

    function showPlaylist(playlistId, resetScroll = true) {
        const playlist = playlistsData[playlistId];
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

        showView(playlistView);
        if(resetScroll) playlistView.scrollTop = 0;
    }

    // ==========================================================================
    // PLAYER LOGIC (The Core)
    // ==========================================================================

    function playItem(playlistId, songIndex) {
        currentPlaylistId = playlistId;
        currentSongIndex = songIndex;
        const item = songsData[playlistId][songIndex];
        const playlist = playlistsData[playlistId];

        audioPlayer.pause();
        videoPlayer.pause();

        updateMiniPlayerUI(item.title, item.artist, playlist.cover, item.type);
        updateMediaSession(item, playlist);

        if (item.type === 'video') {
            activePlayer = 'video';
            showView(videoView);
            updateVideoPlayerUI(item, playlist);
            
            videoPlayer.src = item.url;
            const playPromise = videoPlayer.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.error("Autoplay was prevented. The user needs to interact directly with the player.", error);
                    updatePlayPauseIcons(true); // Show play icon so user can start playback
                });
            }

        } else {
            activePlayer = 'audio';
            audioPlayer.src = item.url;
            audioPlayer.play();
            // If user was in video view, move them to playlist view
            if(lastActiveView === videoView) {
                showView(playlistView);
            }
        }
        
        updatePlayPauseIcons(false);
    }

    function togglePlayPause() {
        if (!activePlayer) return;
        const player = (activePlayer === 'video') ? videoPlayer : audioPlayer;
        if (player.paused) {
             const playPromise = player.play();
             if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.error("Play call was interrupted or failed.", error);
                });
            }
        } else {
            player.pause();
        }
    }

    function playNext() {
        const currentPlaylist = songsData[currentPlaylistId];
        if (currentPlaylist && currentSongIndex < currentPlaylist.length - 1) {
            playItem(currentPlaylistId, currentSongIndex + 1);
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
    // UI UPDATES & EVENT LISTENERS
    // ==========================================================================

    function updateMiniPlayerUI(title, artist, albumArt) {
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
        const iconState = isPaused ? 'fa-play' : 'fa-pause';
        miniPlayPauseBtn.className = `fas ${iconState}`;
        videoPlayPauseBtn.className = `fas ${iconState}`;
    }
    
    function formatTime(seconds) {
        const min = Math.floor(seconds / 60);
        const sec = Math.floor(seconds % 60).toString().padStart(2, '0');
        return `${min}:${sec}`;
    }

    miniPlayPauseBtn.addEventListener('click', togglePlayPause);
    miniNextButton.addEventListener('click', playNext);
    miniPrevButton.addEventListener('click', playPrevious);
    miniPlayer.addEventListener('click', (e) => {
        if (e.target.tagName !== 'I') { 
            openPlayerFullScreen();
        }
    });

    videoPlayPauseBtn.addEventListener('click', togglePlayPause);
    videoNextButton.addEventListener('click', playNext);
    videoPrevButton.addEventListener('click', playPrevious);

    const setupPlayerEvents = (player) => {
        player.addEventListener('play', () => updatePlayPauseIcons(false));
        player.addEventListener('pause', () => updatePlayPauseIcons(true));
        player.addEventListener('ended', playNext);
    };

    setupPlayerEvents(audioPlayer);
    setupPlayerEvents(videoPlayer);

    // --- NEW ROBUST ERROR HANDLING FOR VIDEO ---
    videoPlayer.addEventListener('error', (e) => {
        console.error("--- VIDEO PLAYER ERROR ---");
        console.error("Error Event:", e);
        const error = videoPlayer.error;
        if (error) {
            console.error("Error Code:", error.code);
            console.error("Error Message:", error.message);
            switch (error.code) {
                case error.MEDIA_ERR_ABORTED:
                    console.error('The video playback was aborted.');
                    break;
                case error.MEDIA_ERR_NETWORK:
                    console.error('A network error caused the video download to fail.');
                    break;
                case error.MEDIA_ERR_DECODE:
                    console.error('The video playback was aborted due to a corruption problem or unsupported features.');
                    break;
                case error.MEDIA_ERR_SRC_NOT_SUPPORTED:
                    console.error('The video could not be loaded, either because the server or network failed or because the format is not supported.');
                    break;
                default:
                    console.error('An unknown error occurred.');
                    break;
            }
        }
        // Non-blocking alert for the user
        setTimeout(() => {
            alert("Hiba a videó lejátszása közben. A forrás hibás vagy nem elérhető. Az alkalmazás működőképes marad.");
        }, 1);
        updatePlayPauseIcons(true); // Show play icon to allow user to try again or play something else
        showView(playlistView); // Go back to the playlist view to prevent being stuck
    });

    videoPlayer.addEventListener('stalled', () => {
        console.warn("Video stalled: Browser is trying to get media data, but it is not available.");
    });

    videoPlayer.addEventListener('waiting', () => {
        console.info("Video waiting: Playback has stopped temporarily due to lack of data (buffering).");
    });
    // --- END OF NEW ERROR HANDLING ---

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
