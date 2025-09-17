@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar User</h5>
            <button class="btn btn-primary" data-toggle="modal" data-target="#userModal">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama User</th>
                            <th>Alamat</th>
                            <th>No. Telp</th>
                            <th>Email</th>
                            <th>Bengkel</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $key => $usr)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $usr->name }}</td>
                                <td>{{ $usr->alamat }}</td>
                                <td>{{ $usr->no_telp ?? '-' }}</td>
                                <td>{{ $usr->email ?? '-' }}</td>
                                <td>{{ $usr->bengkel ?? '-' }}</td>
                                <td>{{ $usr->role_name ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $usr->id }}"
                                        data-nama="{{ $usr->name }}" data-alamat="{{ $usr->alamat }}"
                                        data-no_telp="{{ $usr->no_telp }}" data-email="{{ $usr->email }}"
                                        data-role="{{ $usr->role_id }}" data-bengkel="{{ $usr->bengkel_id }}"
                                        data-toggle="modal" data-target="#userModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $usr->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-sm btn-success reset-btn" data-id="{{ $usr->id }}">
                                        <i class="fas fa-sync"></i>
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
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Tambah User</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="userForm">
                    <div class="modal-body">
                        <input type="hidden" id="user_id" name="user_id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama User</label>
                            <input type="text" class="form-control" id="name" name="name" required>
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
                        <div class="mb-3">
                            <label for="id_role" class="form-label">Role</label>
                            <select class="form-control" name="id_role" id="id_role">
                                <option value="">Pilih Role</option>
                                @foreach ($role as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="bengkel-group" style="display:none;">
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
            function toggleBengkel() {
                var selectedRole = $('#id_role option:selected').text().toLowerCase();
                if (selectedRole === 'mekanik') {
                    $('#bengkel-group').show();
                } else {
                    $('#bengkel-group').hide();
                    $('#id_bengkel').val('');
                }
            }
            $('#id_role').on('change', toggleBengkel);

            // When editing, show/hide bengkel based on selected role
            $('#userModal').on('show.bs.modal', function(event) {
                setTimeout(toggleBengkel, 100); // Ensure after values are set
            });
            $('#dataTable').DataTable({
                "order": [
                    [5, "asc"]
                ] // Sort by order column
            });
            // Handle modal show event
            $('#userModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                if (button.hasClass('edit-btn')) {
                    modal.find('.modal-title').text('Edit User');
                    modal.find('#user_id').val(button.data('id'));
                    modal.find('#name').val(button.data('nama'));
                    modal.find('#alamat').val(button.data('alamat'));
                    modal.find('#no_telp').val(button.data('no_telp'));
                    modal.find('#email').val(button.data('email'));

                    // Set role
                    var roleId = button.data('role');
                    modal.find('#id_role').val(roleId);

                    // Set bengkel (jika ada)
                    var bengkelId = button.data('bengkel');
                    modal.find('#id_bengkel').val(bengkelId);

                    // Panggil toggleBengkel agar tampil sesuai role
                    toggleBengkel();
                } else {
                    modal.find('.modal-title').text('Tambah User');
                    modal.find('#user_id').val('');
                    modal.find('#userForm')[0].reset();
                    $('#id_bengkel').val('');
                    toggleBengkel();
                }
            });

            // Handle form submission
            $('#userForm').submit(function(e) {
                e.preventDefault();
                var selectedRole = $('#id_role option:selected').text().toLowerCase();
                if (selectedRole === 'mekanik' && !$('#id_bengkel').val()) {
                    Swal.fire({
                        title: 'Peringatan!',
                        text: 'Bengkel wajib diisi untuk role Mekanik.',
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
                        var userId = $('#user_id').val();
                        var url = userId ? `/user/${userId}` : '/user';
                        var method = userId ? 'PUT' : 'POST';
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        formData += '&_token=' + encodeURIComponent(csrfToken);

                        $.ajax({
                            url: url,
                            type: method,
                            data: formData,
                            success: function(response) {
                                $('#userModal').modal('hide');
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data user berhasil disimpan.',
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
                    text: 'Apakah Anda yakin ingin menghapus user ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var userId = $(this).data('id');
                        $.ajax({
                            url: `/user/${userId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data user berhasil dihapus.',
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
            $('.reset-btn').click(function() {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin mereset password user ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var userId = $(this).data('id');
                        $.ajax({
                            url: `/user/${userId}/reset`,
                            type: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data password berhasil direset.',
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
