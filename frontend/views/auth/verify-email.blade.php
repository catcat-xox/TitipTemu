@extends('layouts.app')

@section('title', 'Verifikasi Email — TitipTemu')

@section('content')
    <section class="auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-md-8 col-lg-6">
                    <div class="auth-card text-center">
                        <span class="eyebrow">Verifikasi akun</span>
                        <h1 class="auth-title mt-3">Periksa email Anda</h1>
                        <p>
                            Kami mengirim tautan verifikasi ke alamat email Anda.
                            Verifikasi email sebelum membuat laporan atau mengirim konfirmasi pengembalian.
                        </p>

                        @if (session('status'))
                            <div class="alert alert-success text-start" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Kirim ulang tautan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
