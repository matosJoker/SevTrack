@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Service Advisor</h5>
            <button class="btn btn-primary" data-toggle="modal" data-target="#serviceadvisorModal">
                <i class="fas fa-plus"></i> Tambah Service Advisor
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Service Advisor</th>
                            <th>Alamat</th>
                            <th>No. Telp</th>
                            <th>Email</th>
                            <th>Bengkel</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serviceadvisor as $key => $dt)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $dt->nama_service_advisor }}</td>
                                <td>{{ $dt->alamat }}</td>
                                <td>{{ $dt->no_telp ?? '-' }}</td>
                                <td>{{ $dt->email ?? '-' }}</td>
                                <td>{{ $dt->bengkel ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $dt->id }}"
                                        data-nama="{{ $dt->nama_service_advisor }}" data-alamat="{{ $dt->alamat }}"
                                        data-no_telp="{{ $dt->no_telp }}" data-email="{{ $dt->email }}"
                                        data-bengkel="{{ $dt->id_bengkel }}" data-toggle="modal" data-target="#serviceadvisorModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $dt->id }}">
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
    <div class="modal fade" id="serviceadvisorModal" tabindex="-1" aria-labelledby="serviceadvisorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceadvisorModalLabel">Tambah Service Advisor</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="serviceadvisorForm">
                    <div class="modal-body">
                        <input type="hidden" id="serviceadvisor_id" name="serviceadvisor_id">
                        <div class="mb-3">
                            <label for="nama_service_advisor" class="form-label">Nama Service Advisor</label>
                            <input type="text" class="form-control" id="nama_service_advisor" name="nama_service_advisor"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3" id="bengkel-group">
                            <label for="id_bengkel" class="form-label">Bengkel</label>
                            <select class="form-control" name="id_bengkel" id="id_bengkel">
                                <option value="">Pilih Bengkel</option>
                                @foreach ($bengkel as $b)
                                    <option value="{{ $b->id }}">{{ $b->nama_bengkel }}</option>
                                @endforeach
                            </select>
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
            $('#serviceadvisorModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                if (button.hasClass('edit-btn')) {
                    modal.find('.modal-title').text('Edit Service Advisor');
                    modal.find('#serviceadvisor_id').val(button.data('id'));
                    modal.find('#nama_service_advisor').val(button.data('nama'));
                    modal.find('#alamat').val(button.data('alamat'));
                    modal.find('#no_telp').val(button.data('no_telp'));
                    modal.find('#email').val(button.data('email'));

                    // Set role
                    var roleId = button.data('role');
                    modal.find('#id_role').val(roleId);

                    // Set bengkel (jika ada)
                    var bengkelId = button.data('bengkel');
                    modal.find('#id_bengkel').val(bengkelId);

                    
                } else {
                    modal.find('.modal-title').text('Tambah Service Advisor');
                    modal.find('#serviceadvisor_id').val('');
                    modal.find('#serviceadvisorForm')[0].reset();
                    $('#id_bengkel').val('');
                }
            });

            // Handle form submission
            $('#serviceadvisorForm').submit(function(e) {
                e.preventDefault();
                if (!$('#id_bengkel').val()) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Bengkel wajib diisi ',
                        icon: 'warning'
                    });
                    return;
                }

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
                        var serviceadvisorId = $('#serviceadvisor_id').val();
                        var url = serviceadvisorId ? `/serviceadvisor/${serviceadvisorId}` : '/serviceadvisor';
                        var method = serviceadvisorId ? 'PUT' : 'POST';
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        formData += '&_token=' + encodeURIComponent(csrfToken);

                        $.ajax({
                            url: url,
                            type: method,
                            data: formData,
                            success: function(response) {
                                $('#serviceadvisorModal').modal('hide');
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data service advisor berhasil disimpan.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let message = 'Terjadi kesalahan. Silakan coba lagi.';
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    message = xhr.responseJSON.error;
                                }
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: message,
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
                    text: 'Apakah Anda yakin ingin menghapus service advisor ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var serviceadvisorId = $(this).data('id');
                        $.ajax({
                            url: `/serviceadvisor/${serviceadvisorId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data service advisor berhasil dihapus.',
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
