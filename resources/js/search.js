// Search suggestions: shows matching products while typing in the search window.
// The page text is built with textContent (not innerHTML), so a product name can never inject HTML.
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    const box   = document.getElementById('searchSuggestions');
    const modal = document.getElementById('searchModal');

    // No search box on this page, nothing to do
    if (!input || !box) return;

    let timer  = null;
    let latest = 0; // counts searches, so an answer that arrives late is ignored

    // Bolds the part of the name that matches what was typed
    function highlight(text, query) {
        const fragment = document.createDocumentFragment();
        const start = text.toLowerCase().indexOf(query.toLowerCase());

        if (start === -1) {
            fragment.append(text);
            return fragment;
        }

        const end = start + query.length;
        const match = document.createElement('strong');
        match.className = 'suggestion-match';
        match.textContent = text.slice(start, end);

        fragment.append(text.slice(0, start), match, text.slice(end));

        return fragment;
    }

    function render(items, query) {
        box.innerHTML = '';

        if (items.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'suggestion-empty';
            empty.textContent = 'No products found for "' + query + '".';
            box.appendChild(empty);
            return;
        }

        items.forEach(function (item) {
            const link = document.createElement('a');
            link.className = 'suggestion';
            link.href = item.url;

            // Picture, or the category icon when the product has no image
            const thumb = document.createElement('span');
            thumb.className = 'suggestion-thumb';

            if (item.image) {
                const img = document.createElement('img');
                img.src = item.image;
                img.alt = '';
                thumb.appendChild(img);
            } else {
                const icon = document.createElement('i');
                icon.className = 'bi ' + item.icon;
                thumb.appendChild(icon);
            }

            // Name and category
            const info = document.createElement('span');
            info.className = 'suggestion-info';

            const name = document.createElement('span');
            name.className = 'suggestion-name';
            name.appendChild(highlight(item.name, query));

            const category = document.createElement('span');
            category.className = 'suggestion-cat';
            category.textContent = item.category;

            info.append(name, category);

            // Price, or "Sold out"
            const price = document.createElement('span');
            price.className = 'suggestion-price';

            if (item.stock === 'out') {
                price.classList.add('is-out');
                price.textContent = 'Sold out';
            } else {
                price.textContent = item.price;
            }

            link.append(thumb, info, price);
            box.appendChild(link);
        });

        // Link to the full results page
        const all = document.createElement('a');
        all.className = 'suggestion-all';
        all.href = box.dataset.shop + '?q=' + encodeURIComponent(query);
        all.textContent = 'See all results for "' + query + '"';
        box.appendChild(all);
    }

    function search(query) {
        const id = ++latest;

        fetch(box.dataset.url + '?q=' + encodeURIComponent(query), {
            headers: { 'Accept': 'application/json' },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (items) {
                // A newer search was started while waiting, so skip this answer
                if (id !== latest) return;

                render(items, query);
            })
            .catch(function () {
                if (id === latest) box.innerHTML = '';
            });
    }

    // Wait a moment after typing stops, so we don't search on every key
    input.addEventListener('input', function () {
        const query = input.value.trim();

        clearTimeout(timer);
        latest++;

        if (query.length < 2) {
            box.innerHTML = '';
            return;
        }

        timer = setTimeout(function () {
            search(query);
        }, 250);
    });

    // Start fresh next time the search window opens
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            clearTimeout(timer);
            latest++;
            box.innerHTML = '';
            input.value = '';
        });
    }
});