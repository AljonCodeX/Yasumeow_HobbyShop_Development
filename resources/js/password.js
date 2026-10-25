// Eye button on password fields: toggles between hidden and visible text
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.password-toggle').forEach(function (button) {
        const input = button.parentElement.querySelector('input');
        const icon  = button.querySelector('i');

        if (!input || !icon) return;

        button.addEventListener('click', function () {
            const show = input.type === 'password';

            input.type = show ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !show);
            icon.classList.toggle('bi-eye-slash', show);
            button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });
});