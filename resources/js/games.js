// Games page: category filter chips
document.addEventListener('DOMContentLoaded', function () {
    const chips    = document.querySelectorAll('.games-chip');
    const sections = document.querySelectorAll('.games-section');
    const empty    = document.getElementById('gamesEmpty');

    // Not on the games page, nothing to do
    if (!chips.length) return;

    function apply(active) {
        let total = 0;

        sections.forEach(function (section) {
            let shown = 0;

            section.querySelectorAll('.game-col').forEach(function (col) {
                const visible = active === 'all' || col.dataset.section === active;
                col.classList.toggle('d-none', !visible);
                if (visible) shown++;
            });

            section.classList.toggle('d-none', shown === 0);
            total += shown;
        });

        empty.classList.toggle('d-none', total !== 0);
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