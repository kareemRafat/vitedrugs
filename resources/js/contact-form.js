/**
 * Contact form — AJAX submission with toast feedback.
 */
export default function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    const toastEl = document.getElementById('contactToast');
    const toastMessage = toastEl?.querySelector('.toast-message');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn?.innerHTML;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Clear previous errors
        clearErrors(form);

        // Loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Sending...
            `;
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            const data = await response.json();

            if (response.ok) {
                showToast(toastEl, toastMessage, 'success', data.message || 'Message sent successfully!');
                form.reset();
            } else if (response.status === 422) {
                showErrors(form, data.errors);
            } else {
                showToast(toastEl, toastMessage, 'error', data.message || 'Something went wrong. Please try again.');
            }
        } catch (err) {
            showToast(toastEl, toastMessage, 'error', 'Network error. Please check your connection.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    });
}

function showToast(toastEl, messageEl, type, message) {
    if (!toastEl || !messageEl) return;

    // Set type classes
    const typeMap = {
        success: { icon: 'text-fg-success bg-success-soft' },
        error: { icon: 'text-fg-danger bg-danger-soft' },
    };

    const cfg = typeMap[type] || typeMap.success;
    toastEl.className = `fixed top-4 end-4 z-[100] flex items-center w-full max-w-sm p-4 text-body bg-neutral-primary-soft rounded-base shadow-xs border border-default transition-all duration-300`;

    // Update icon color
    const iconBox = toastEl.querySelector('.inline-flex.items-center.justify-center.shrink-0');
    if (iconBox) {
        iconBox.className = `inline-flex items-center justify-center shrink-0 w-7 h-7 ${cfg.icon} rounded`;
    }

    messageEl.textContent = message;

    toastEl.classList.remove('hidden', 'translate-x-full', 'opacity-0');
    toastEl.classList.add('translate-x-0', 'opacity-100');

    // Auto-dismiss after 4s
    clearTimeout(toastEl._timer);
    toastEl._timer = setTimeout(() => hideToast(toastEl), 4000);

    // Dismiss button
    const dismissBtn = toastEl.querySelector('[data-dismiss-target]');
    if (dismissBtn) {
        dismissBtn.onclick = () => hideToast(toastEl);
    }
}

function hideToast(toastEl) {
    toastEl.classList.add('translate-x-full', 'opacity-0');
    setTimeout(() => toastEl.classList.add('hidden'), 300);
}

function clearErrors(form) {
    form.querySelectorAll('.field-error').forEach(el => el.remove());
    form.querySelectorAll('.input-error').forEach(el => el.classList.remove('border-danger-subtle'));
}

function showErrors(form, errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('border-danger-subtle');
            const errorEl = document.createElement('p');
            errorEl.className = 'field-error mt-1 text-sm font-semibold text-white bg-rose-500 rounded-sm py-0.5 px-3 w-fit';
            errorEl.textContent = messages[0];
            input.parentNode.appendChild(errorEl);
        }
    }
}
