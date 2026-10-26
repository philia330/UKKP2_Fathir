{{--
    MODAL DETAIL PENGADUAN (partial)
    Fungsi: menampilkan detail lengkap satu laporan dalam jendela pop-up dan memperbarui statusnya.
    Dipakai bersama oleh admin dan petugas (JavaScript di bawah membedakan lewat alamat halaman).
    Cara kerja singkat:
      1. Petugas/admin klik tombol detail di tabel, yang memanggil fungsi showDetail(id) (ada di bagian script bawah).
      2. JavaScript mengambil data pengaduan dari server, mengisi ke elemen yang punya id "modal-..." lalu membuka modal.
      3. Form "Ubah Status" mengirim status baru ke server.
--}}

{{-- ===== KERANGKA MODAL =====
     - modal          : komponen pop-up Bootstrap (tersembunyi sampai dibuka)
     - fade           : efek muncul perlahan. Contoh: hapus "fade" agar muncul langsung tanpa animasi
     - id="detailModal" : nama unik modal, dipakai JavaScript untuk membukanya
     - tabindex="-1"  : supaya modal bisa ditutup dengan tombol Esc
     - aria-labelledby / aria-hidden : atribut aksesibilitas untuk pembaca layar, jangan dihapus --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">

    {{-- modal-dialog : posisi & lebar kotak modal
         - modal-lg   : ukuran besar (sekitar 800px).
                        Contoh: hapus = ukuran normal (500px), modal-sm = kecil, modal-xl = sangat besar
         Contoh tambahan: tambah "modal-dialog-centered" agar modal muncul di TENGAH layar secara vertikal,
                          "modal-dialog-scrollable" agar hanya isi modal yang bisa di-scroll --}}
    <div class="modal-dialog modal-lg">
        {{-- modal-content : kotak putih berisi header, body, footer --}}
        <div class="modal-content">

            {{-- ===== HEADER MODAL: judul + tombol tutup ===== --}}
            <div class="modal-header">
                {{-- modal-title : gaya judul modal
                     me-2 : jarak KANAN ikon ke teks. Contoh: me-3 = lebih jauh
                     bi-file-earmark-text : ikon dokumen --}}
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Detail Pengaduan
                </h5>
                {{-- btn-close : tombol silang (X) di pojok KANAN atas
                     data-bs-dismiss="modal" : klik tombol ini menutup modal
                     Contoh: tambah class "btn-close-white" kalau header diberi latar gelap --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- ===== BODY MODAL: isi utama ===== --}}
            <div class="modal-body">

                {{-- BAGIAN A: Data customer (KIRI) dan foto bukti (KANAN) dalam satu baris.
                     - row : baris grid, isinya kolom col-*
                     - col-md-6 : lebar 6 dari 12 kolom = setengah layar, jadi 2 kolom berdampingan.
                                  Contoh: col-md-8 + col-md-4 = kolom KIRI lebih lebar, kolom KANAN lebih sempit
                                  (di HP otomatis turun jadi atas-bawah) --}}
                <div class="row">

                    {{-- KOLOM KIRI: tabel data customer --}}
                    <div class="col-md-6">
                        {{-- table-borderless : tabel tanpa garis tepi.
                             Contoh: ganti jadi "table table-bordered" agar ada garis di semua sisi,
                             tambah "table-sm" agar lebih rapat, "table-striped" agar baris selang-seling warna --}}
                        <table class="table table-borderless">
                            <tr>
                                {{-- fw-bold : huruf tebal. Contoh: fw-normal = biasa, fw-light = tipis
                                     text-muted : warna abu-abu untuk label. Contoh: text-dark = hitam, text-primary = biru
                                     style="width: 130px;" : lebar kolom label dikunci 130px supaya nilai di sebelahnya rata.
                                     Contoh: 160px = label lebih lebar (berguna kalau labelnya panjang), 100px = lebih sempit --}}
                                <td class="fw-bold text-muted" style="width: 130px;">Nama Customer</td>
                                {{-- id="modal-nama" : tempat JavaScript menaruh nama customer. Tanda "-" adalah isi sementara sebelum data dimuat --}}
                                <td id="modal-nama">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Email</td>
                                {{-- Diisi JavaScript dari data.user.email --}}
                                <td id="modal-email">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">No. Telp</td>
                                {{-- Diisi dari data.user.no_telp. Kalau kosong, tampil "-" --}}
                                <td id="modal-telp">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Kategori</td>
                                {{-- Diisi dari data.kategori.nama (jenis masalah). Kalau tidak ada kategori, tampil "-" --}}
                                <td id="modal-kategori">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Tanggal</td>
                                {{-- Diisi tanggal & jam laporan masuk, format Indonesia (contoh: 26 Oktober 2026 14.30) --}}
                                <td id="modal-tanggal">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Status</td>
                                {{-- Diisi badge berwarna sesuai status (kuning/biru/hijau), dibuat oleh JavaScript --}}
                                <td id="modal-status">-</td>
                            </tr>
                        </table>
                    </div>

                    {{-- KOLOM KANAN: foto bukti pengaduan --}}
                    <div class="col-md-6">
                        {{-- mb-1 : margin bawah kecil (jarak label ke foto). Contoh: mb-3 = lebih jauh --}}
                        <p class="fw-bold text-muted mb-1">Foto Bukti</p>
                        {{-- text-center : isi (foto) rata TENGAH. Contoh: text-start = rata KIRI, text-end = rata KANAN
                             Isi bawaan "Tidak ada foto" akan diganti JavaScript jika laporan punya foto --}}
                        <div id="modal-foto" class="text-center">
                            <span class="text-muted">Tidak ada foto</span>
                        </div>
                    </div>
                </div>

                {{-- hr : garis pemisah horizontal antar bagian --}}
                <hr>

                {{-- BAGIAN B: Isi pengaduan
                     - mt-3 : margin ATAS ukuran 3. Contoh: mt-0 = menempel, mt-5 = jauh
                     - col-12 : lebar penuh (12 dari 12 kolom) --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <p class="fw-bold text-muted mb-1">Isi Pengaduan</p>
                        {{-- Kotak isi laporan:
                             - p-3 : padding dalam ukuran 3. Contoh: p-4 = lebih lega, p-2 = lebih rapat
                             - bg-light : latar abu sangat muda. Contoh: bg-warning-subtle = kuning muda, bg-white = putih
                             - rounded : sudut membulat. Contoh: rounded-0 = siku, rounded-3 = lebih bulat, rounded-pill = kapsul
                             Isinya diisi JavaScript dari data.pengaduan --}}
                        <div id="modal-pengaduan" class="p-3 bg-light rounded"></div>
                    </div>
                </div>

                <hr>

                {{-- BAGIAN C: Form ubah status --}}
                <div class="row mt-3">
                    <div class="col-12">
                        <p class="fw-bold text-muted mb-2">Ubah Status</p>
                        {{-- id="statusForm" : form ini dicari JavaScript untuk diisi alamat tujuan (action) sesuai id pengaduan.
                             method="POST" : kirim data lewat POST --}}
                        <form id="statusForm" method="POST">
                            {{-- Token keamanan CSRF Laravel. Kalau dihapus, muncul error 419 --}}
                            @csrf
                            {{-- Browser hanya mendukung GET/POST, jadi method PUT "dipalsukan" lewat input tersembunyi ini
                                 supaya cocok dengan route Laravel bertipe PUT --}}
                            @method('PUT')
                            {{-- Wadah 3 pilihan status berjajar:
                                 - d-flex   : berjajar menyamping
                                 - gap-2    : jarak antar pilihan ukuran 2. Contoh: gap-4 = lebih renggang
                                 - flex-wrap: kalau layar sempit, pilihan turun ke baris berikutnya (tidak keluar layar)
                                 Contoh: tambah "justify-content-end" agar pilihan menempel ke KANAN,
                                         "justify-content-center" agar di TENGAH --}}
                            <div class="d-flex gap-2 flex-wrap">
                                {{-- form-check : gaya pasangan radio + label
                                     name="status" : ketiga radio memakai name sama supaya hanya satu yang bisa dipilih
                                     value : nilai yang dikirim ke server (menunggu / diproses / selesai)
                                     id & for : menyambungkan label dengan radio, sehingga klik label ikut memilih radio --}}
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_menunggu" value="menunggu">
                                    {{-- badge : label kecil berwarna.
                                         bg-warning = kuning, text-dark = teks hitam (agar terbaca di atas kuning).
                                         Contoh: bg-danger text-white = merah dengan teks putih --}}
                                    <label class="form-check-label badge bg-warning text-dark" for="status_menunggu">Menunggu</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_diproses" value="diproses">
                                    {{-- bg-info = biru muda --}}
                                    <label class="form-check-label badge bg-info" for="status_diproses">Diproses</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_selesai" value="selesai">
                                    {{-- bg-success = hijau --}}
                                    <label class="form-check-label badge bg-success" for="status_selesai">Selesai</label>
                                </div>
                            </div>
                            {{-- Tombol simpan:
                                 - btn btn-primary : tombol biru. Contoh: btn-success = hijau, btn-danger = merah, btn-outline-primary = hanya garis biru
                                 - mt-3 : jarak ATAS ke pilihan status. Contoh: mt-4 = lebih jauh
                                 - me-1 : jarak KANAN ikon ke teks (lebih kecil dari me-2)
                                 Contoh: tambah "w-100" agar tombol selebar penuh, atau "float-end" agar tombol ke KANAN
                                 type="submit" : mengirim form saat diklik --}}
                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="bi bi-check-circle me-1"></i>Simpan Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/*
 * showDetail(id): dipanggil saat tombol "detail" di tabel diklik.
 * Parameter id = nomor pengaduan yang mau dilihat.
 * Alurnya: ambil data dari server -> isi ke modal -> buka modal.
 */
function showDetail(id) {
    // Tentukan base path (admin atau petugas)
    // Jika alamat halaman mengandung '/admin/', berarti yang membuka adalah admin, selain itu dianggap petugas.
    // Contoh: kalau nanti ada role baru, tambahkan kondisinya di sini.
    const isAdmin = window.location.pathname.includes('/admin/');
    const basePath = isAdmin ? '/admin' : '/petugas';

    // Fetch data pengaduan
    // Meminta data satu pengaduan ke server (alamat contoh: /petugas/pengaduan/5), hasilnya berupa JSON.
    fetch(`${basePath}/pengaduan/${id}`)
        .then(response => response.json())
        .then(data => {
            // Isi data ke modal
            // textContent dipakai (bukan innerHTML) supaya teks dari customer tidak dibaca sebagai kode HTML. Ini lebih aman.
            document.getElementById('modal-nama').textContent = data.user.nama;
            document.getElementById('modal-email').textContent = data.user.email;
            // "|| '-'" artinya: kalau no_telp kosong, tampilkan "-"
            document.getElementById('modal-telp').textContent = data.user.no_telp || '-';
            // Kategori bisa kosong, jadi dicek dulu sebelum mengambil namanya
            document.getElementById('modal-kategori').textContent = data.kategori ? data.kategori.nama : '-';
            // Format tanggal gaya Indonesia. Contoh pengaturan:
            //   month: 'short' => "Okt" (bukan "Oktober"), month: '2-digit' => "10"
            //   hapus hour & minute => hanya tanggal tanpa jam
            document.getElementById('modal-tanggal').textContent = new Date(data.created_at).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });
            document.getElementById('modal-pengaduan').textContent = data.pengaduan;

            // Status badge
            // Pilih tampilan badge sesuai status. Warna badge sama seperti pilihan radio di form.
            // Contoh: ganti bg-info jadi bg-primary kalau mau badge "Diproses" berwarna biru tua.
            let statusBadge = '';
            switch(data.status) {
                case 'menunggu': statusBadge = '<span class="badge bg-warning text-dark">Menunggu</span>'; break;
                case 'diproses': statusBadge = '<span class="badge bg-info">Diproses</span>'; break;
                case 'selesai': statusBadge = '<span class="badge bg-success">Selesai</span>'; break;
            }
            // innerHTML dipakai di sini karena isinya HTML badge buatan kita sendiri (aman, bukan dari user)
            document.getElementById('modal-status').innerHTML = statusBadge;

            // Foto
            // Jika laporan punya foto, tampilkan thumbnail yang bisa diklik untuk dibuka di tab baru.
            if (data.foto) {
                // img-thumbnail : gambar dengan bingkai tipis & sudut membulat.
                // max-height: 150px : tinggi foto maksimal 150px. Contoh: 250px = foto lebih besar, 100px = lebih kecil
                // target="_blank" : klik foto membuka gambar penuh di tab baru
                document.getElementById('modal-foto').innerHTML = `
                    <a href="/storage/${data.foto}" target="_blank">
                        <img src="/storage/${data.foto}" alt="Foto pengaduan" class="img-thumbnail" style="max-height: 150px;">
                    </a>
                `;
            } else {
                // Tidak ada foto: kembalikan tulisan bawaan (penting saat modal dibuka ulang untuk laporan lain)
                document.getElementById('modal-foto').innerHTML = '<span class="text-muted">Tidak ada foto</span>';
            }

            // Set radio button status
            // Centang radio yang nilainya sama dengan status saat ini, sehingga pilihan awal sesuai kondisi laporan.
            document.querySelectorAll('input[name="status"]').forEach(radio => {
                radio.checked = (radio.value === data.status);
            });

            // Set form action
            // Mengisi alamat tujuan form dengan id pengaduan yang sedang dibuka (contoh: /petugas/pengaduan/5/status)
            document.getElementById('statusForm').action = `${basePath}/pengaduan/${id}/status`;

            // Tampilkan modal
            // Membuat objek modal Bootstrap lalu membukanya. Butuh Bootstrap JS (sudah dimuat di layouts/app.blade.php).
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        })
        .catch(error => {
            // Jika gagal (internet putus, server error, dll): catat error di console browser (F12) dan beri tahu pengguna
            console.error('Error:', error);
            alert('Gagal memuat detail pengaduan');
        });
}
</script>