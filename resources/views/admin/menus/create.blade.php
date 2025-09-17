@extends('layouts.app')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Create New Menu</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('menus.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Menu Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="icon">Icon (Font Awesome)</label>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon"
                        name="icon" value="{{ old('icon') }}" placeholder="fas fa-icon">
                    @error('icon')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="route">Route Name</label>
                    <input type="text" class="form-control @error('route') is-invalid @enderror" id="route"
                        name="route" value="{{ old('route') }}">
                    @error('route')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="parent_id">Parent Menu</label>
                    <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                        <option value="">-- Root Menu --</option>
                        @foreach ($parentMenus as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="order">Order</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order"
                        name="order" value="{{ old('order', 0) }}" required>
                    @error('order')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('menus.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
