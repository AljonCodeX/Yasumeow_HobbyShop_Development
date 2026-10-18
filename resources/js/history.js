// Reservation history: filters update the list in place (no page reload, address unchanged),
// so refreshing the page always returns to the default list.
import flatpickr from 'flatpickr';

document.addEventListener('DOMContentLoaded', function () {
    const form  = document.getElementById('historyForm');
    const list  = document.getElementById('historyList');
    const input = document.getElementById('dateRange');
    const from  = document.getElementById('dateFrom');
    const to    = document.getElementById('dateTo');

    // Not on the reservations page, nothing to do
    if (!form || !list || !input || !from || !to) return;

    const status = document.getElementById('statusInput');
    const statusLabel = document.getElementById('statusLabel');
    const statusItems = form.querySelectorAll('.status-item');
    const search = form.querySelector('input[name="q"]');
    let latest = 0; // counts requests, so a late answer is ignored

    // If the page was opened with a filter (for example from a dashboard card),
    // show it once, but keep the address clean so a refresh gives the default list
    if (window.location.search) {
        window.history.replaceState(null, '', window.location.pathname);
    }

    function load(url) {
        const id = ++latest;
        list.style.opacity = '.5';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (response) { return response.text(); })
            .then(function (html) {
                if (id !== latest) return;

                const doc   = new DOMParser().parseFromString(html, 'text/html');
                const fresh = doc.getElementById('historyList');
                if (fresh) list.innerHTML = fresh.innerHTML;

                list.style.opacity = '';
            })
            .catch(function () {
                if (id === latest) list.style.opacity = '';
            });
    }

    function send() {
        const params = new URLSearchParams();

        if (from.value) params.set('from', from.value);
        if (to.value) params.set('to', to.value);
        if (status && status.value) params.set('status', status.value);
        if (search && search.value.trim()) params.set('q', search.value.trim());

        const query = params.toString();
        load(form.getAttribute('action') + (query ? '?' + query : ''));
    }

    const picker = flatpickr(input, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        defaultDate: [from.value, to.value].filter(Boolean),
        onChange: function (dates) {
            // Wait until both dates are picked
            if (dates.length === 2) {
                from.value = flatpickr.formatDate(dates[0], 'Y-m-d');
                to.value   = flatpickr.formatDate(dates[1], 'Y-m-d');
                send();
            }
        },
    });

    // Double-click the date box to clear the dates
    input.addEventListener('dblclick', function () {
        if (!from.value && !to.value) return;

        picker.clear();
        from.value = '';
        to.value = '';
        send();
    });

    statusItems.forEach(function (item) {
        item.addEventListener('click', function () {
            status.value = item.dataset.value;
            statusLabel.textContent = item.dataset.label;

            statusItems.forEach(function (other) {
                other.classList.toggle('active', other === item);
            });

            send();
        });
    });

    if (search) {
        let timer = null;

        search.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(send, 400);
        });
    }

    // Enter in the search box
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        send();
    });

    // Pagination links also load in place and keep the current filters
    list.addEventListener('click', function (event) {
        const link = event.target.closest('.pagination a');
        if (!link) return;

        event.preventDefault();
        load(link.href);
    });
});