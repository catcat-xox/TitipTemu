@php
    $editing = isset($report) && $report !== null;
@endphp
<form class="form-panel" method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif
    <div class="row g-4">
        <div class="col-md-6">
            <label class="form-label" for="item_name">Nama barang *</label>
            <input class="form-control {{ $errors->has('item_name') ? 'is-invalid' : '' }}" id="item_name" name="item_name"
                value="{{ old('item_name', $editing ? $report->item_name : '') }}"
                aria-invalid="{{ $errors->has('item_name') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('item_name') ? 'error-item_name' : '' }}" required>
            @include('partials.field-error', ['name' => 'item_name'])
        </div>
        <div class="col-md-6">
            <label class="form-label" for="category">Kategori *</label>
            <select class="form-select {{ $errors->has('category') ? 'is-invalid' : '' }}" id="category" name="category"
                aria-invalid="{{ $errors->has('category') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('category') ? 'error-category' : '' }}" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}"
                        @selected(old('category', $editing ? $report->category : '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            @include('partials.field-error', ['name' => 'category'])
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Deskripsi barang *</label>
            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" id="description" name="description" rows="4"
                minlength="10" maxlength="1000"
                aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}"
                aria-describedby="hint-description {{ $errors->has('description') ? 'error-description' : '' }}" required>{{ old('description', $editing ? $report->description : '') }}</textarea>
            <div class="form-text" id="hint-description">Jangan tulis ciri unik yang sebaiknya digunakan untuk verifikasi.</div>
            @include('partials.field-error', ['name' => 'description'])
        </div>
        <div class="col-md-6">
            <label class="form-label" for="lost_at">Tanggal ditemukan *</label>
            <input class="form-control {{ $errors->has('lost_at') ? 'is-invalid' : '' }}" type="date" id="lost_at" name="lost_at"
                value="{{ old('lost_at', $editing ? $report->lost_at->toDateString() : '') }}"
                max="{{ now()->toDateString() }}"
                aria-invalid="{{ $errors->has('lost_at') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('lost_at') ? 'error-lost_at' : '' }}" required>
            @include('partials.field-error', ['name' => 'lost_at'])
        </div>
        <div class="col-md-6">
            <label class="form-label" for="location">Lokasi ditemukan *</label>
            <input class="form-control {{ $errors->has('location') ? 'is-invalid' : '' }}" id="location" name="location"
                value="{{ old('location', $editing ? $report->location : '') }}" maxlength="150"
                aria-invalid="{{ $errors->has('location') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('location') ? 'error-location' : '' }}" required>
            @include('partials.field-error', ['name' => 'location'])
        </div>
        <div class="col-12">
            <label class="form-label" for="contact_phone">No. HP penemu *</label>
            <input class="form-control {{ $errors->has('contact_phone') ? 'is-invalid' : '' }}" type="tel" inputmode="tel" id="contact_phone" name="contact_phone"
                value="{{ old('contact_phone', $editing ? $report->contact_phone : '') }}"
                placeholder="Contoh: 081234567890" maxlength="30"
                aria-invalid="{{ $errors->has('contact_phone') ? 'true' : 'false' }}"
                aria-describedby="hint-contact_phone {{ $errors->has('contact_phone') ? 'error-contact_phone' : '' }}" required>
            <div class="form-text" id="hint-contact_phone">Nomor hanya dipakai untuk tombol WhatsApp setelah laporan disetujui dan tidak ditampilkan sebagai teks di halaman publik.</div>
            @include('partials.field-error', ['name' => 'contact_phone'])
        </div>
        <div class="col-12">
            <label class="form-label" for="photo">Foto barang{{ $editing ? '' : ' *' }}</label>
            <input class="form-control {{ $errors->has('photo') ? 'is-invalid' : '' }}" type="file" id="photo" name="photo"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                aria-invalid="{{ $errors->has('photo') ? 'true' : 'false' }}"
                aria-describedby="hint-photo {{ $errors->has('photo') ? 'error-photo' : '' }}" @if (! $editing) required @endif>
            <div class="form-text" id="hint-photo">JPG, JPEG, PNG, atau WebP; maksimal 2 MB.{{ $editing ? ' Biarkan kosong untuk mempertahankan foto lama.' : '' }}</div>
            @include('partials.field-error', ['name' => 'photo'])
        </div>
        @unless ($editing)
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input {{ $errors->has('terms_accepted') ? 'is-invalid' : '' }}" type="checkbox" id="terms_accepted" name="terms_accepted" value="1"
                        @checked(old('terms_accepted'))
                        aria-invalid="{{ $errors->has('terms_accepted') ? 'true' : 'false' }}"
                        aria-describedby="{{ $errors->has('terms_accepted') ? 'error-terms_accepted' : '' }}" required>
                    <label class="form-check-label" for="terms_accepted">
                        Saya menyetujui ketentuan pelaporan barang temuan dan memastikan informasi ini benar.
                    </label>
                    @include('partials.field-error', ['name' => 'terms_accepted'])
                </div>
            </div>
        @endunless
        <div class="col-12 d-flex flex-column flex-sm-row justify-content-sm-end gap-2">
            <a class="btn btn-outline-secondary btn-lg" href="{{ route('reports.mine') }}">Kembali</a>
            <button class="btn btn-primary btn-lg" type="submit">{{ $submitLabel }}</button>
        </div>
    </div>
</form>
