@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Bengkel</h5>
            <button class="btn btn-primary" data-toggle="modal" data-target="#bengkelModal">
                <i class="fas fa-plus"></i> Tambah Bengkel
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Bengkel</th>
                            <th>Alamat</th>
                            <th>No. Telp</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bengkels as $key => $bengkel)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $bengkel->nama_bengkel }}</td>
                                <td>{{ $bengkel->alamat }}</td>
                                <td>{{ $bengkel->no_telp ?? '-' }}</td>
                                <td>{{ $bengkel->email ?? '-' }}</td>
                                <td>
                                    @if ($bengkel->website)
                                        <a href="{{ $bengkel->website }}" target="_blank">{{ $bengkel->website }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $bengkel->id }}"
                                        data-nama="{{ $bengkel->nama_bengkel }}" data-alamat="{{ $bengkel->alamat }}"
                                        data-no_telp="{{ $bengkel->no_telp }}" data-email="{{ $bengkel->email }}"
                                        data-website="{{ $bengkel->website }}" data-toggle="modal"
                                        data-target="#bengkelModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $bengkel->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="bengkelModal" tabindex="-1" aria-labelledby="bengkelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bengkelModalLabel">Tambah Bengkel</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="bengkelForm">
                    <div class="modal-body">
                        <input type="hidden" id="bengkel_id" name="bengkel_id">
                        <div class="mb-3">
                            <label for="nama_bengkel" class="form-label">Nama Bengkel</label>
                            <input type="text" class="form-control" id="nama_bengkel" name="nama_bengkel" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website">
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
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [
                    [5, "asc"]
                ] // Sort by order column
            });
            // Handle modal show event
            $('#bengkelModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                if (button.hasClass('edit-btn')) {
                    modal.find('.modal-title').text('Edit Bengkel');
                    modal.find('#bengkel_id').val(button.data('id'));
                    modal.find('#nama_bengkel').val(button.data('nama'));
                    modal.find('#alamat').val(button.data('alamat'));
                    modal.find('#no_telp').val(button.data('no_telp'));
                    modal.find('#email').val(button.data('email'));
                    modal.find('#website').val(button.data('website'));
                } else {
                    modal.find('.modal-title').text('Tambah Bengkel');
                    modal.find('#bengkel_id').val('');
                    modal.find('#bengkelForm')[0].reset();
                }
            });

            // Handle form submission
            $('#bengkelForm').submit(function(e) {
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
                        var bengkelId = $('#bengkel_id').val();
                        var url = bengkelId ? `/bengkel/${bengkelId}` : '/bengkel';
                        var method = bengkelId ? 'PUT' : 'POST';
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        formData += '&_token=' + encodeURIComponent(csrfToken);

                        $.ajax({
                            url: url,
                            type: method,
                            data: formData,
                            success: function(response) {
                                $('#bengkelModal').modal('hide');
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data bengkel berhasil disimpan.',
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
                    text: 'Apakah Anda yakin ingin menghapus bengkel ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var bengkelId = $(this).data('id');
                        $.ajax({
                            url: `/bengkel/${bengkelId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data bengkel berhasil dihapus.',
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
    </script>
@endpush
