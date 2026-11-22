import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const toastStack = document.querySelector('[data-tt-toast-lifetime]');

if (toastStack) {
    const lifetime = Number(toastStack.dataset.ttToastLifetime) || 3000;

    toastStack.querySelectorAll('[data-tt-toast]').forEach((toast) => {
        window.setTimeout(() => toast.remove(), lifetime);
    });
}
