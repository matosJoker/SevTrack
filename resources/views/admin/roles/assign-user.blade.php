@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Assign User to Role: {{ $role->name }}</h6>
        </div>
        <div class="card-body">
            @if ($users->isEmpty())
                <div class="alert alert-info">
                    All users are already assigned to this role.
                </div>
            @else
                <form action="{{ route('roles.assign-user', $role->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="user_id">Select User</label>
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">-- Select User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Assign User</button>
                    <a href="{{ route('roles.show', $role->id) }}" class="btn btn-secondary">Cancel</a>
                </form>
            @endif
        </div>
    </div>
@endsection
