document.addEventListener('click', function (e) {
    if (e.target.classList.contains('toggle-review')) {
        const el = document.getElementById(e.target.dataset.target);
        el.classList.toggle('clamped');

        e.target.innerText = el.classList.contains('clamped')
            ? 'Teljes szöveg'
            : 'Kevesebb';
    }
});