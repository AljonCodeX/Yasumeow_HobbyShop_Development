// Light / dark mode button.
// The saved choice is applied by a small script in the layout <head>, so the page doesn't
// flash the wrong colors. This file only handles the button click.
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('[data-theme-toggle]');

    // No theme button on this page, nothing to do
    if (!buttons.length) return;

    const root = document.documentElement;

    function updateLabels() {
        const dark = root.getAttribute('data-bs-theme') === 'dark';

        buttons.forEach(function (button) {
            button.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
        });
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';

            root.setAttribute('data-bs-theme', next);

            // Remember the choice (can fail in private browsing, so it is wrapped)
            try {
                localStorage.setItem('ym-theme', next);
            } catch (e) {}

            updateLabels();
        });
    });

    updateLabels();
});