import { Dropdown, Modal } from 'bootstrap';

const toastStack = document.querySelector('[data-tt-toast-lifetime]');

if (toastStack) {
    const lifetime = Number(toastStack.dataset.ttToastLifetime) || 3000;

    toastStack.querySelectorAll('[data-tt-toast]').forEach((toast) => {
        window.setTimeout(() => toast.remove(), lifetime);
    });
}

// Menu aksi berada di dalam tabel yang bisa digulir; strategi fixed mencegah
// dropdown terpotong oleh batas overflow container tersebut.
document.querySelectorAll('[data-tt-action-menu]').forEach((toggle) => {
    new Dropdown(toggle, { popperConfig: { strategy: 'fixed' } });
});

const confirmModal = document.getElementById('ttConfirmModal');

if (confirmModal) {
    const modal = Modal.getOrCreateInstance(confirmModal);
    const title = confirmModal.querySelector('[data-tt-confirm-title]');
    const message = confirmModal.querySelector('[data-tt-confirm-message]');
    const accept = confirmModal.querySelector('[data-tt-confirm-accept]');
    const icons = confirmModal.querySelectorAll('[data-tt-confirm-icon]');
    let pendingForm = null;

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (! (form instanceof HTMLFormElement) || ! form.hasAttribute('data-tt-confirm')) {
            return;
        }

        event.preventDefault();

        if (pendingForm) {
            return;
        }

        const requestedVariant = form.dataset.ttConfirmVariant;
        const variant = ['primary', 'success', 'danger'].includes(requestedVariant)
            ? requestedVariant
            : 'primary';

        title.textContent = form.dataset.ttConfirmTitle || 'Konfirmasi tindakan';
        message.textContent = form.dataset.ttConfirm;
        accept.textContent = form.dataset.ttConfirmLabel || 'Ya, lanjutkan';
        confirmModal.dataset.ttVariant = variant;
        icons.forEach((icon) => {
            icon.hidden = icon.dataset.ttConfirmIcon !== variant;
        });

        pendingForm = form;
        modal.show();
    });

    accept.addEventListener('click', () => {
        if (! pendingForm) {
            return;
        }

        const form = pendingForm;
        pendingForm = null;
        accept.disabled = true;

        modal.hide();
        form.submit();
    });

    confirmModal.addEventListener('hidden.bs.modal', () => {
        pendingForm = null;
        accept.disabled = false;
    });
}
