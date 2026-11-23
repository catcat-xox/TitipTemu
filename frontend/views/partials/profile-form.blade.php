<form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data"
    class="profile-form">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Nama</label>
            <input id="name" name="name" type="text"
                value="{{ old('name', $user->name) }}"
                class="form-control @error('name') is-invalid @enderror" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="phone">No. HP</label>
            <input id="phone" name="phone" type="tel"
                value="{{ old('phone', $user->phone) }}" autocomplete="tel"
                class="form-control @error('phone') is-invalid @enderror" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="nim_nip">NIM/NIP</label>
            <input id="nim_nip" name="nim_nip" type="text"
                value="{{ old('nim_nip', $user->nim_nip) }}"
                class="form-control @error('nim_nip') is-invalid @enderror" required>
            @error('nim_nip')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="user_type">Status civitas</label>
            <select id="user_type" name="user_type"
                class="form-select @error('user_type') is-invalid @enderror" required>
                @foreach ($typeLabels as $value => $label)
                    <option value="{{ $value }}" @selected(old('user_type', $user->user_type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('user_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="faculty">Fakultas</label>
            <input id="faculty" name="faculty" type="text"
                value="{{ old('faculty', $user->faculty) }}"
                class="form-control @error('faculty') is-invalid @enderror" required>
            @error('faculty')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="study_program">Program studi <span class="text-muted fw-normal">(wajib diisi mahasiswa)</span></label>
            <input id="study_program" name="study_program" type="text"
                value="{{ old('study_program', $user->study_program) }}"
                class="form-control @error('study_program') is-invalid @enderror">
            @error('study_program')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label" for="profile_photo">Foto profil <span class="text-muted fw-normal">(opsional)</span></label>
            <input id="profile_photo" name="profile_photo" type="file"
                accept="image/jpeg,image/png,image/webp"
                class="form-control @error('profile_photo') is-invalid @enderror">
            @error('profile_photo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mt-3">
        <button type="submit" class="btn btn-primary">Simpan profil</button>
        <a class="btn btn-outline-secondary" href="{{ route('user.dashboard') }}">Kembali ke Akun</a>
    </div>
</form>
