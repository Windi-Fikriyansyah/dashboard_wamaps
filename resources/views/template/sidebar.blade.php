<ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
        <a href="{{route('dashboard')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Menu Leads</span>
    </li>


    <li class="menu-item {{ request()->is('leads*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-layout"></i>
            <div data-i18n="Layouts">Leads</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('leads.index') ? 'active' : '' }}">
                <a href="{{route('leads.index')}}" class="menu-link">
                    <div data-i18n="Without menu">Search Leads</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('leads.data') ? 'active' : '' }}">
                <a href="{{route('leads.data')}}" class="menu-link">
                    <div data-i18n="Without navbar">Data Leads</div>
                </a>
            </li>

        </ul>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Marketing</span>
    </li>
    <li class="menu-item {{ request()->is('whatsapp*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bxl-whatsapp"></i>
            <div data-i18n="Layouts">Whatsapp</div>
        </a>

        <ul class="menu-sub">
            <li class="menu-item {{ request()->routeIs('whatsapp.devices') ? 'active' : '' }}">
                <a href="{{ route('whatsapp.devices') }}" class="menu-link">
                    <div data-i18n="Without menu">Devices</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('whatsapp.templates') ? 'active' : '' }}">
                <a href="{{ route('whatsapp.templates') }}" class="menu-link">
                    <div data-i18n="Without navbar">Template Pesan</div>
                </a>
            </li>
            <li class="menu-item {{ request()->routeIs('whatsapp.broadcast') ? 'active' : '' }}">
                <a href="{{ route('whatsapp.broadcast') }}" class="menu-link">
                    <div data-i18n="Without navbar">Broadcast</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('whatsapp.history') ? 'active' : '' }}">
                <a href="{{ route('whatsapp.history') }}" class="menu-link">
                    <div data-i18n="Without navbar">History Pesan</div>
                </a>
            </li>

        </ul>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Laporan</span>
    </li>


    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Lainnya</span>
    </li>
    <!-- Harga -->


</ul>