import './bootstrap';
import 'flowbite';
import { show as showToast, hide as hideToast } from './toast';
import initContactForm from './contact-form';
import initCookieConsent from './cookie-consent';

window.Toast = { show: showToast, hide: hideToast };

document.addEventListener('DOMContentLoaded', () => {
    initContactForm();
    initCookieConsent();
});
