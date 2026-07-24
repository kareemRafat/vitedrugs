import './bootstrap';
import 'flowbite';
import { show as showToast, hide as hideToast } from './toast';
import initCookieConsent from './cookie-consent';

window.Toast = { show: showToast, hide: hideToast };

document.addEventListener('DOMContentLoaded', () => {
    initCookieConsent();
});

document.addEventListener('livewire:navigated', () => {
    initCookieConsent();
});
