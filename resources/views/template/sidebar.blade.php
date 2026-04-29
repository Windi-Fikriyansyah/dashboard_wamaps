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

    <li class="menu-item {{ request()->routeIs('leads.index') ? 'active' : '' }}">
        <a href="{{route('leads.index')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-search"></i>
            <div data-i18n="Search Leads">Search Leads</div>
        </a>
    </li>

    <li class="menu-item {{ request()->routeIs('leads.data') ? 'active' : '' }}">
        <a href="{{route('leads.data')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-data"></i>
            <div data-i18n="Data Leads">Data Leads</div>
        </a>
    </li>




    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Marketing</span>
    </li>

    <li class="menu-item {{ request()->routeIs('whatsapp.devices') ? 'active' : '' }}">
        <a href="{{ route('whatsapp.devices') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-devices"></i>
            <div data-i18n="Devices">WhatsApp Devices</div>
        </a>
    </li>

    <li class="menu-item {{ request()->routeIs('whatsapp.templates') ? 'active' : '' }}">
        <a href="{{ route('whatsapp.templates') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-message-square-detail"></i>
            <div data-i18n="Template Pesan">Template Pesan</div>
        </a>
    </li>

    <li class="menu-item {{ request()->routeIs('whatsapp.broadcast') ? 'active' : '' }}">
        <a href="{{ route('whatsapp.broadcast') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-send"></i>
            <div data-i18n="Broadcast">WA Broadcast</div>
        </a>
    </li>


    <li class="menu-item">
        <a href="" class="menu-link">
            <i class="menu-icon tf-icons bx bx-send"></i>
            <div data-i18n="Data Leads">Email Broadcast (Incoming)</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Bonus & Software</span>
    </li>

    <li class="menu-item {{ request()->routeIs('bonus') ? 'active' : '' }}">
        <a href="{{ route('bonus') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-gift"></i>
            <div data-i18n="Bonus">Bonus VIP</div>
        </a>
    </li>

    <li class="menu-item {{ request()->routeIs('extension') ? 'active' : '' }}">
        <a href="{{ route('extension') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-extension"></i>
            <div data-i18n="Extension">Versi Extension</div>
        </a>
    </li>

    <li class="menu-item {{ request()->routeIs('software') ? 'active' : '' }}">
        <a href="{{ route('software') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-desktop"></i>
            <div data-i18n="Software">Versi Software</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Kemitraan</span>
    </li>

    <li class="menu-item {{ request()->routeIs('affiliate') ? 'active' : '' }}">
        <a href="{{ route('affiliate') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-group"></i>
            <div data-i18n="Afiliasi">Afiliasi</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Pengaturan</span>
    </li>

    <li class="menu-item {{ request()->is('settings*') ? 'active' : '' }}">
        <a href="{{ route('settings.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div data-i18n="Settings">Pengaturan Akun</div>
        </a>
    </li>

</ul>