@if (session('status') || session('success') || session('error') || $errors->any())
    <div class="tt-toast-stack" data-tt-toast-lifetime="3000" aria-live="polite" aria-label="Pemberitahuan">
        @if (session('status'))
            <div class="tt-toast tt-toast--info" data-tt-toast role="status">
                <span class="tt-toast__icon" aria-hidden="true">i</span>
                <div class="tt-toast__body">
                    <p class="tt-toast__title">Informasi</p>
                    <p class="tt-toast__message">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="tt-toast tt-toast--success" data-tt-toast role="status">
                <span class="tt-toast__icon" aria-hidden="true">✓</span>
                <div class="tt-toast__body">
                    <p class="tt-toast__title">Berhasil</p>
                    <p class="tt-toast__message">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="tt-toast tt-toast--error" data-tt-toast role="alert">
                <span class="tt-toast__icon" aria-hidden="true">!</span>
                <div class="tt-toast__body">
                    <p class="tt-toast__title">Perlu diperiksa</p>
                    <p class="tt-toast__message">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="tt-toast tt-toast--error" data-tt-toast role="alert">
                <span class="tt-toast__icon" aria-hidden="true">!</span>
                <div class="tt-toast__body">
                    <p class="tt-toast__title">Periksa kembali data berikut:</p>
                    <ul class="tt-toast__list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
@endif
