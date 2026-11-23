{{-- Modal konfirmasi bersama. Dipakai layout publik dan Admin sebagai pengganti dialog bawaan browser. --}}
<div class="modal fade tt-confirm-modal" id="ttConfirmModal" tabindex="-1"
    aria-labelledby="ttConfirmTitle" aria-describedby="ttConfirmMessage" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body tt-confirm-modal__body">
                <div class="tt-confirm-modal__icon" aria-hidden="true">
                    <svg data-tt-confirm-icon="primary" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" hidden>
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9.6 9a2.5 2.5 0 1 1 3.6 2.2c-.8.4-1.2 1-1.2 1.9" />
                        <path d="M12 17h.01" />
                    </svg>
                    <svg data-tt-confirm-icon="success" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" hidden>
                        <circle cx="12" cy="12" r="9" />
                        <path d="m8.5 12.2 2.4 2.4 4.6-5" />
                    </svg>
                    <svg data-tt-confirm-icon="danger" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" hidden>
                        <path d="M10.3 3.9 2.5 17.2A2 2 0 0 0 4.2 20h15.6a2 2 0 0 0 1.7-2.8L13.7 3.9a2 2 0 0 0-3.4 0Z" />
                        <path d="M12 9v4" />
                        <path d="M12 17h.01" />
                    </svg>
                </div>
                <h2 class="tt-confirm-modal__title h4" id="ttConfirmTitle" data-tt-confirm-title>Konfirmasi tindakan</h2>
                <p class="tt-confirm-modal__message" id="ttConfirmMessage" data-tt-confirm-message></p>
            </div>
            <div class="modal-footer tt-confirm-modal__footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary tt-confirm-modal__accept" data-tt-confirm-accept>
                    Ya, lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>
