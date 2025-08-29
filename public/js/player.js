document.addEventListener('DOMContentLoaded', () => {
    const player = videojs('moviePlayer');
    const fullscreenToggle = document.querySelector('.fullscreen-toggle');
    const playerWrapper = document.querySelector('.video-player-wrapper');

    if (fullscreenToggle && player) {
        fullscreenToggle.addEventListener('click', () => {
            if (player.isFullscreen()) {
                player.exitFullscreen();
            } else {
                player.requestFullscreen();
            }
        });
        
        player.on('fullscreenchange', () => {
            const enterIcon = fullscreenToggle.querySelector('.icon-fullscreen-enter');
            const exitIcon = fullscreenToggle.querySelector('.icon-fullscreen-exit');
            if (player.isFullscreen()) {
                enterIcon.style.display = 'none';
                exitIcon.style.display = 'block';
            } else {
                enterIcon.style.display = 'block';
                exitIcon.style.display = 'none';
            }
        });
    }
});