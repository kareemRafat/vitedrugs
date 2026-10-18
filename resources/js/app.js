import './bootstrap';
import 'flowbite';
import { show as showToast, hide as hideToast } from './toast';
import initCookieConsent from './cookie-consent';
import initBackToTop from './back-to-top';

window.Toast = { show: showToast, hide: hideToast };

document.addEventListener('DOMContentLoaded', () => {
    initCookieConsent();
    initBackToTop();
});

document.addEventListener('livewire:navigated', () => {
    initCookieConsent();
    initBackToTop();
});
