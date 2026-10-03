@extends('layouts.app')

@section('title', 'Tentang Kami - Tasty Food')

@section('content')

{{-- HERO --}}
<section class="page-hero">
    <div>
        <h1>TENTANG KAMI</h1>
        <p>Mengenal lebih dekat Tasty Food</p>
    </div>
</section>

{{-- INTRO --}}
<section class="section-pad bg-soft">
    <div class="split">
        <div>
            <h2>TASTY FOOD</h2>
            <p class="lead">
                Kami percaya makanan yang baik berasal dari bahan yang baik
                dan diolah dengan penuh ketelitian.
            </p>
            <p>
                Tasty Food hadir untuk memberikan pengalaman menikmati makanan
                yang lezat sekaligus berkualitas. Kami menggunakan bahan-bahan
                pilihan dan menjaga setiap proses pengolahan agar menghasilkan
                hidangan yang sehat, bergizi, dan nikmat untuk semua kalangan.
            </p>
        </div>
        <div class="duo-img">
            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=700&q=80" alt="Salad segar Tasty Food">
            <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?auto=format&fit=crop&w=700&q=80" alt="Chef Tasty Food sedang memasak">
        </div>
    </div>
</section>

{{-- VISI --}}
<section class="section-pad">
    <div class="split">
        <div class="duo-img">
            <img src="https://images.unsplash.com/photo-1567337710282-00832b415979?auto=format&fit=crop&w=700&q=80" alt="Hidangan nusantara">
            <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=700&q=80" alt="Ramen Tasty Food">
        </div>
        <div>
            <h2>VISI</h2>
            <p>
                Menjadi pilihan utama masyarakat dalam menikmati makanan sehat
                dan lezat, serta turut melestarikan kekayaan kuliner nusantara
                dengan sentuhan modern yang dapat dinikmati semua generasi.
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
                Menyajikan hidangan berkualitas dari bahan-bahan segar pilihan,
                menjaga konsistensi rasa dan kebersihan dalam setiap proses,
                serta memberikan pelayanan terbaik agar setiap pelanggan
                mendapatkan pengalaman makan yang berkesan.
            </p>
        </div>
        <div class="single-img">
            <img src="https://images.unsplash.com/photo-1466637574441-749b8f19452f?auto=format&fit=crop&w=900&q=80" alt="Bahan-bahan segar">
        </div>
    </div>
</section>

@endsection
