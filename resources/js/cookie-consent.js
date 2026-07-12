export default function initCookieConsent() {
    const banner = document.getElementById('cookieConsent');
    const acceptBtn = document.getElementById('cookieAccept');

    if (!banner || !acceptBtn) return;

    const storageKey = 'cookies_accepted_vetpedia';

    // Check if already accepted
    if (localStorage.getItem(storageKey) === 'true') {
        banner.remove();
        return;
    }

    // Slide in after a short delay
    requestAnimationFrame(() => {
        banner.classList.remove('translate-y-full', 'opacity-0');
        banner.classList.add('translate-y-0', 'opacity-100');
    });

    // Accept handler
    acceptBtn.addEventListener('click', () => {
        localStorage.setItem(storageKey, 'true');
        banner.classList.remove('translate-y-0', 'opacity-100');
        banner.classList.add('translate-y-full', 'opacity-0');
        setTimeout(() => banner.remove(), 500);
    });
}
