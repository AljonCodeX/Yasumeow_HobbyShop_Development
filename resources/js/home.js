// Home page: "Show more categories" button
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('categoryToggle');

    // Not on the home page, or not enough categories to need the button
    if (!toggle) return;

    const extras = document.querySelectorAll('.category-extra');
    const moreText = toggle.textContent.trim();
    let open = false;

    toggle.addEventListener('click', function () {
        open = !open;

        extras.forEach(function (tile) {
            tile.classList.toggle('d-none', !open);
        });

        toggle.textContent = open ? 'Show less' : moreText;
        toggle.setAttribute('aria-expanded', open);
    });
});