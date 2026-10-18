// Light / dark mode.
// The saved choice is applied by a small script in the layout <head>, so the page doesn't
// flash the wrong colors. This file handles the navbar button and the Settings page.
//
// Saved choice in localStorage ('ym-theme'): 'light' or 'dark'.
// Nothing saved means "System": follow the device setting.
document.addEventListener('DOMContentLoaded', function () {
    const root    = document.documentElement;
    const buttons = document.querySelectorAll('[data-theme-toggle]');
    const options = document.querySelectorAll('input[name="theme"]');
    const system  = window.matchMedia('(prefers-color-scheme: dark)');

    // No theme controls on this page, nothing to do
    if (!buttons.length && !options.length) return;

    // Returns 'light', 'dark' or 'system'
    function getChoice() {
        let saved = null;

        try {
            saved = localStorage.getItem('ym-theme');
        } catch (e) {}

        return saved === 'light' || saved === 'dark' ? saved : 'system';
    }

    // Saves the choice (can fail in private browsing, so it is wrapped)
    function saveChoice(choice) {
        try {
            if (choice === 'system') {
                localStorage.removeItem('ym-theme');
            } else {
                localStorage.setItem('ym-theme', choice);
            }
        } catch (e) {}
    }

    // Puts the real theme on the page
    function applyTheme() {
        const choice = getChoice();
        const theme  = choice === 'system' ? (system.matches ? 'dark' : 'light') : choice;

        root.setAttribute('data-bs-theme', theme);

        // Navbar button labels
        buttons.forEach(function (button) {
            button.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        });

        // Settings page: tick the saved option
        options.forEach(function (option) {
            option.checked = option.value === choice;
        });
    }

    // Navbar button: flip the current look
    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            saveChoice(root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark');
            applyTheme();
        });
    });

    // Settings page: Light, Dark or System
    options.forEach(function (option) {
        option.addEventListener('change', function () {
            saveChoice(option.value);
            applyTheme();
        });
    });

    // In System mode, follow the device when its setting changes
    system.addEventListener('change', function () {
        if (getChoice() === 'system') applyTheme();
    });

    applyTheme();
});