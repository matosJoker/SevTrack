@extends('layouts.app')

@section('content')
    <style>
        .badge {
            font-size: 0.85em;
            padding: 0.35em 0.65em;
            border-radius: 4px;
            font-weight: 500;
            color: #fff;
        }

        .filter-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        .card-header {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
            border-bottom: 0;
            padding: 1rem 1.5rem;
        }
    </style>
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Daftar Data Pelanggan</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Plat Nomor</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelanggan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->plat_nomor }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->email }}</td>
                                <td>{{ $item->no_telp }}</td>
                            </tr>
                        @endforeach
                        @if (count($pelanggan) == 0)
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-exclamation-circle me-2"></i>Tidak ada data pelanggan
                                    @if (request()->has('id_bengkel'))
                                        dengan filter yang dipilih
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <!-- Pagination -->
                {{-- @if ($pelanggan->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Menampilkan {{ $pelanggan->firstItem() }} - {{ $pelanggan->lastItem() }} dari
                            {{ $pelanggan->total() }} hasil
                        </div>
                        <nav>
                            {{ $pelanggan->links() }}
                        </nav>
                    </div>
                @endif --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            $('#dataTable').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-success btn-sm mr-2',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        title: 'Daftar Data Pelanggan'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-primary btn-sm',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        title: 'Daftar Data Pelanggan'
                    }
                ],
                pageLength: 10,
                responsive: true,
                ordering: true,
                order: [
                    [1, 'asc']
                ] // Default urutkan berdasarkan tanggal terbaru
            });
        });
    </script>
@endpush
