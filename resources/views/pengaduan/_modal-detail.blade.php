<!-- Modal Detail Pengaduan -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Detail Pengaduan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold text-muted" style="width: 130px;">Nama Customer</td>
                                <td id="modal-nama">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Email</td>
                                <td id="modal-email">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">No. Telp</td>
                                <td id="modal-telp">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Kategori</td>
                                <td id="modal-kategori">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Tanggal</td>
                                <td id="modal-tanggal">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Status</td>
                                <td id="modal-status">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <p class="fw-bold text-muted mb-1">Foto Bukti</p>
                        <div id="modal-foto" class="text-center">
                            <span class="text-muted">Tidak ada foto</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mt-3">
                    <div class="col-12">
                        <p class="fw-bold text-muted mb-1">Isi Pengaduan</p>
                        <div id="modal-pengaduan" class="p-3 bg-light rounded"></div>
                    </div>
                </div>
                <hr>
                <div class="row mt-3">
                    <div class="col-12">
                        <p class="fw-bold text-muted mb-2">Ubah Status</p>
                        <form id="statusForm" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_menunggu" value="menunggu">
                                    <label class="form-check-label badge bg-warning text-dark" for="status_menunggu">Menunggu</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_diproses" value="diproses">
                                    <label class="form-check-label badge bg-info" for="status_diproses">Diproses</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_selesai" value="selesai">
                                    <label class="form-check-label badge bg-success" for="status_selesai">Selesai</label>
                                </div>
                            </div>
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
function showDetail(id) {
    // Tentukan base path (admin atau petugas)
    const isAdmin = window.location.pathname.includes('/admin/');
    const basePath = isAdmin ? '/admin' : '/petugas';

    // Fetch data pengaduan
    fetch(`${basePath}/pengaduan/${id}`)
        .then(response => response.json())
        .then(data => {
            // Isi data ke modal
            document.getElementById('modal-nama').textContent = data.user.nama;
            document.getElementById('modal-email').textContent = data.user.email;
            document.getElementById('modal-telp').textContent = data.user.no_telp || '-';
            document.getElementById('modal-kategori').textContent = data.kategori ? data.kategori.nama : '-';
            document.getElementById('modal-tanggal').textContent = new Date(data.created_at).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
            });
            document.getElementById('modal-pengaduan').textContent = data.pengaduan;

            // Status badge
            let statusBadge = '';
            switch(data.status) {
                case 'menunggu': statusBadge = '<span class="badge bg-warning text-dark">Menunggu</span>'; break;
                case 'diproses': statusBadge = '<span class="badge bg-info">Diproses</span>'; break;
                case 'selesai': statusBadge = '<span class="badge bg-success">Selesai</span>'; break;
            }
            document.getElementById('modal-status').innerHTML = statusBadge;

            // Foto
            if (data.foto) {
                document.getElementById('modal-foto').innerHTML = `
                    <a href="/storage/${data.foto}" target="_blank">
                        <img src="/storage/${data.foto}" alt="Foto pengaduan" class="img-thumbnail" style="max-height: 150px;">
                    </a>
                `;
            } else {
                document.getElementById('modal-foto').innerHTML = '<span class="text-muted">Tidak ada foto</span>';
            }

            // Set radio button status
            document.querySelectorAll('input[name="status"]').forEach(radio => {
                radio.checked = (radio.value === data.status);
            });

            // Set form action
            document.getElementById('statusForm').action = `${basePath}/pengaduan/${id}/status`;

            // Tampilkan modal
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat detail pengaduan');
        });
}
</script>
