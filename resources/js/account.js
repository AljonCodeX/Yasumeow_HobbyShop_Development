// Account Info: the camera icon opens the file picker and uploads the chosen photo
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('photoButton');
    const input  = document.getElementById('photoInput');
    const form   = document.getElementById('photoForm');

    // Not on the Account Info page, nothing to do
    if (!button || !input || !form) return;

    button.addEventListener('click', function () {
        input.click();
    });

    input.addEventListener('change', function () {
        if (input.files.length) form.submit();
    });
});