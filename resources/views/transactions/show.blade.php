@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Transaksi</h5>
            <div>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                {{-- <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-warning"> --}}
                    {{-- <i class="fas fa-edit"></i> Edit --}}
                {{-- </a> --}}
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="font-weight-bold">Informasi Pelanggan</h6>
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">Nama</td>
                            <td>: {{ $transaction->customer->nama }}</td>
                        </tr>
                        <tr>
                            <td>No. Telepon</td>
                            <td>: {{ $transaction->customer->no_telp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: {{ $transaction->customer->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: {{ $transaction->customer->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Plat Nomor</td>
                            <td>: {{ $transaction->customer->plat_nomor }}</td>
                        </tr>
                        <tr>
                            <td>VIN</td>
                            <td>: {{ $transaction->customer->vin ?? '-' }}</td>
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
                        <tr>
                            <td>Status</td>
                            <td>:
                                @if ($transaction->status == 'proses')
                                    <span class="badge badge-info">Dalam Proses</span>
                                @else
                                    <span class="badge badge-success">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <h6 class="font-weight-bold">Detail Layanan</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Layanan</th>
                            <th>Harga</th>
                            <th>Keterangan</th>
                            <th>Foto Sebelum</th>
                            <th>Foto Sesudah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transaction->details as $key => $detail)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $detail->service->nama_layanan }}</td>
                                <td>{{ formatRupiah($detail->harga) }}</td>
                                <td>{{ $detail->keterangan ?? '-' }}</td>
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
                                    @if ($detail->foto_sesudah)
                                        <a href="{{ asset($detail->foto_sesudah) }}" target="_blank">
                                            <img src="{{ asset($detail->foto_sesudah) }}" alt="Foto Sesudah"
                                                class="img-thumbnail" style="max-width: 100px;">
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
