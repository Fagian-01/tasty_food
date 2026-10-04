@extends('layouts.app')

@section('title', 'Tentang Kami - Kairo Ramen')

@section('content')

{{-- HERO --}}
<section class="page-hero">
    <div>
        <h1>TENTANG KAMI</h1>
        <p>Cerita di balik semangkuk Kairo Ramen</p>
    </div>
</section>

{{-- INTRO --}}
<section class="section-pad bg-soft">
    <div class="split">
        <div>
            <h2>KAIRO RAMEN</h2>
            <p class="lead">
                Japanese comfort food for every moment.
                Hangat, sederhana, dan selalu bikin kembali lagi.
            </p>
            <p>
                Kairo Ramen berawal dari kedai kecil yang hanya menjual
                satu menu: shoyu ramen. Kaldunya kami rebus perlahan
                hingga 12 jam, mienya dibuat segar setiap hari, dan
                toppingnya dipilih satu per satu. Kini menu kami bertambah
                &mdash; gyoza, karaage, donburi, hingga sushi &mdash; tapi
                prinsipnya tetap sama: semangkuk kehangatan yang jujur.
            </p>
        </div>
        <div class="duo-img">
            <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=700&q=80" alt="Ramen Kairo Ramen">
            <img src="https://images.unsplash.com/photo-1552611052-33e04de081de?auto=format&fit=crop&w=700&q=80" alt="Chef Kairo Ramen sedang memasak">
        </div>
    </div>
</section>

{{-- VISI --}}
<section class="section-pad">
    <div class="split">
        <div class="duo-img">
            <img src="https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=700&q=80" alt="Miso ramen">
            <img src="https://images.unsplash.com/photo-1553621042-f6e147245754?auto=format&fit=crop&w=700&q=80" alt="Sushi Kairo Ramen">
        </div>
        <div>
            <h2>VISI</h2>
            <p>
                Menjadi kedai Japanese comfort food favorit yang membuat
                masakan Jepang terasa dekat, hangat, dan terjangkau untuk
                semua orang &mdash; satu mangkuk dalam satu waktu.
            </p>
        </div>
    </div>
</section>

{{-- MISI --}}
<section class="section-pad" style="padding-top:0;">
    <div class="split">
        <div>
            <h2>MISI</h2>
            <p>
                Menyajikan ramen dan hidangan Jepang dengan kaldu autentik
                dan bahan segar setiap hari, menjaga konsistensi rasa dan
                kebersihan dapur, serta melayani setiap tamu seperti teman
                yang pulang ke rumah.
            </p>
        </div>
        <div class="single-img">
            <img src="https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=900&q=80" alt="Gyoza segar Kairo Ramen">
        </div>
    </div>
</section>

@endsection
