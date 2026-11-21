@extends('layouts.app')

@section('title', 'Daftar — TitipTemu')
@section('description', 'Buat akun TitipTemu untuk melaporkan barang hilang.') 

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-md-8 col-lg-5">
                    <div class="auth-card">
                        <div class="text-center mb-4">
                            <span class="eyebrow">Buat akun</span>
                            <h1 class="auth-title mt-3">Mulai bersama TitipTemu</h1>
                            <p class="mb-0">Daftar untuk membuat dan memantau laporan Anda.</p>
                        </div>

                        <form method="POST" action="{{ route('register.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="name">Nama</label>
                                <input id="name" name="name" type="text"
                                    value="{{ old('name') }}" autocomplete="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input id="email" name="email" type="email"
                                    value="{{ old('email') }}" autocomplete="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="nim_nip">NIM/NIP</label>
                                <input id="nim_nip" name="nim_nip" type="text"
                                    value="{{ old('nim_nip') }}" autocomplete="off"
                                    class="form-control @error('nim_nip') is-invalid @enderror"
                                    required>
                                @error('nim_nip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="user_type">Status civitas</label>
                                <select id="user_type" name="user_type"
                                    class="form-select @error('user_type') is-invalid @enderror" required>
                                    <option value="">Pilih status</option>
                                    <option value="student" @selected(old('user_type') === 'student')>Mahasiswa</option>
                                    <option value="lecturer" @selected(old('user_type') === 'lecturer')>Dosen</option>
                                    <option value="staff" @selected(old('user_type') === 'staff')>Staf</option>
                                </select>
                                @error('user_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="faculty">Fakultas</label>
                                <input id="faculty" name="faculty" type="text"
                                    value="{{ old('faculty') }}" autocomplete="organization"
                                    class="form-control @error('faculty') is-invalid @enderror"
                                    required>
                                @error('faculty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="study_program">Program studi</label>
                                <input id="study_program" name="study_program" type="text"
                                    value="{{ old('study_program') }}" autocomplete="organization-title"
                                    class="form-control @error('study_program') is-invalid @enderror">
                                <div class="form-text">Wajib diisi mahasiswa.</div>
                                @error('study_program')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input id="password" name="password" type="password"
                                    autocomplete="new-password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="password_confirmation">Konfirmasi password</label>
                                <input id="password_confirmation" name="password_confirmation" type="password"
                                    autocomplete="new-password" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Buat akun</button>
                        </form>

                        <p class="auth-switch">
                            Sudah punya akun?
                            <a href="{{ route('login') }}">Masuk</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
