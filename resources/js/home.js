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

// Rows marked data-load-more show a few cards, then a "Load more" button reveals the rest
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-load-more]').forEach(function (row) {
        const isMobile = window.matchMedia('(max-width: 767.98px)').matches;
        const step = Number(isMobile ? row.dataset.stepMobile : row.dataset.step);
        const items = Array.from(row.children);

        // Few enough cards (or the empty message): no button needed
        if (items.length <= step) return;

        let shown = step;
        items.slice(step).forEach(function (item) {
            item.classList.add('d-none');
        });

        const wrap = document.createElement('div');
        wrap.className = 'text-center mt-3';
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-link load-more-link p-0 fw-bold text-body text-decoration-none';
        button.innerHTML = 'Load More <i class="bi bi-chevron-down ms-1"></i>';
        wrap.appendChild(button);
        row.after(wrap);

        button.addEventListener('click', function () {
            items.slice(shown, shown + step).forEach(function (item) {
                item.classList.remove('d-none');
            });
            shown += step;

            // Nothing left to show: remove the button
            if (shown >= items.length) wrap.remove();
        });
    });
});