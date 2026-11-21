@extends('layouts.app')

@section('title', 'Dashboard User — TitipTemu')

@section('content')
    <section class="section">
        <div class="container">
            <span class="eyebrow">Area User</span>
            <h1 class="mt-3">Dashboard User</h1>
            @if (! auth()->user()->hasVerifiedEmail())
                <div class="alert alert-warning mt-4" role="alert">
                    Verifikasi email sebelum membuat laporan atau mengirim konfirmasi pengembalian.
                    <a href="{{ route('verification.notice') }}">Buka halaman verifikasi.</a>
                </div>
            @else
                <p>Anda masuk sebagai User. Fitur laporan akan tersedia pada tahap berikutnya.</p>
            @endif
        </div>
    </section>
@endsection
