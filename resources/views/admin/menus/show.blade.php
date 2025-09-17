@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Menu Details: {{ $menu->name }}</h6>
            <a href="{{ route('menus.assign-role-form', $menu->id) }}" class="btn btn-success btn-sm">
                <i class="fas fa-user-plus"></i> Assign Role
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Menu Information</h5>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8">{{ $menu->name }}</dd>

                        <dt class="col-sm-4">Icon:</dt>
                        <dd class="col-sm-8"><i class="{{ $menu->icon }}"></i> {{ $menu->icon }}</dd>

                        <dt class="col-sm-4">Route:</dt>
                        <dd class="col-sm-8">{{ $menu->route ?? 'N/A' }}</dd>

                        <dt class="col-sm-4">Parent:</dt>
                        <dd class="col-sm-8">{{ $menu->parent->name ?? 'Root' }}</dd>

                        <dt class="col-sm-4">Order:</dt>
                        <dd class="col-sm-8">{{ $menu->order }}</dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $menu->is_active ? 'success' : 'danger' }}">
                                {{ $menu->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Created At:</dt>
                        <dd class="col-sm-8">{{ $menu->created_at->format('Y-m-d H:i') }}</dd>

                        <dt class="col-sm-4">Updated At:</dt>
                        <dd class="col-sm-8">{{ $menu->updated_at->format('Y-m-d H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assigned Roles ({{ $menu->roles->count() }})</h6>
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
                                @forelse($menu->roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}</td>
                                        <td>{{ $role->description ?? 'N/A' }}</td>
                                        <td>
                                            <form
                                                action="{{ route('menus.remove-role', ['menuId' => $menu->id, 'roleId' => $role->id]) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to remove this role from the menu?')">
                                                    <i class="fas fa-user-minus"></i> Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No roles assigned to this menu</td>
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
