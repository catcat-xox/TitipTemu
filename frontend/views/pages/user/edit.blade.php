@extends('layouts.public')

@section('title', 'Ubah Profil — TitipTemu')

@section('content')
    <section class="page-hero">
        <div class="container">
            <h1>Ubah profil</h1>
            <p class="page-intro">Perbarui data diri Anda. Perubahan foto profil tetap disimpan privat.</p>
        </div>
    </section>

    <section class="section pt-0">
        <div class="container">
            <div class="surface-card p-3 p-md-4">
                @include('partials.profile-form', [
                    'user' => auth()->user(),
                    'typeLabels' => $typeLabels,
                ])
            </div>
        </div>
    </section>
@endsection
