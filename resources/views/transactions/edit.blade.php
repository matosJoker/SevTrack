@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Transaksi</h5>
            <div>
                <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form id="updateTransactionForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Informasi Kendaraan</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td>Plat Nomor</td>
                                <td>: {{ $transaction->customer->plat_nomor }}</td>
                            </tr>
                            <tr>
                                <td width="30%">Tipe Kendaraan</td>
                                <td>: {{ $transaction->customer->tipe_kendaraan }}</td>
                            </tr>
                            <tr>
                                <td>No Wo</td>
                                <td>: {{ $transaction->customer->no_wo }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Informasi Transaksi</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%">Tanggal</td>
                                <td>: {{ date('d-m-Y H:i:s', strtotime($transaction->created_at)) }}</td>
                            </tr>
                            <tr>
                                <td>Service Advisor</td>
                                <td>: {{ $transaction->serviceAdvisor->nama_service_advisor }}</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td>: {{ formatRupiah($transaction->total) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <input type="hidden" name="status" value="selesai">
                </div>

                <h6 class="font-weight-bold">Detail Pekerjaan</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Pekerjaan</th>
                                <th>Harga</th>
                                <th>Foto Sebelum</th>
                                <th>Foto Sesudah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->details as $key => $detail)
                                <tr class="service-row">
                                    <td>{{ $detail->service->nama_layanan }}</td>
                                    <td>{{ formatRupiah($detail->harga) }}</td>
                                    <td>
                                        @if ($detail->foto_sebelum)
                                            <a href="{{ asset($detail->foto_sebelum) }}" target="_blank">
                                                <img src="{{ asset($detail->foto_sebelum) }}" alt="Foto Sebelum"
                                                    class="img-thumbnail" style="max-width: 100px;">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <label class="add-photo-btn text-success" title="Tambah Foto Sesudah Service">
                                                <i class="fas fa-camera fa-lg"> </i> Foto
                                                <input type="file" class="d-none service-photo-input"
                                                    name="services[{{ $key }}][foto_layanan][]" multiple
                                                    accept="image/*">
                                            </label>
                                        </div>
                                        <div class="col-12 mt-2 service-photos-container"
                                            id="service-photos-{{ $key }}">
                                            @if ($detail->foto_sesudah)
                                                @php
                                                    $fotoSesudah = json_decode($detail->foto_sesudah, true);
                                                @endphp
                                                @if (is_array($fotoSesudah))
                                                    @foreach ($fotoSesudah as $foto)
                                                        <div class="service-photo-container">
                                                            <img src="{{ asset($foto) }}" class="service-photo-preview"
                                                                alt="Foto Sesudah">
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi SweetAlert2
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Handle form submit dengan AJAX
            $('#updateTransactionForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = new FormData(this);
                const submitBtn = $('#submitBtn');

                // Disable button saat proses
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
                        $.ajax({
                            url: '{{ route('transactions.update', $transaction->id) }}',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        showConfirmButton: true
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href =
                                                '{{ route('transactions.show', $transaction->id) }}';
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    'Terjadi kesalahan saat memproses permintaan.';

                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }

                                    if (xhr.responseJSON.errors) {
                                        let errorList = '';
                                        $.each(xhr.responseJSON.errors, function(key,
                                            value) {
                                            errorList += `<li>${value}</li>`;
                                        });

                                        if (errorList) {
                                            errorMessage += `<ul>${errorList}</ul>`;
                                        }
                                    }
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    html: errorMessage
                                });
                            },
                            complete: function() {
                                // Enable button kembali
                                submitBtn.prop('disabled', false).html(
                                    '<i class="fas fa-save"></i> Simpan Perubahan');
                            }
                        });
                    }
                })

            });

            // Preview foto yang diupload
            $(document).on('change', '.service-photo-input', function() {
                const files = this.files;
                const serviceIndex = $(this).closest('.service-row').index();
                const previewContainer = $(this).closest('td').find('.service-photos-container');

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
                                    const input = $(this).closest('tr').find(
                                        'input.service-photo-input')[0];

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
        });
    </script>
@endpush
