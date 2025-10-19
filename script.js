document.addEventListener('DOMContentLoaded', () => {
    const apps = document.querySelectorAll('.app');
    const appContent = document.querySelector('.app-content');
    const appFrame = document.getElementById('app-frame');
    const closeButton = document.querySelector('.close-button');

    apps.forEach(app => {
        app.addEventListener('click', () => {
            const appName = app.dataset.app;
            appFrame.src = `${appName}.php`;
            appContent.classList.add('show');
        });
    });

    closeButton.addEventListener('click', () => {
        appContent.classList.remove('show');
        appFrame.src = 'about:blank'; // Clear the iframe
    });
});
