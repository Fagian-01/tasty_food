<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Konten demo KAIRO RAMEN (idempoten, aman dijalankan ulang).
 *
 * - 6 berita + 12 galeri dengan gambar makanan Jepang terverifikasi.
 * - Gambar diunduh otomatis dari Unsplash ke storage/app/public
 *   (news/xxx.jpg, gallery/xxx.jpg) agar kompatibel dengan
 *   asset('storage/...') yang dipakai blade + CRUD admin.
 * - Kunci unik: news by slug, gallery by title.
 * - Tidak menghapus data existing; record yang sudah ada hanya
 *   diperbarui kontennya (created_at tidak diubah), record admin
 *   dengan slug/title lain tidak disentuh.
 *
 * Jalankan: php artisan db:seed --class=KairoDemoContentSeeder
 */
class KairoDemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $newsCount = 0;
        foreach ($this->newsItems() as $item) {
            $path = $this->fetchImage($item['image_url'], 'news/'.$item['slug'].'.jpg');
            if (! $path) {
                $this->command?->warn("Lewati berita [{$item['slug']}]: gambar gagal diunduh dan belum ada file lokal.");
                continue;
            }

            $existing = News::where('slug', $item['slug'])->first();
            if ($existing) {
                $existing->update([
                    'title' => $item['title'],
                    'image' => $path,
                    'content' => $item['content'],
                ]);
            } else {
                News::create([
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'image' => $path,
                    'content' => $item['content'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['created_at'],
                ]);
                $newsCount++;
            }
        }

        $galleryCount = 0;
        foreach ($this->galleryItems() as $item) {
            $path = $this->fetchImage($item['image_url'], 'gallery/'.$item['file'].'.jpg');
            if (! $path) {
                $this->command?->warn("Lewati galeri [{$item['title']}]: gambar gagal diunduh dan belum ada file lokal.");
                continue;
            }

            $existing = Gallery::where('title', $item['title'])->first();
            if ($existing) {
                $existing->update([
                    'image' => $path,
                    'description' => $item['description'],
                ]);
            } else {
                Gallery::create([
                    'title' => $item['title'],
                    'image' => $path,
                    'description' => $item['description'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['created_at'],
                ]);
                $galleryCount++;
            }
        }

        $this->command?->info("KairoDemoContentSeeder selesai: {$newsCount} berita baru, {$galleryCount} galeri baru (total: ".News::count().' berita, '.Gallery::count().' galeri).');
    }

    /**
     * Unduh gambar ke disk public. Lewati jika file sudah ada
     * (hemat waktu + idempoten). Return path relatif atau null.
     */
    protected function fetchImage(string $url, string $path): ?string
    {
        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            return $path;
        }

        try {
            $response = Http::timeout(60)->get($url);
            if ($response->successful() && $response->body() !== '') {
                $disk->put($path, $response->body());

                return $path;
            }
        } catch (\Throwable $e) {
            $this->command?->warn('HTTP client gagal, coba unduhan langsung: '.$e->getMessage());
        }

        // Fallback tanpa dependensi tambahan.
        try {
            $context = stream_context_create(['http' => ['timeout' => 60]]);
            $body = @file_get_contents($url, false, $context);
            if ($body !== false && $body !== '') {
                $disk->put($path, $body);

                return $path;
            }
        } catch (\Throwable $e) {
            $this->command?->warn('Unduhan langsung gagal: '.$e->getMessage());
        }

        return $disk->exists($path) ? $path : null;
    }

    /** @return array<int, array{title:string,slug:string,image_url:string,content:string,created_at:\DateTime}> */
    protected function newsItems(): array
    {
        $now = now();

        return [
            [
                'title' => 'Grand Opening KAIRO RAMEN di Bandung',
                'slug' => 'grand-opening-kairo-ramen',
                'image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=80',
                'content' => "Kabar gembira untuk pecinta Japanese comfort food di Bandung! KAIRO RAMEN resmi membuka pintunya dan siap menyajikan semangkuk kehangatan untuk setiap momenmu. Kedai kami hadir dengan suasana yang nyaman, pelayanan yang ramah, dan tentu saja semangkuk ramen autentik yang dimasak dengan penuh ketelitian.\n\nDi hari pembukaan, pengunjung bisa mencicipi menu andalan kami: Shoyu Ramen dengan kaldu gurih yang direbus perlahan, Karaage Donburi yang renyah, hingga Salmon Sushi Set yang segar. Semua bahan dipilih setiap pagi dan dimasak tepat sebelum disajikan, jadi kesegarannya terjamin.\n\nSelama periode grand opening, nikmati promo spesial untuk setiap pembelian paket ramen dan gyoza. Ajak keluarga dan teman-temanmu, rasakan sendiri hangatnya kaldu 12 jam kami. Kami buka setiap hari pukul 10.00 sampai 22.00. Sampai jumpa di KAIRO RAMEN!",
                'created_at' => (clone $now)->subDays(2),
            ],
            [
                'title' => 'Rahasia Kuah Ramen yang Dimasak 12 Jam',
                'slug' => 'rahasia-kuah-ramen-12-jam',
                'image_url' => 'https://images.unsplash.com/photo-1557872943-16a5ac26437e?auto=format&fit=crop&w=1200&q=80',
                'content' => "Pernah bertanya-tanya kenapa kuah ramen di KAIRO terasa begitu gurih dan hangat sampai suapan terakhir? Jawabannya ada pada kesabaran. Setiap panci kaldu kami direbus perlahan selama 12 jam, mengekstrak seluruh rasa dari tulang ayam pilihan, bawang, jahe, dan rempah rahasia dapur kami.\n\nProses panjang ini tidak bisa dipersingkat. Api kecil yang stabil membuat kolagen larut sempurna, menghasilkan kuah yang kaya rasa namun tetap ringan di lidah. Setiap pagi, tim dapur kami mencicipi kaldu sebelum kedai dibuka untuk memastikan kualitasnya konsisten dari hari ke hari.\n\nDipadukan dengan mi segar yang dibuat harian, chashu yang lembut, telur ajitama dengan kuning telur yang lumer, serta nori berkualitas, semangkuk ramen kami adalah hasil dari ketekunan. Satu suapan, dan kamu akan paham kenapa 12 jam penantian itu sepadan.",
                'created_at' => (clone $now)->subDays(6),
            ],
            [
                'title' => 'Menu Baru: Spicy Miso Ramen',
                'slug' => 'menu-baru-spicy-miso-ramen',
                'image_url' => 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=1200&q=80',
                'content' => "Buat kamu yang suka tantangan rasa, kami punya kabar pedas! KAIRO RAMEN meluncurkan menu terbaru: Spicy Miso Ramen. Perpaduan pasta miso yang gurih dengan racikan cabai spesial menghasilkan kuah yang creamy, pedasnya nendang, tapi tetap seimbang dan bikin nagih.\n\nToppingnya pun tidak main-main: daging cincang berbumbu, jagung manis, pakcoy segar, daun bawang, dan telur ajitama belah dua yang selalu jadi favorit. Setiap elemen dirancang untuk melengkapi kepedasan kuahnya, jadi suapan demi suapan terasa seru.\n\nTersedia dalam tiga level kepedasan, dari level 1 yang ramah untuk pemula sampai level 3 untuk para pemberani. Berani coba? Datang langsung ke kedai dan buktikan sendiri. Jangan lupa siapkan minuman dingin pendamping!",
                'created_at' => (clone $now)->subDays(10),
            ],
            [
                'title' => 'Promo Paket Ramen dan Gyoza, Hemat Sampai Akhir Bulan',
                'slug' => 'promo-paket-ramen-gyoza',
                'image_url' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=1200&q=80',
                'content' => "Makan enak tidak harus mahal. Bulan ini KAIRO RAMEN menghadirkan promo paket bundling: setiap pembelian satu mangkuk ramen favoritmu, kamu bisa menambahkan satu porsi gyoza dengan harga spesial. Gyoza kami dilipat satu per satu setiap pagi, renyah di luar dan juicy di dalam.\n\nPaket ini cocok untuk makan siang bareng teman kantor maupun makan malam santai bersama keluarga. Kamu bisa memilih ramen apa pun, dari Shoyu Ramen yang klasik sampai Spicy Miso Ramen yang menantang, lalu padukan dengan gyoza kukus atau gyoza goreng sesuai selera.\n\nPromo berlaku setiap hari selama persediaan masih ada dan tidak dapat digabungkan dengan promo lain. Tunjukkan artikel ini ke kasir atau sebutkan kode KAIROHEMAT saat memesan. Yuk, manfaatkan promonya sebelum berakhir!",
                'created_at' => (clone $now)->subDays(14),
            ],
            [
                'title' => 'Bahan Segar untuk Setiap Hidangan',
                'slug' => 'bahan-segar-setiap-hidangan',
                'image_url' => 'https://images.unsplash.com/photo-1466637574441-749b8f19452f?auto=format&fit=crop&w=1200&q=80',
                'content' => "Rahasia hidangan enak bukan cuma resep, tapi juga bahan. Di KAIRO RAMEN, kami berkomitmen memakai bahan segar setiap hari. Sayuran datang setiap pagi dari pemasok lokal terpercaya, mi dibuat fresh di dapur kami, dan ikan untuk sushi dipilih dengan standar kesegaran yang ketat.\n\nKami percaya makanan yang baik dimulai jauh sebelum kompor dinyalakan. Telur untuk ajitama dipilih yang berkualitas, daging untuk chashu dan karaage dimarinasi dengan takaran pas, dan setiap bumbu diracik sendiri tanpa pengawet. Tidak ada jalan pintas di dapur kami.\n\nKomitmen ini mungkin membuat kerja kami lebih berat, tapi senyummu saat suapan pertama adalah bayarannya. Karena buat kami, menyajikan makanan segar adalah bentuk penghormatan kepada setiap pelanggan yang datang.",
                'created_at' => (clone $now)->subDays(21),
            ],
            [
                'title' => 'Cerita di Balik Berdirinya KAIRO RAMEN',
                'slug' => 'cerita-di-balik-kairo-ramen',
                'image_url' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=1200&q=80',
                'content' => "KAIRO RAMEN berawal dari kedai yang sangat kecil dengan hanya satu menu: shoyu ramen. Pendirinya percaya satu hal sederhana, kalau satu mangkuk saja tidak bisa dibuat dengan benar, buat apa menambah menu lain? Dari keyakinan itu, kaldu 12 jam kami lahir dan tidak pernah berubah sampai hari ini.\n\nPelanggan pertama kami adalah tetangga sekitar dan pekerja yang lewat sepulang kerja. Dari mulut ke mulut, antrean makin panjang. Barulah kami berani menambah menu: gyoza buatan tangan, karaage yang renyah, donburi yang mengenyangkan, hingga sushi segar setiap pagi.\n\nKini kedai kami lebih besar, tapi prinsipnya tetap sama seperti hari pertama: semangkuk kehangatan yang jujur. Terima kasih sudah menjadi bagian dari cerita kami. Semangkuk berikutnya, kami tunggu kehadiranmu.",
                'created_at' => (clone $now)->subDays(30),
            ],
        ];
    }

    /** @return array<int, array{title:string,file:string,image_url:string,description:string,created_at:\DateTime}> */
    protected function galleryItems(): array
    {
        $now = now();
        $day = fn (int $d) => (clone $now)->subDays($d);

        return [
            [
                'title' => 'Shoyu Ramen',
                'file' => 'shoyu-ramen',
                'image_url' => 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Menu andalan kami: mi kenyal dengan kuah shoyu gurih, chashu lembut, dan telur ajitama.',
                'created_at' => $day(1),
            ],
            [
                'title' => 'Ramen Chashu Spesial',
                'file' => 'ramen-chashu-spesial',
                'image_url' => 'https://images.unsplash.com/photo-1557872943-16a5ac26437e?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Irisan chashu tebal dengan kuah kaldu 12 jam, disajikan panas mengepul.',
                'created_at' => $day(2),
            ],
            [
                'title' => 'Spicy Miso Ramen',
                'file' => 'spicy-miso-ramen',
                'image_url' => 'https://images.unsplash.com/photo-1591814468924-caf88d1232e1?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Kuah miso pedas yang creamy dengan daging cincang berbumbu, jagung, dan pakcoy segar.',
                'created_at' => $day(3),
            ],
            [
                'title' => 'Gyoza Kukus',
                'file' => 'gyoza-kukus',
                'image_url' => 'https://images.unsplash.com/photo-1496116218417-1a781b1c416c?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Gyoza kukus yang lembut dengan isian ayam dan sayur, dilipat satu per satu setiap pagi.',
                'created_at' => $day(4),
            ],
            [
                'title' => 'Aneka Gyoza',
                'file' => 'aneka-gyoza',
                'image_url' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Set gyoza lengkap: kukus, goreng, dan sayur, disajikan dengan kuah kaldu hangat.',
                'created_at' => $day(5),
            ],
            [
                'title' => 'Sushi Boat KAIRO',
                'file' => 'sushi-boat-kairo',
                'image_url' => 'https://images.unsplash.com/photo-1553621042-f6e147245754?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Platter sushi premium di atas perahu kayu, cocok untuk dinikmati beramai-ramai.',
                'created_at' => $day(6),
            ],
            [
                'title' => 'Salmon Sushi Roll',
                'file' => 'salmon-sushi-roll',
                'image_url' => 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Roll salmon segar dengan nasi pulen dan taburan tobiko yang gurih.',
                'created_at' => $day(7),
            ],
            [
                'title' => 'Nigiri Sushi',
                'file' => 'nigiri-sushi',
                'image_url' => 'https://images.unsplash.com/photo-1611143669185-af224c5e3252?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Rangkaian nigiri salmon, tuna, dan udang yang disusun di atas piring saji hitam.',
                'created_at' => $day(8),
            ],
            [
                'title' => 'Sashimi dan Sushi Platter',
                'file' => 'sashimi-sushi-platter',
                'image_url' => 'https://images.unsplash.com/photo-1562802378-063ec186a863?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Irisan sashimi salmon segar berpadu nigiri dan maki dalam satu kotak kayu.',
                'created_at' => $day(9),
            ],
            [
                'title' => 'Interior KAIRO Ramen',
                'file' => 'interior-kairo-ramen',
                'image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Ruang makan yang hangat dan modern, nyaman untuk makan sendiri maupun bersama.',
                'created_at' => $day(10),
            ],
            [
                'title' => 'Suasana Ruang Makan',
                'file' => 'suasana-ruang-makan',
                'image_url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Meja-meja tertata rapi menanti pengunjung, dengan pencahayaan hangat khas KAIRO.',
                'created_at' => $day(11),
            ],
            [
                'title' => 'Dapur KAIRO',
                'file' => 'dapur-kairo',
                'image_url' => 'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=1200&q=80',
                'description' => 'Tim dapur kami menyiapkan setiap hidangan dengan bahan segar dan ketelitian.',
                'created_at' => $day(12),
            ],
        ];
    }
}
