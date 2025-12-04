@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Transaksi</h5>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Plat Nomor</th>
                            <th>Tipe Kendaraan</th>
                            <th>Nomor WO</th>
                            <th>Service Advisor</th>
                            <th>Pekerjaan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $key => $transaction)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ date('d-m-Y H:i:s', strtotime($transaction->created_at)) }}</td>
                                <td>{{ $transaction->customer->plat_nomor }}</td>
                                <td>{{ $transaction->customer->tipe_kendaraan }}</td>
                                <td>{{ $transaction->customer->no_wo }}</td>
                                <td>{{ $transaction->serviceAdvisor->nama_service_advisor }}</td>
                                <td>
                                    @foreach ($transaction->details as $detail)
                                        <span class="badge bg-secondary mb-1"
                                            style="color: #fff;">{{ $detail->service->nama_layanan }}</span><br>
                                    @endforeach
                                </td>
                                <td>{{ formatRupiah($transaction->total) }}</td>
                                <td>
                                    @if ($transaction->status == 'proses')
                                        <span class="badge badge-info">Dalam Proses</span>
                                    @elseif ($transaction->status == 'batal')
                                        <span class="badge badge-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge badge-success">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($transaction->status != 'batal')
                                        <a href="{{ route('transactions.show', $transaction->id) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    @endif
                                    @if ($transaction->status == 'proses')
                                        <a href="{{ route('transactions.edit', $transaction->id) }}"
                                            class="btn btn-sm btn-success">
                                            <i class="fas fa-edit"></i> Selesai
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                            data-target="#deleteModal{{ $transaction->id }}">
                                            <i class="fas fa-trash"></i> Batal
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
    <!-- Modal untuk Batal Transaksi -->
    @foreach ($transactions as $transaction)
        <div class="modal fade" id="deleteModal{{ $transaction->id }}" tabindex="-1" role="dialog"
            aria-labelledby="deleteModalLabel{{ $transaction->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $transaction->id }}">Batalkan Transaksi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="cancelForm{{ $transaction->id }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

                            <div class="form-group">
                                <label for="alasan{{ $transaction->id }}">Alasan Pembatalan:</label>
                                <textarea class="form-control" id="alasan{{ $transaction->id }}" name="alasan" rows="3" required
                                    placeholder="Masukkan alasan pembatalan transaksi"></textarea>
                                <div class="invalid-feedback" id="alasanError{{ $transaction->id }}"></div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Transaksi #{{ $transaction->customer->plat_nomor }} akan dibatalkan. Transaksi yang sudah
                                dibatalkan tidak dapat dikembalikan.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-danger" id="submitBtn{{ $transaction->id }}">
                                <i class="fas fa-trash"></i> Batalkan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [
                    [5, "asc"]
                ] // Sort by order column
            });
        });
        $(document).ready(function() {
            $('form[id^="cancelForm"]').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const transactionId = form.find('input[name="transaction_id"]').val();
                const submitBtn = form.find('button[type="submit"]');
                const modal = $(`#deleteModal${transactionId}`);

                // Reset error messages
                form.find('.is-invalid').removeClass('is-invalid');
                form.find('.invalid-feedback').text('');

                // Show loading state
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

                // Prepare the data to be sent
                const formData = {
                    alasan: document.getElementById(`alasan${transactionId}`).value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                fetch(`/transactions/${transactionId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            // Handle non-200 responses
                            return response.json().then(data => {
                                throw new Error(data.message || 'An error occurred');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });

                        modal.modal('hide');

                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message,
                        });
                    })
                    .finally(() => {
                        submitBtn.prop('disabled', false);
                    });
            });

        });
    </script>
@endpush
