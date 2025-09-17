@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Layanan</h5>
            <button class="btn btn-primary" data-toggle="modal" data-target="#layModal">
                <i class="fas fa-plus"></i> Tambah Layanan
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Layanan</th>
                            <th>Diskripsi</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($layanan as $key => $lay)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $lay->nama_layanan }}</td>
                                <td>{{ $lay->deskripsi }}</td>
                                <td>{{ formatRupiah($lay->harga) }}</td>
                                <td>
                                    @if ($lay->status == 'aktif')
                                        <span class="badge bg-success" style="color: white">Aktif</span>
                                    @else
                                        <span class="badge bg-danger" style="color: white">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $lay->id }}"
                                        data-nama="{{ $lay->nama_layanan }}" data-deskripsi="{{ $lay->deskripsi }}"
                                        data-harga="{{ $lay->harga }}" data-email="{{ $lay->email }}"
                                        data-website="{{ $lay->website }}" data-toggle="modal" data-target="#layModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $lay->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @if ($lay->status == 'aktif')
                                        <button class="btn btn-sm btn-danger toggle-status-btn"
                                            data-id="{{ $lay->id }}" data-status="nonaktif">
                                            Non Aktifkan
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-success toggle-status-btn"
                                            data-id="{{ $lay->id }}" data-status="aktif">
                                            Aktifkan
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="layModal" tabindex="-1" aria-labelledby="layModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="layModalLabel">Tambah Layanan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="layForm">
                    <div class="modal-body">
                        <input type="hidden" id="id_layanan" name="id_layanan">
                        <div class="mb-3">
                            <label for="nama_layanan" class="form-label">Nama Layanan</label>
                            <input type="text" class="form-control" id="nama_layanan" name="nama_layanan" required>
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Diskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="text" class="form-control text-end" id="harga" name="harga"
                                placeholder="0" style="text-align: right;">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('harga').addEventListener('keyup', function(e) {
            // Hapus semua karakter selain angka
            let value = this.value.replace(/[^0-9]/g, '');

            // Format ke Rupiah
            let rupiah = new Intl.NumberFormat('id-ID').format(value);

            // Update nilai input
            this.value = rupiah;
        });
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [
                    [5, "asc"]
                ] // Sort by order column
            });
            // Handle modal show event
            $('#layModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                if (button.hasClass('edit-btn')) {
                    modal.find('.modal-title').text('Edit Layanan');
                    modal.find('#id_layanan').val(button.data('id'));
                    modal.find('#nama_layanan').val(button.data('nama'));
                    modal.find('#deskripsi').val(button.data('deskripsi'));
                    modal.find('#harga').val(button.data('harga'));
                } else {
                    modal.find('.modal-title').text('Tambah Layanan');
                    modal.find('#id_layanan').val('');
                    modal.find('#layForm')[0].reset();
                }
            });

            // Handle form submission
            $('#layForm').submit(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah data sudah benar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = $(this).serialize();
                        var layId = $('#id_layanan').val();
                        var url = layId ? `/layanan/${layId}` : '/layanan';
                        var method = layId ? 'PUT' : 'POST';
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        formData += '&_token=' + encodeURIComponent(csrfToken);

                        $.ajax({
                            url: url,
                            type: method,
                            data: formData,
                            success: function(response) {
                                $('#layModal').modal('hide');
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data lay berhasil disimpan.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Handle delete button
            $('.delete-btn').click(function() {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menghapus layanan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var layId = $(this).data('id');
                        $.ajax({
                            url: `/layanan/${layId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data lay berhasil dihapus.',
                                    icon: 'success',
                                    timer: 1200,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });

        // Handle toggle status button
        $('.toggle-status-btn').click(function() {
            var layId = $(this).data('id');
            var newStatus = $(this).data('status');

            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin ${newStatus === 'aktif' ? 'mengaktifkan' : 'menonaktifkan'} layanan ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/layanan/status`,
                        type: 'POST', 
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            status: newStatus,
                            id: layId
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: `Layanan berhasil ${newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan'}.`,
                                icon: 'success',
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan. Silakan coba lagi.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
