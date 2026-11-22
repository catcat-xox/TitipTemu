@extends('layouts.admin-auth')

@section('title', 'Masuk Admin — TitipTemu')
@section('description', 'Halaman masuk khusus Admin TitipTemu.')

@section('content')
    <div class="admin-auth-card auth-card">
        <div class="text-center mb-4">
            <h1 class="auth-title">Masuk ke panel Admin</h1>
            <p class="mb-0">Gunakan akun Admin yang terpisah dari akun pengguna biasa.</p>
        </div>

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="email">Email Admin</label>
                <input id="email" name="email" type="email"
                    value="{{ old('email') }}" autocomplete="email"
                    class="form-control @error('email') is-invalid @enderror"
                    required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input id="password" name="password" type="password"
                    autocomplete="current-password"
                    class="form-control @error('password') is-invalid @enderror"
                    required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-4">
                <input id="remember" name="remember" value="1" type="checkbox"
                    class="form-check-input" @checked(old('remember'))>
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Masuk sebagai Admin</button>
        </form>
    </div>
@endsection
