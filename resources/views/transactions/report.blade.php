@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Daftar Transaksi</h5>
        </div>

        <div class="card-body">
            <!-- Filter Section -->
            <div class="filter-section mb-4">
                <form action="{{ route('transactions.report') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="text" class="form-control" id="start_date" name="start_date"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="text" class="form-control" id="end_date" name="end_date"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select form-control" id="status" name="status">
                                <option value="">Semua Status</option>
                                <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Dalam
                                    Proses</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai
                                </option>
                                <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Dibatalkan
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row align-items-end mt-2">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Terapkan Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            @if (request()->has('start_date') || request()->has('status'))
                                <a href="{{ route('transactions.report') }}" class="btn btn-secondary mt-2">
                                    <i class="fas fa-sync me-1"></i> Reset Filter
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable">
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $key => $transaction)
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
                                        <span class="badge bg-info">Dalam Proses</span>
                                    @elseif ($transaction->status == 'batal')
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data transaksi
                                    @if (request()->has('start_date') || request()->has('status'))
                                        dengan filter yang dipilih
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if ($transactions->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari
                            {{ $transactions->total() }} hasil
                        </div>
                        <nav>
                            {{ $transactions->links() }}
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $('#start_date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
        $('#end_date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-success btn-sm mr-2',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        title: 'Daftar Transaksi'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        title: 'Daftar Transaksi'
                    }
                ],
                pageLength: 10,
                responsive: true,
                ordering: true,
                order: [
                    [1, 'desc']
                ] // Default urutkan berdasarkan tanggal terbaru
            });

            // Set nilai default tanggal jika tidak ada filter
            @if (!request()->has('start_date'))
                const today = new Date();
                const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

                document.getElementById('start_date').valueAsDate = firstDayOfMonth;
                document.getElementById('end_date').valueAsDate = lastDayOfMonth;
            @endif
        });
    </script>
@endpush
