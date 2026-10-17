// Reopens the reservation drawer after +, - or remove, so it doesn't close on every click
import { Offcanvas } from 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const drawer = document.getElementById('reserveDrawer');

    if (drawer && drawer.hasAttribute('data-auto-open')) {
        Offcanvas.getOrCreateInstance(drawer).show();
    }
});