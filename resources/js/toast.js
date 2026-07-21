const SVGS = {
    success: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/></svg>',
    error: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>',
    warning: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
    info: '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
};

const COLOR_MAP = {
    success: 'text-fg-success bg-success-soft',
    error: 'text-fg-danger bg-danger-soft',
    warning: 'text-body bg-amber-50',
    info: 'text-body bg-blue-50',
};

export function show(id, type = 'success', message = '') {
    const toast = document.getElementById(id);
    if (!toast) return;

    const msgEl = toast.querySelector('.toast-message');
    if (msgEl) msgEl.textContent = message;

    const iconBox = toast.querySelector('.inline-flex.items-center.justify-center.shrink-0');
    if (iconBox) {
        iconBox.className = 'inline-flex items-center justify-center shrink-0 w-7 h-7 ' + (COLOR_MAP[type] || COLOR_MAP.success) + ' rounded';
        const svg = iconBox.querySelector('svg');
        if (svg) svg.outerHTML = SVGS[type] || SVGS.success;
    }

    toast.classList.remove('hidden', 'translate-x-full', 'opacity-0');
    toast.classList.add('translate-x-0', 'opacity-100');

    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => hide(id), 5000);

    const dismissBtn = toast.querySelector('[data-dismiss-target]');
    if (dismissBtn) dismissBtn.onclick = () => hide(id);
}

export function hide(id) {
    const toast = document.getElementById(id);
    if (!toast) return;
    toast.classList.add('translate-x-full', 'opacity-0');
    setTimeout(() => toast.classList.add('hidden'), 300);
}
