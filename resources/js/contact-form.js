/**
 * Contact form — AJAX submission with toast feedback.
 */
export default function initContactForm() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn?.innerHTML;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        clearErrors(form);

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
                window.Toast.show('contactToast', 'success', data.message || 'Message sent successfully!');
                form.reset();
            } else if (response.status === 422) {
                showErrors(form, data.errors);
            } else {
                window.Toast.show('contactToast', 'error', data.message || 'Something went wrong. Please try again.');
            }
        } catch (err) {
            window.Toast.show('contactToast', 'error', 'Network error. Please check your connection.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    });
}

function clearErrors(form) {
    form.querySelectorAll('.field-error').forEach(el => el.remove());
    form.querySelectorAll('.border-red-600').forEach(el => el.classList.remove('border-red-600'));
}

const alertCircleSvg = '<svg class=\"w-3 h-3 shrink-0\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><circle cx=\"12\" cy=\"12\" r=\"10\"/><line x1=\"12\" y1=\"8\" x2=\"12\" y2=\"12\"/><line x1=\"12\" y1=\"16\" x2=\"12.01\" y2=\"16\"/></svg>';

function showErrors(form, errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('border-red-600');
            const errorEl = document.createElement('p');
            errorEl.className = 'field-error mt-1 text-xs sm:text-sm font-medium text-fg-danger-strong flex items-center gap-1';
            errorEl.innerHTML = alertCircleSvg + ' <span>' + messages[0] + '</span>';
            input.parentNode.appendChild(errorEl);
        }
    }
}
