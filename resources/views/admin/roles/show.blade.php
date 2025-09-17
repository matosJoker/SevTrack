@extends('layouts.app')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Role Details: {{ $role->name }}</h6>
        <a href="{{ route('roles.assign-user-form', $role->id) }}" class="btn btn-success btn-sm">
            <i class="fas fa-user-plus"></i> Assign User
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Role Information</h5>
                <p><strong>Name:</strong> {{ $role->name }}</p>
                <p><strong>Description:</strong> {{ $role->description ?? 'N/A' }}</p>
                <p><strong>Created At:</strong> {{ $role->created_at->format('Y-m-d H:i') }}</p>
                <p><strong>Updated At:</strong> {{ $role->updated_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Assigned Users ({{ $role->users->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($role->users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <form action="{{ route('roles.remove-user', ['roleId' => $role->id, 'userId' => $user->id]) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this user from the role?')">
                                            <i class="fas fa-user-minus"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No users assigned to this role</td>
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