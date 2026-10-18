// Light / dark mode, set from the Settings page.
// The saved choice is applied by a small script in the layout <head>, so the page doesn't
// flash the wrong colors. This file handles the Settings page options.
//
// Saved choice in localStorage ('ym-theme'): 'light' or 'dark'.
// Nothing saved means "System": follow the device setting.
document.addEventListener('DOMContentLoaded', function () {
    const root    = document.documentElement;
    const options = document.querySelectorAll('input[name="theme"]');
    const system  = window.matchMedia('(prefers-color-scheme: dark)');

    // Not on the Settings page, nothing to do
    if (!options.length) return;

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

    // Puts the real theme on the page and ticks the saved option
    function applyTheme() {
        const choice = getChoice();
        const theme  = choice === 'system' ? (system.matches ? 'dark' : 'light') : choice;

        root.setAttribute('data-bs-theme', theme);

        options.forEach(function (option) {
            option.checked = option.value === choice;
        });
    }

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