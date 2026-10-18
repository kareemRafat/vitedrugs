const SCROLL_THRESHOLD = 400;

export default function initBackToTop() {
    const button = document.getElementById('backToTop');

    if (!button) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    // `app.css` forces `scroll-behavior: auto` for reduced motion, but that is a
    // CSS rule and cannot override the option passed to `scrollTo` below.
    const scrollToTop = () => {
        window.scrollTo({
            top: 0,
            behavior: reducedMotion.matches ? 'auto' : 'smooth',
        });
    };

    const render = () => {
        const visible = window.scrollY > SCROLL_THRESHOLD;

        button.classList.toggle('opacity-0', !visible);
        button.classList.toggle('translate-y-3', !visible);
        button.classList.toggle('pointer-events-none', !visible);
        button.classList.toggle('opacity-100', visible);
        button.classList.toggle('translate-y-0', visible);
    };

    // `livewire:navigated` re-runs every initialiser, and this element is not
    // re-rendered by that navigation, so the listener has to be bound only once.
    // Visibility is still recomputed below, since the page is at a new offset.
    if (button.dataset.scrollToTopBound !== 'true') {
        button.dataset.scrollToTopBound = 'true';

        let ticking = false;

        window.addEventListener('scroll', () => {
            if (ticking) return;

            ticking = true;
            requestAnimationFrame(() => {
                ticking = false;
                render();
            });
        }, { passive: true });

        button.addEventListener('click', () => {
            // Without moving focus, it stays on a button that has just scrolled
            // out of view, which strands keyboard and screen-reader users.
            document.querySelector('main')?.focus({ preventScroll: true });

            scrollToTop();
        });
    }

    render();
}