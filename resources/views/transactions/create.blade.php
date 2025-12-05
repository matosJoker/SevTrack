@extends('layouts.app')

@section('title', 'Tambah Transaksi Bengkel')

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4 mt-4">
            <h1 class="h3 mb-0 text-gray-800">Tambah Transaksi Bengkel</h1>
        </div>

        <div id="error-alert" class="alert alert-danger d-none">
            <ul id="error-list"></ul>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Form Transaksi</h6>
                    </div>
                    <div class="card-body">
                        <form id="transactionForm" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_service_advisor">Service Advisor</label>
                                        <select class="form-control" id="id_service_advisor" name="id_service_advisor"
                                            required>
                                            <option value="">Pilih</option>
                                            @foreach ($serviceadvisor as $sa)
                                                <option value="{{ $sa->id }}">{{ $sa->nama_service_advisor }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3">Data Kendaraan</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="plat_nomor">Plat Nomor</label>
                                        <input type="text" class="form-control" id="plat_nomor" name="plat_nomor"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipe_kendaraan">Tipe Kendaraan</label>
                                        <input type="text" class="form-control" id="tipe_kendaraan"
                                            name="tipe_kendaraan">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kilometer">Kilometer</label>
                                        <input type="number" class="form-control" id="kilometer" name="kilometer">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="no_wo">Nomor WO</label>
                                        <input type="text" class="form-control" id="no_wo" name="no_wo">
                                    </div>
                                </div>
                            </div>
                            <h5 class="mt-4 mb-3">Pekerjaan</h5>
                            <div id="services-container">
                                <div class="service-row row mb-3">
                                    <div class="col-md-4">
                                        <select class="form-control service-select" name="services[0][id_layanan]" required>
                                            <option value="">Pilih Pekerjaan</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}" data-harga="{{ $service->harga }}">
                                                    {{ $service->nama_layanan }} - Rp
                                                    {{ number_format($service->harga, 0, ',', '.') }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check custom-price-toggle">
                                            <input class="form-check-input custom-price-checkbox" type="checkbox"
                                                name="services[0][flag_harga_khusus]" id="custom_price_0" value="1">
                                            <label class="form-check-label" for="custom_price_0">
                                                Harga Khusus
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control harga-input" name="services[0][harga]"
                                            placeholder="Harga" required min="0" readonly>
                                    </div>
                                    <div class="col-md-2">
                                        <!-- Input foto untuk layanan -->
                                        <label class="add-photo-btn text-success" title="Tambah Foto Sebelum Service">
                                            <i class="fas fa-camera fa-lg"> </i> Foto Sebelum Service
                                            <input type="file" class="d-none service-photo-input"
                                                name="services[0][foto_layanan][]" multiple accept="image/*">
                                        </label>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger remove-service" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Container untuk preview foto layanan -->
                                    <div class="col-12 mt-2 service-photos-container" id="service-photos-0"></div>
                                </div>
                            </div>

                            <button type="button" id="add-service" class="btn btn-secondary mt-2">
                                <i class="fas fa-plus"></i> Tambah Pekerjaan
                            </button>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Simpan Transaksi
                                </button>
                                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let serviceCount = 1;

            // Preview foto kendaraan
            $('#foto_kendaraan').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#photo-preview').attr('src', e.target.result);
                        $('#photo-preview-container').removeClass('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Tambah layanan
            $('#add-service').click(function() {
                const newRow = `
                <div class="service-row row mb-3">
                    <div class="col-md-4">
                        <select class="form-control service-select" name="services[${serviceCount}][id_layanan]" required>
                            <option value="">Pilih Pekerjaan</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" data-harga="{{ $service->harga }}">{{ $service->nama_layanan }} - Rp {{ number_format($service->harga, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check custom-price-toggle">
                            <input class="form-check-input custom-price-checkbox" type="checkbox" name="services[${serviceCount}][flag_harga_khusus]" id="custom_price_${serviceCount}" value="1">
                            <label class="form-check-label" for="custom_price_${serviceCount}">
                                Harga Khusus
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control harga-input" name="services[${serviceCount}][harga]" placeholder="Harga" required min="0" readonly>
                    </div>
                     <div class="col-md-2">
                        <!-- Input foto untuk layanan -->
                        <label class="add-photo-btn text-success" title="Tambah Foto Sebelum Service">
                            <i class="fas fa-camera fa-lg"> </i> Foto Sebelum Service
                            <input type="file" class="d-none service-photo-input"
                                name="services[${serviceCount}][foto_layanan][]" multiple accept="image/*">
                        </label>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-service">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    
                    <!-- Container untuk preview foto layanan -->
                    <div class="col-12 mt-2 service-photos-container" id="service-photos-${serviceCount}"></div>
                </div>
            `;

                $('#services-container').append(newRow);
                serviceCount++;

                // Enable semua tombol hapus
                $('.remove-service').prop('disabled', false);
            });

            // Hapus layanan
            $(document).on('click', '.remove-service', function() {
                if ($('.service-row').length > 1) {
                    $(this).closest('.service-row').remove();

                    // Jika hanya tersisa satu, disable tombol hapus
                    if ($('.service-row').length === 1) {
                        $('.remove-service').prop('disabled', true);
                    }
                }
            });

            // Auto-fill harga ketika layanan dipilih
            $(document).on('change', '.service-select', function() {
                const selectedOption = $(this).find('option:selected');
                const harga = selectedOption.data('harga');
                const hargaInput = $(this).closest('.service-row').find('.harga-input');

                if (harga) {
                    hargaInput.val(harga);
                } else {
                    hargaInput.val('');
                }
            });

            // Toggle harga khusus
            $(document).on('change', '.custom-price-checkbox', function() {
                const isChecked = $(this).is(':checked');
                const hargaInput = $(this).closest('.service-row').find('.harga-input');

                if (isChecked) {
                    hargaInput.prop('readonly', false);
                } else {
                    // Kembalikan ke harga asli layanan
                    const selectedOption = $(this).closest('.service-row').find(
                        '.service-select option:selected');
                    const originalPrice = selectedOption.data('harga');
                    hargaInput.val(originalPrice);
                    hargaInput.prop('readonly', true);
                }
            });

            // Handle upload foto layanan
            $(document).on('change', '.service-photo-input', function() {
                const files = this.files;
                const serviceIndex = $(this).closest('.service-row').index();
                const previewContainer = $(this).closest('.service-row').find('.service-photos-container');

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (file.type.match('image.*')) {
                        const reader = new FileReader();

                        reader.onload = (function(theFile, index) {
                            return function(e) {
                                // Buat elemen preview foto
                                const photoDiv = $(
                                    '<div class="service-photo-container"></div>');
                                const img = $(
                                    `<img class="service-photo-preview" src="${e.target.result}" title="${theFile.name}">`
                                );
                                const removeBtn = $(
                                    '<span class="remove-photo">&times;</span>');

                                // Tambahkan event untuk menghapus foto
                                removeBtn.click(function() {
                                    photoDiv.remove();
                                    // Hapus file dari input
                                    const dataTransfer = new DataTransfer();
                                    const input = $(`input.service-photo-input`)[
                                        serviceIndex];

                                    for (let i = 0; i < input.files.length; i++) {
                                        if (i !== index) {
                                            dataTransfer.items.add(input.files[i]);
                                        }
                                    }

                                    input.files = dataTransfer.files;
                                });

                                photoDiv.append(img);
                                photoDiv.append(removeBtn);
                                previewContainer.append(photoDiv);
                            };
                        })(file, i);

                        reader.readAsDataURL(file);
                    }
                }
            });

            // Cek customer berdasarkan plat nomor
            $('#plat_nomor').blur(function() {
                const platNomor = $(this).val();

                if (platNomor) {
                    Swal.showLoading();
                    $.ajax({
                        url: '{{ route('transactions.findByPlat') }}',
                        method: 'GET',
                        data: {
                            plat_nomor: platNomor
                        },
                        success: function(response) {
                            if (response.customer !== null) {
                                Swal.fire({
                                    title: 'Kendaraan ditemukan!',
                                    text: '',
                                })
                                $('#tipe_kendaraan').val(response.customer.tipe_kendaraan);
                                $('#no_wo').val(response.customer.no_wo);
                                // $('#kilometer').val(response.customer.kilometer);

                            } else {
                                Swal.fire({
                                    title: 'Kendaraan tidak ditemukan!',
                                    text: '',
                                })
                                $('#tipe_kendaraan').val('');
                                $('#no_wo').val('');
                                // $('#kilometer').val('');
                            }
                        },
                        complete: function() {
                            Swal.hideLoading();
                        }
                    });
                }
            });

            // Submit form dengan AJAX
            $('#transactionForm').submit(function(e) {
                e.preventDefault();

                // Validate at least one service is selected
                if ($('.service-row').length < 1) {
                    Swal.fire({
                        title: 'Validasi Error!',
                        text: 'Minimal satu layanan harus dipilih',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Validate photos are uploaded for each service
                let photosMissing = false;
                $('.service-row').each(function() {
                    const photoInput = $(this).find('.service-photo-input')[0];
                    if (!photoInput.files || photoInput.files.length === 0) {
                        photosMissing = true;
                        return false;
                    }
                });

                if (photosMissing) {
                    Swal.fire({
                        title: 'Validasi Error!',
                        text: 'Foto Sebelum Service wajib dipilih',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                const submitBtn = $('#submitBtn');
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Memproses...');
                Swal.fire({
                    title: 'Simpan Transaksi?',
                    text: "Apakah Anda yakin ingin menyimpan transaksi ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim data form via AJAX
                        const formData = new FormData(this);

                        $.ajax({
                            url: '{{ route('transactions.store') }}',
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            beforeSend: function() {
                                // Tampilkan loading
                                Swal.showLoading();
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href =
                                            '{{ route('transactions.index') }}';
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.hideLoading();

                                if (xhr.status === 422) {
                                    // Validasi error
                                    const errors = xhr.responseJSON.errors;
                                    let errorHtml = '';

                                    for (const field in errors) {
                                        errorHtml += `<li>${errors[field][0]}</li>`;
                                    }

                                    $('#error-list').html(errorHtml);
                                    $('#error-alert').removeClass('d-none');

                                    // Scroll ke atas untuk melihat error
                                    window.scrollTo(0, 0);
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Terjadi kesalahan saat menyimpan data.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            },
                            complete: function() {
                                submitBtn.prop('disabled', false).html(
                                    '<i class="fas fa-save"></i> Simpan Transaksi');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
