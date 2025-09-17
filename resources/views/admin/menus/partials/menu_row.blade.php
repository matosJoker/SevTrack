<tr>
    <td>
        @for ($i = 0; $i < $level; $i++)
            &nbsp;&nbsp;&nbsp;&nbsp; <!-- Indentation -->
        @endfor
        @if ($level > 0)
            <i class="fas fa-level-down-alt"></i> <!-- Child indicator icon -->
        @endif
        {{ $menu->name }}
    </td>
    <td><i class="{{ $menu->icon }}"></i> {{ $menu->icon }}</td>
    <td>{{ $menu->route ?? 'N/A' }}</td>
    <td>{{ $menu->parent->name ?? 'Root' }}</td>
    <td>
        @if ($menu->children->count() > 0)
            <span class="badge badge-info">{{ $menu->children->count() }} children</span>
        @else
            <span class="text-muted">No children</span>
        @endif
    </td>
    <td>{{ $menu->order }}</td>
    <td>
        <span class="badge badge-{{ $menu->is_active ? 'success' : 'danger' }}">
            {{ $menu->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>
        <a href="{{ route('menus.show', $menu->id) }}" class="btn btn-info btn-sm">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-warning btn-sm">
            <i class="fas fa-edit"></i>
        </a>
        <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>

@foreach ($menu->children as $child)
    @include('admin.menus.partials.menu_row', ['menu' => $child, 'level' => $level + 1])
@endforeach
