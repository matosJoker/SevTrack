<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/logo.png') }}" alt="logo" class="img-fluid rounded-circle" style="height: 50px; width: 50px; object-fit: cover;">
        </div>
        <div class="sidebar-brand-text mx-3">SevTrack</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    {{-- <li class="nav-item active">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li> --}}

    @isset($menus)
        @foreach ($menus as $menu)
            @php
                $isActive = false;
                $childRoutes = $menu->children->pluck('route')->filter()->toArray();

                // Cek apakah route saat ini match dengan menu atau children
                if ($menu->route && request()->is(trim($menu->route, '/') . '*')) {
                    $isActive = true;
                } elseif (count($childRoutes) > 0) {
                    foreach ($childRoutes as $route) {
                        if (request()->is(trim($route, '/') . '*')) {
                            $isActive = true;
                            break;
                        }
                    }
                }
            @endphp

            <li class="nav-item {{ $isActive ? 'active' : '' }}">
                @if ($menu->children->isNotEmpty())
                    <a class="nav-link collapsed" href="#" data-toggle="collapse"
                        data-target="#collapse-{{ $menu->id }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                        aria-controls="collapse-{{ $menu->id }}">
                        @if ($menu->icon)
                            <i class="fas fa-fw {{ $menu->icon }}"></i>
                        @endif
                        <span>{{ $menu->name }}</span>
                    </a>
                    <div id="collapse-{{ $menu->id }}" class="collapse {{ $isActive ? 'show' : '' }}"
                        aria-labelledby="heading-{{ $menu->id }}" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            @foreach ($menu->children as $child)
                                <a class="collapse-item {{ request()->is(trim($child->route, '/') . '*') ? 'active' : '' }}"
                                    href="{{ $child->route ? route($child->route) : '#' }}">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a class="nav-link" href="{{ $menu->route ? route($menu->route) : '#' }}">
                        @if ($menu->icon)
                            <i class="fas fa-fw {{ $menu->icon }}"></i>
                        @endif
                        <span>{{ $menu->name }}</span>
                    </a>
                @endif
            </li>
        @endforeach
    @endisset


    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
