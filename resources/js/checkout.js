// Checkout page: updates the "Pay now" and "Pay in store" amounts and the button text
// when a different payment option is picked
document.addEventListener('DOMContentLoaded', function () {
    const options = document.querySelectorAll('input[name="payment_option"]');
    const now     = document.getElementById('checkoutNow');
    const store   = document.getElementById('checkoutStore');
    const button  = document.getElementById('checkoutButton');

    // Not on the checkout page, nothing to do
    if (!options.length || !now || !store || !button) return;

    function peso(value) {
        return '₱' + Number(value).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    }

    function update() {
        const picked = document.querySelector('input[name="payment_option"]:checked');

        now.textContent    = peso(picked.dataset.now);
        store.textContent  = peso(picked.dataset.store);
        button.textContent = picked.dataset.button;
    }

    options.forEach(function (option) {
        option.addEventListener('change', update);
    });

    update();
});