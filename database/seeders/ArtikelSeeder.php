<?php

namespace Database\Seeders;

use App\Models\Artikel;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArtikelSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user admin/owner pertama sebagai penulis
        $user = User::whereIn('role', ['owner', 'admin'])->first();

        if (!$user) {
            $this->command->warn('Tidak ada user owner/admin. Skip seeder artikel.');
            return;
        }

        $artikels = [
            [
                'title'     => 'Perjalanan Biji Kopi dari Tanah Tinggi Sumatera Utara',
                'slug'      => 'perjalanan-biji-kopi-sumatera-utara',
                'category'  => 'Cerita',
                'read_time' => 5,
                'excerpt'   => 'Setiap cangkir Tether Brew dimulai dari perkebunan kopi di dataran tinggi Sumatera Utara. Kenali proses panjang dari petik hingga seduh yang menghasilkan rasa khas kopi Sumatera.',
                'content'   => '<p>Sumatera Utara dikenal sebagai salah satu penghasil kopi terbaik di dunia. Dari ketinggian lebih dari 1.200 meter di atas permukaan laut, biji kopi Arabika tumbuh subur dengan karakter rasa yang unik — earthy, full-bodied, dan dengan sedikit sentuhan herbal.</p><h2>Dari Petani ke Cangkir</h2><p>Proses dimulai dari pemilihan cherry kopi yang matang sempurna. Para petani di sekitar Lintong dan Sidikalang memetik secara selektif, memastikan hanya buah merah yang dipanen. Setelah dipetik, biji kopi melewati proses pencucian (wet process) yang menghasilkan rasa lebih bersih dan cerah.</p><h2>Proses Pengeringan &amp; Roasting</h2><p>Biji kopi kemudian dijemur di bawah sinar matahari selama 2-3 minggu hingga kadar air mencapai 12%. Tahap roasting menjadi kunci — kami menggunakan medium-dark roast untuk menghasilkan rasa yang balanced antara fruity acidity dan chocolatey sweetness.</p><h2>Komitmen Tether Brew</h2><p>Di Tether Brew, kami berkomitmen menggunakan 100% biji kopi lokal Sumatera Utara. Bukan hanya soal rasa, tapi juga soal mendukung perekonomian petani kopi lokal. Setiap cangkir yang kamu nikmati adalah bentuk dukungan nyata untuk mereka.</p><blockquote>"Kopi terbaik bukan yang termahal, tapi yang diolah dengan penuh cinta dari tanah kelahirannya."</blockquote><p>Jadi, saat kamu menyeruput Cold Brew atau Americano dari gerobak Tether Brew, ingatlah — ada perjalanan panjang dan penuh dedikasi di balik setiap tegukannya.</p>',
            ],
            [
                'title'     => '5 Rahasia Membuat Cold Brew Sempurna ala Barista',
                'slug'      => '5-rahasia-cold-brew-sempurna',
                'category'  => 'Tips',
                'read_time' => 4,
                'excerpt'   => 'Cold brew bukan sekadar kopi dingin biasa. Pelajari teknik rasio, waktu rendam, dan grind size yang tepat untuk mendapatkan rasa maksimal yang smooth dan refreshing.',
                'content'   => '<p>Cold brew sudah jadi minuman favorit banyak pecinta kopi, terutama di kota tropis seperti Medan. Tapi tahukah kamu, membuat cold brew yang sempurna butuh teknik khusus? Berikut 5 rahasianya!</p><h2>1. Gunakan Grind Size yang Tepat</h2><p>Kunci utama cold brew ada pada ukuran gilingan kopi. Gunakan coarse grind (gilingan kasar), mirip dengan gula pasir kasar. Gilingan yang terlalu halus akan menghasilkan rasa pahit dan over-extracted.</p><h2>2. Rasio Air dan Kopi</h2><p>Rasio ideal untuk cold brew adalah 1:7 (1 bagian kopi : 7 bagian air). Untuk konsentrat yang lebih kuat, gunakan rasio 1:5. Konsentrat ini bisa dicampur dengan air atau susu saat disajikan.</p><h2>3. Waktu Rendam 12-18 Jam</h2><p>Rendam kopi dalam air dingin selama minimal 12 jam, idealnya 16-18 jam. Kurang dari itu, rasa belum maksimal. Lebih dari 24 jam, kopi bisa jadi terlalu pahit.</p><h2>4. Gunakan Air Berkualitas</h2><p>Air adalah 98% dari cold brew-mu. Gunakan air yang sudah disaring atau air mineral. Hindari air keran yang mengandung klorin karena bisa mempengaruhi rasa.</p><h2>5. Simpan dengan Benar</h2><p>Setelah disaring, simpan cold brew di kulkas dalam wadah tertutup rapat. Cold brew bisa bertahan hingga 2 minggu, tapi rasa terbaiknya ada di 3-5 hari pertama.</p><blockquote>"Kesabaran adalah bumbu rahasia cold brew — biarkan waktu bekerja untuk mengekstrak rasa terbaik."</blockquote>',
            ],
            [
                'title'     => 'Kenapa Kopi Keliling Jadi Tren Baru di Kota Medan?',
                'slug'      => 'kopi-keliling-tren-baru-medan',
                'category'  => 'Insight',
                'read_time' => 4,
                'excerpt'   => 'Konsep kopi keliling semakin diminati masyarakat urban Medan. Simak alasan di balik tren ini dan bagaimana Tether Brew menjadi pelopor di kancah kopi keliling kota.',
                'content'   => '<p>Beberapa tahun terakhir, gerobak kopi keliling mulai menjamur di jalanan kota Medan. Dari kawasan kampus hingga area perkantoran, konsep ini mendapat sambutan hangat dari berbagai kalangan. Apa yang membuat tren ini begitu menarik?</p><h2>Aksesibilitas Tinggi</h2><p>Tidak perlu lagi mencari coffee shop atau mengantre panjang. Kopi keliling membawa kopi langsung ke lokasi pelanggan. Cukup cek peta, temukan gerobak terdekat, dan kopi premium sudah ada di tanganmu.</p><h2>Harga yang Ramah di Kantong</h2><p>Tanpa biaya sewa tempat yang mahal, kopi keliling bisa menawarkan harga yang jauh lebih terjangkau. Di Tether Brew, kamu bisa menikmati kopi premium mulai dari Rp 8.000 saja!</p><h2>Pengalaman yang Unik</h2><p>Ada sensasi tersendiri membeli kopi dari gerobak di jalanan. Interaksi langsung dengan barista, melihat proses penyeduhan, dan menikmati kopi di udara terbuka — semua itu menciptakan pengalaman yang tak bisa ditiru oleh coffee shop konvensional.</p><h2>Tether Brew: Pelopor Kopi Keliling Modern</h2><p>Tether Brew membawa konsep kopi keliling ke level berikutnya dengan teknologi pelacakan real-time. Pelanggan bisa melihat posisi gerobak di peta, memesan via WhatsApp, dan bahkan mengirim lokasi mereka ke rider. Ini bukan sekadar gerobak kopi — ini adalah revolusi cara menikmati kopi.</p>',
            ],
            [
                'title'     => 'Kafein dalam Kopi vs Teh: Mana yang Lebih Baik untuk Produktivitasmu?',
                'slug'      => 'kafein-kopi-vs-teh',
                'category'  => 'Edukasi',
                'read_time' => 6,
                'excerpt'   => 'Banyak yang bertanya, lebih baik minum kopi atau teh? Kami bedah kandungan kafein, efek pada tubuh, dan kapan waktu terbaik mengonsumsi keduanya untuk performa optimal.',
                'content'   => '<p>Perdebatan antara pecinta kopi dan teh sudah berlangsung lama. Keduanya memiliki kelebihan masing-masing, terutama dalam hal kandungan kafein dan efeknya terhadap produktivitas. Mari kita bedah secara objektif.</p><h2>Kandungan Kafein</h2><p>Secangkir kopi (240ml) mengandung rata-rata 95-200mg kafein, sementara teh hitam mengandung 40-70mg, dan teh hijau sekitar 25-45mg. Jadi, kopi memang juara dalam hal kadar kafein.</p><h2>Cara Kerja di Tubuh</h2><p>Kafein dari kopi diserap cepat dan memberikan efek "kick" dalam 15-30 menit. Sementara teh mengandung L-theanine yang memperlambat penyerapan kafein, memberikan efek fokus yang lebih gradual dan tahan lama tanpa jitters.</p><h2>Kapan Harus Minum Kopi?</h2><p>Kopi paling efektif dikonsumsi antara pukul 9:30-11:30 pagi dan 1:30-5:00 sore, saat level kortisol tubuh menurun. Hindari minum kopi saat baru bangun tidur — tubuh sudah memproduksi kortisol secara alami saat itu.</p><h2>Kapan Harus Minum Teh?</h2><p>Teh cocok diminum di sore hingga malam hari ketika kamu butuh fokus ringan tanpa mengganggu tidur. Teh hijau juga kaya antioksidan yang baik untuk kesehatan jangka panjang.</p><h2>Kesimpulan</h2><p>Tidak ada jawaban absolut mana yang lebih baik. Kopi untuk boost energi cepat, teh untuk fokus yang sustained. Yang terpenting, nikmati keduanya dengan bijak dan dalam takaran yang tepat.</p>',
            ],
            [
                'title'     => 'Di Balik Layar: Sehari Bersama Rider Tether Brew di Jalanan Medan',
                'slug'      => 'di-balik-layar-rider-tether-brew',
                'category'  => 'Behind The Scenes',
                'read_time' => 5,
                'excerpt'   => 'Pernah penasaran bagaimana keseharian rider kami? Ikuti perjalanan satu hari penuh bersama rider Tether Brew, dari persiapan pagi hingga cup terakhir di sore hari.',
                'content'   => '<p>Pukul 6 pagi, saat sebagian besar orang masih terlelap, rider kami sudah bersiap. Mari kita ikuti perjalanan seorang rider Tether Brew selama satu hari penuh.</p><h2>06:00 - Persiapan Pagi</h2><p>Hari dimulai dengan mengecek peralatan: mesin espresso portable, grinder, stok biji kopi, es batu, cup, dan perlengkapan lainnya. Semua harus dalam kondisi bersih dan siap pakai. Biji kopi di-grind fresh setiap pagi untuk menjaga kesegaran.</p><h2>07:30 - Berangkat ke Lokasi</h2><p>Setelah semua siap, gerobak mulai bergerak menuju titik strategis pertama — biasanya area kampus atau perkantoran. Rider mengaktifkan GPS tracker agar pelanggan bisa menemukan lokasi mereka di peta Tether Brew.</p><h2>08:00 - 12:00 - Rush Hour Pagi</h2><p>Ini adalah jam-jam tersibuk. Pesanan datang bertubi-tubi, terutama Americano dan Cold Brew. Rider harus tetap fokus menjaga kualitas setiap cup sambil melayani pelanggan dengan ramah. Rata-rata, satu rider bisa melayani 40-60 cup di jam sibuk.</p><h2>12:00 - 14:00 - Rehat &amp; Berpindah</h2><p>Setelah rush hour pagi, rider menggunakan waktu ini untuk rehat sejenak, restocking es batu, dan berpindah ke lokasi baru jika diperlukan. Update stok di aplikasi juga dilakukan di waktu ini.</p><h2>14:00 - 17:00 - Sore yang Produktif</h2><p>Sore hari membawa gelombang pesanan kedua. Kali ini, minuman non-kopi seperti Matcha Brew dan Cokelat Brew jadi favorit. Rider terus melayani hingga stok habis atau jam operasional selesai.</p><h2>17:30 - Wrap Up</h2><p>Hari berakhir dengan membersihkan semua peralatan, mencatat penjualan, dan mengembalikan gerobak ke base. Satu hari yang melelahkan, tapi juga memuaskan — karena setiap senyum pelanggan adalah reward terbaik.</p><blockquote>"Kami bukan sekadar menjual kopi. Kami membawa kebahagiaan dalam setiap cangkir, di setiap sudut Medan."</blockquote>',
            ],
        ];

        foreach ($artikels as $i => $data) {
            Artikel::create(array_merge($data, [
                'user_id'      => $user->id,
                'is_published' => true,
                'published_at' => now()->subDays(($i * 5)),
            ]));
        }

        $this->command->info('5 artikel dummy berhasil ditambahkan.');
    }
}
