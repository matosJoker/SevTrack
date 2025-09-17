@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Permission Details: {{ $permission->name }}</h6>
            <a href="{{ route('permissions.assign-role-form', $permission->id) }}" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus"></i> Assign Role
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Permission Information</h5>
                    <p><strong>Name:</strong> {{ $permission->name }}</p>
                    <p><strong>Description:</strong> {{ $permission->description ?? 'N/A' }}</p>
                    <p><strong>Created At:</strong> {{ $permission->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>Updated At:</strong> {{ $permission->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assigned Roles ({{ $permission->roles->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permission->roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->description ?? 'N/A' }}</td>
                                        <td>
                                            <form
                                                action="{{ route('permissions.remove-role', ['permissionId' => $permission->id, 'roleId' => $role->id]) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to remove this role from the permission?')">
                                                    <i class="fas fa-user-minus"></i> Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No roles assigned to this permission</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
