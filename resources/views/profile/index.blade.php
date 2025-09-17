@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-12">
                <!-- Header -->
                <div class="profile-header mb-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img style="width: 150px; height: 150px;" alt="Foto Profil" class="profile-image rounded-circle"
                                src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : asset('img/undraw_profile_2.svg') }}">
                        </div>
                        <div class="col">
                            <h3 class="mb-1">{{ Auth::user()->name }}</h3>
                            <p class="mb-0">{{ Auth::user()->email }}</p>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark">
                                    {{ Auth::user()->role->name }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="card profile-card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified mb-4" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active custom-tab-btn" id="profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#profile" type="button" role="tab" aria-controls="profile"
                                    aria-selected="true">
                                    <span class="d-block d-sm-none"><i class="fas fa-user"></i></span>
                                    <span class="d-none d-sm-block">
                                        <i class="fas fa-user-circle me-2"></i> Informasi Profil
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link custom-tab-btn" id="password-tab" data-bs-toggle="pill"
                                    data-bs-target="#password" type="button" role="tab" aria-controls="password"
                                    aria-selected="false">
                                    <span class="d-block d-sm-none"><i class="fas fa-key"></i></span>
                                    <span class="d-none d-sm-block">
                                        <i class="fas fa-shield-alt me-2"></i> Ubah Password
                                    </span>
                                </button>
                            </li>
                        </ul>

                        <style>
                            .nav-tabs-custom {
                                border-bottom: 2px solid #e9ecef;
                            }

                            .nav-tabs-custom .nav-link {
                                border: none;
                                padding: 1rem 1.5rem;
                                color: #495057;
                                transition: all 0.3s;
                            }

                            .nav-tabs-custom .nav-link:hover {
                                color: #3490dc;
                                border-color: transparent;
                            }

                            .nav-tabs-custom .nav-link.active {
                                color: #3490dc;
                                background: transparent;
                                border-bottom: 2px solid #3490dc;
                                margin-bottom: -2px;
                            }

                            .custom-tab-btn {
                                font-weight: 500;
                                letter-spacing: 0.5px;
                            }

                            .custom-tab-btn i {
                                font-size: 1.1rem;
                            }

                            @media (max-width: 576px) {
                                .nav-tabs-custom .nav-link {
                                    padding: 0.75rem;
                                }
                            }
                        </style>

                        <div class="tab-content" id="profileTabsContent">
                            <!-- Tab Informasi Profil -->
                            <div class="tab-pane fade show active" id="profile" role="tabpanel"
                                aria-labelledby="profile-tab">
                                <form id="profileForm" action="{{ route('profile.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label info-label">Nama Lengkap</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                                                required>
                                            <div class="invalid-feedback" id="name-error"></div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label info-label">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email"
                                                value="{{ old('email', Auth::user()->email) }}" required>
                                            <div class="invalid-feedback" id="email-error"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="no_telp" class="form-label info-label">Nomor Telepon</label>
                                            <input type="text"
                                                class="form-control @error('no_telp') is-invalid @enderror" id="no_telp"
                                                name="no_telp" value="{{ old('no_telp', Auth::user()->no_telp) }}">
                                            <div class="invalid-feedback" id="no_telp-error"></div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="foto" class="form-label info-label">Foto Profil</label>
                                            <input type="file" class="form-control @error('foto') is-invalid @enderror"
                                                id="foto" name="foto" accept="image/*">
                                            <div class="invalid-feedback" id="foto-error"></div>
                                            <small class="form-text text-muted">Maksimal 2MB, format: JPG, PNG,
                                                JPEG</small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="alamat" class="form-label info-label">Alamat</label>
                                        <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', Auth::user()->alamat) }}</textarea>
                                        <div class="invalid-feedback" id="alamat-error"></div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" id="btnSaveProfile">
                                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab Ubah Password -->
                            <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                <form id="passwordForm" action="{{ route('password.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="current_password" class="form-label info-label">Password Saat
                                                Ini</label>
                                            <input type="password"
                                                class="form-control @error('current_password') is-invalid @enderror"
                                                id="current_password" name="current_password" required>
                                            <div class="invalid-feedback" id="current_password-error"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="password" class="form-label info-label">Password Baru</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                id="password" name="password" required>
                                            <div class="invalid-feedback" id="password-error"></div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="password_confirmation" class="form-label info-label">Konfirmasi
                                                Password Baru</label>
                                            <input type="password" class="form-control" id="password_confirmation"
                                                name="password_confirmation" required>
                                            <div class="invalid-feedback" id="password_confirmation-error"></div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Pastikan password Anda minimal 8 karakter dan mengandung kombinasi huruf, angka, dan
                                        simbol.
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" id="btnChangePassword">
                                            <i class="fas fa-key me-2"></i> Ubah Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function activateTab(target) {
                // Reset semua tab & pane
                $('#profileTabs button').removeClass('active').attr('aria-selected', 'false');
                $('.tab-pane').removeClass('show active');

                // Tab yang sesuai hash
                $('#profileTabs button[data-bs-target="' + target + '"]').addClass('active').attr('aria-selected',
                    'true');
                $(target).addClass('show active');
            }

            // Kalau ada hash di URL (contoh: #password)
            if (window.location.hash) {
                activateTab(window.location.hash);
            }

            // Kalau klik tab, update hash di URL
            $('#profileTabs button[data-bs-toggle="pill"]').on('click', function(e) {
                var target = $(this).data('bs-target');
                activateTab(target);
                history.replaceState(null, null, target); // ubah hash di URL tanpa reload
            });

            // Preview image sebelum upload
            $('#foto').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('.profile-image').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Form profil dengan AJAX
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();

                // Reset error messages
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Disable button and show loading
                $('#btnSaveProfile').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...');

                // Create FormData object for file upload
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        // Enable button
                        $('#btnSaveProfile').prop('disabled', false)
                            .html('<i class="fas fa-save me-2"></i>Simpan Perubahan');

                        if (xhr.status === 422) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;

                            // Display each error
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });

                            // Show detailed error message in SweetAlert
                            var errorMessages = '';
                            $.each(errors, function(key, value) {
                                errorMessages += '<li>' + key + ': ' + value[0] +
                                    '</li>';
                            });

                            Swal.fire({
                                icon: 'error',
                                title: xhr.responseJSON.message || 'Validasi gagal',
                                html: '<ul class="text-left">' + errorMessages +
                                    '</ul>',
                            });
                        } else {
                            // General error
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan! Silakan coba lagi.',
                            });
                        }
                    },
                    complete: function() {
                        // Enable button
                        $('#btnSaveProfile').prop('disabled', false)
                            .html('<i class="fas fa-save me-2"></i>Simpan Perubahan');
                    }
                });
            });
            // Form password dengan AJAX
            $('#passwordForm').on('submit', function(e) {
                e.preventDefault();

                // Reset error messages
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Disable button and show loading
                $('#btnChangePassword').prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin me-2"></i> Menyimpan...');

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Show success message
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });

                        // Reset form
                        $('#passwordForm')[0].reset();
                    },
                    error: function(xhr) {
                        // Enable button
                        $('#btnChangePassword').prop('disabled', false)
                            .html('<i class="fas fa-key me-2"></i>Ubah Password');

                        if (xhr.status === 422) {
                            // Validation errors
                            var errors = xhr.responseJSON.errors;

                            // Display each error
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid');
                                $('#' + key + '-error').text(value[0]);
                            });

                            // Show detailed error message in SweetAlert
                            var errorMessages = '';
                            $.each(errors, function(key, value) {
                                errorMessages += '<li>' + key + ': ' + value[0] +
                                    '</li>';
                            });

                            Swal.fire({
                                icon: 'error',
                                title: xhr.responseJSON.message || 'Validasi gagal',
                                html: '<ul class="text-left">' + errorMessages +
                                    '</ul>',
                            });
                        } else {
                            // General error
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan! Silakan coba lagi.',
                            });
                        }
                    },
                    complete: function() {
                        // Enable button
                        $('#btnChangePassword').prop('disabled', false)
                            .html('<i class="fas fa-key me-2"></i>Ubah Password');
                    }
                });
            });
        });
    </script>
@endpush
