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
            <input class="form-control" id="item_name" name="item_name"
                value="{{ old('item_name', $editing ? $report->item_name : '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="category">Kategori *</label>
            <select class="form-select" id="category" name="category" required>
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}"
                        @selected(old('category', $editing ? $report->category : '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label" for="description">Deskripsi umum *</label>
            <textarea class="form-control" id="description" name="description" rows="4"
                minlength="10" maxlength="1000" required>{{ old('description', $editing ? $report->description : '') }}</textarea>
            <div class="form-text">Jangan tulis ciri unik yang sebaiknya digunakan untuk verifikasi.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="lost_at">Tanggal kehilangan *</label>
            <input class="form-control" type="date" id="lost_at" name="lost_at"
                value="{{ old('lost_at', $editing ? $report->lost_at->toDateString() : '') }}"
                max="{{ now()->toDateString() }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="location">Lokasi kehilangan *</label>
            <input class="form-control" id="location" name="location"
                value="{{ old('location', $editing ? $report->location : '') }}" maxlength="150" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="photo">Foto barang{{ $editing ? '' : ' *' }}</label>
            <input class="form-control" type="file" id="photo" name="photo"
                accept=".jpg,.jpeg,.png,image/jpeg,image/png" @if (! $editing) required @endif>
            <div class="form-text">JPG, JPEG, atau PNG; maksimal 5 MB.{{ $editing ? ' Biarkan kosong untuk mempertahankan foto lama.' : '' }}</div>
        </div>
        @unless ($editing)
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" value="1"
                        @checked(old('terms_accepted')) required>
                    <label class="form-check-label" for="terms_accepted">
                        Saya menyetujui ketentuan pelaporan dan memastikan informasi ini benar.
                    </label>
                </div>
            </div>
        @endunless
        <div class="col-12 d-flex flex-column flex-sm-row justify-content-sm-end gap-2">
            <a class="btn btn-outline-secondary btn-lg" href="{{ route('reports.mine') }}">Kembali</a>
            <button class="btn btn-primary btn-lg" type="submit">{{ $submitLabel }}</button>
        </div>
    </div>
</form>
