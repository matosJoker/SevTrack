@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Menus</h6>
            <a href="{{ route('menus.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Menu
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Icon</th>
                            <th>Route</th>
                            <th>Parent</th>
                            <th>Children</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menus as $menu)
                            @include('admin.menus.partials.menu_row', ['menu' => $menu, 'level' => 0])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Page level plugins -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [
                    [5, "asc"]
                ] // Sort by order column
            });
        });
    </script>
@endsection
