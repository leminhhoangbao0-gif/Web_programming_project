(function () {
    document.querySelectorAll('[data-qty-cap]').forEach(input => {
        input.addEventListener('change', () => {
            const max = parseInt(input.max, 10) || 1;
            let v = parseInt(input.value, 10) || 1;
            input.value = Math.min(Math.max(v, 1), max);
        });
    });

    const slider = document.querySelector('[data-slider]');
    if (slider) {
        const slides = slider.querySelectorAll('.slide');
        let i = 0;
        if (slides.length > 1) setInterval(() => {
            slides[i].classList.remove('active');
            i = (i + 1) % slides.length;
            slides[i].classList.add('active');
        }, 4000);
    }
})();
