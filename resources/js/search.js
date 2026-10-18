// Universal search. The whole list is loaded once, then filtered in the browser,
// so results appear instantly while typing. Text is set with textContent, never innerHTML.
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    const box   = document.getElementById('searchSuggestions');
    const modal = document.getElementById('searchModal');

    // No search box on this page, nothing to do
    if (!input || !box) return;

    let index   = null; // the full list, loaded once
    let loading = null;

    function loadIndex() {
        if (index) return Promise.resolve(index);

        if (!loading) {
            loading = fetch(box.dataset.url, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) { index = data; return data; })
                .catch(function () { loading = null; });
        }

        return loading;
    }

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function highlight(text, query) {
        const fragment = document.createDocumentFragment();
        const start = query ? text.toLowerCase().indexOf(query.toLowerCase()) : -1;

        if (start === -1) {
            fragment.append(text);
            return fragment;
        }

        const end = start + query.length;
        fragment.append(text.slice(0, start), el('strong', 'suggestion-match', text.slice(start, end)), text.slice(end));

        return fragment;
    }

    function thumb(item) {
        const wrap = el('span', 'suggestion-thumb');

        if (item.image) {
            const img = el('img');
            img.src = item.image;
            img.alt = '';
            wrap.appendChild(img);
        } else {
            wrap.appendChild(el('i', 'bi ' + item.icon));
        }

        return wrap;
    }

    function row(item, title, sub, side, query) {
        const link = el('a', 'suggestion');
        link.href = item.url;

        const info = el('span', 'suggestion-info');
        const name = el('span', 'suggestion-name');
        name.appendChild(highlight(title, query));
        info.append(name, el('span', 'suggestion-cat', sub));

        link.append(thumb(item), info);

        if (side) {
            const price = el('span', 'suggestion-price', side.text);
            if (side.out) price.classList.add('is-out');
            link.appendChild(price);
        }

        return link;
    }

    function group(label, rows) {
        if (!rows.length) return;

        box.appendChild(el('p', 'suggestion-group', label));
        rows.forEach(function (r) { box.appendChild(r); });
    }

    // Starts-with first, then name contains, then matches in category/description
    function filter(list, q, key, limit) {
        const found = [];

        list.forEach(function (item) {
            const name = item[key].toLowerCase();
            let rank = -1;

            if (name.startsWith(q)) rank = 0;
            else if (name.indexOf(q) !== -1) rank = 1;
            else if (item.s.indexOf(q) !== -1) rank = 2;

            if (rank >= 0) found.push({ item: item, rank: rank });
        });

        found.sort(function (a, b) { return a.rank - b.rank; });

        return found.slice(0, limit).map(function (f) { return f.item; });
    }

    function renderResults(query) {
        const q = query.toLowerCase();
        const products = filter(index.products, q, 'name', 4);
        const games    = filter(index.games, q, 'name', 4);
        const events   = filter(index.events, q, 'title', 4);

        box.innerHTML = '';

        if (!products.length && !games.length && !events.length) {
            box.appendChild(el('p', 'suggestion-empty', 'Nothing found for "' + query + '".'));
            return;
        }

        group('Products', products.map(function (p) {
            return row(p, p.name, p.category,
                { text: p.stock === 'out' ? 'Sold out' : p.price, out: p.stock === 'out' }, query);
        }));

        group('Games', games.map(function (g) {
            return row(g, g.name, g.type, null, query);
        }));

        group('Events', events.map(function (e) {
            return row(e, e.title, e.when, e.fee, query);
        }));

        const all = el('a', 'suggestion-all', 'See all results for "' + query + '"');
        all.href = box.dataset.results + '?q=' + encodeURIComponent(query);
        box.appendChild(all);
    }

    function update() {
        const query = input.value.trim();

        if (query.length === 1) {
            box.innerHTML = '';
            return;
        }

        if (!index) {
            box.innerHTML = '';

            // Nothing typed yet: stay blank. Otherwise show a short message until the list arrives
            if (query.length > 0) box.appendChild(el('p', 'suggestion-empty', 'Loading...'));

            loadIndex().then(function (data) { if (data) update(); });
            return;
        }

        if (query.length === 0) box.innerHTML = '';
        else renderResults(query);
    }

    input.addEventListener('input', update);

    // Load the list quietly soon after the page opens, so it is ready before you search
    setTimeout(loadIndex, 800);

    if (modal) {
        modal.addEventListener('show.bs.modal', update);

        modal.addEventListener('hidden.bs.modal', function () {
            box.innerHTML = '';
            input.value = '';
        });
    }
});