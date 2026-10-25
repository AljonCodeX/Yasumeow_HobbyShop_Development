// Games page: category filter chips
document.addEventListener('DOMContentLoaded', function () {
    const chips = document.querySelectorAll('.shop-chip[data-filter]');
    const cols  = document.querySelectorAll('.game-col');

    // Not on the games page, nothing to do
    if (!chips.length) return;

    function apply(active) {
        cols.forEach(function (col) {
            const visible = active === 'all' || col.dataset.section === active;
            col.classList.toggle('d-none', !visible);
        });
    }

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            chips.forEach(function (c) {
                c.classList.toggle('active', c === chip);
            });
            apply(chip.dataset.filter);
        });
    });
});