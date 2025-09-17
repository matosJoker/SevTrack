@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assign Role to Permission: {{ $permission->name }}</h6>
        </div>
        <div class="card-body">
            @if ($roles->isEmpty())
                <div class="alert alert-info">
                    All roles are already assigned to this permission.
                </div>
            @else
                <form action="{{ route('permissions.assign-role', $permission->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="role_id">Select Role</label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <option value="">-- Select Role --</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}
                                    ({{ $role->description ?? 'No description' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign Role</button>
                    <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-secondary">Cancel</a>
                </form>
            @endif
        </div>
    </div>
@endsection
