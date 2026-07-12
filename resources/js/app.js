import './bootstrap';
import 'flowbite';
import initContactForm from './contact-form';
import initCookieConsent from './cookie-consent';

document.addEventListener('DOMContentLoaded', () => {
    initContactForm();
    initCookieConsent();
});
