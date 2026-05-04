@extends('template.app')

@section('content')
    <div class="col-md-12">
        <div class="px-md-4 max-w-6xl mx-auto mb-5">
            <!-- Header -->
            <div class="mb-5">
                <h1 class="display-6 fw-black text-dark tracking-tight d-flex align-items-center gap-3">
                    <i class="bx bx-desktop text-primary fs-1"></i>
                    Wamaps Desktop Version
                </h1>
                <p class="text-muted fw-medium fs-5 mt-2">
                    Tingkatkan efisiensi lead generation Anda dengan aplikasi native desktop Wamaps. Lebih ngebut, lebih
                    aman.
                </p>
            </div>

            <div class="row g-4">
                <!-- Main Content (Left) -->
                <div class="col-lg-7">
                    <!-- Download Action Section -->
                    <div class="card border-0 shadow-lg position-relative overflow-hidden mb-4"
                        style="border-radius: 2.5rem; background: linear-gradient(135deg, #696cff 0%, #3f42ff 100%); color: white;">
                        <!-- Decorative shapes -->
                        <div class="position-absolute top-0 end-0 bg-white opacity-10 rounded-circle"
                            style="width: 250px; height: 250px; margin-top: -100px; margin-right: -100px; filter: blur(50px);">
                        </div>
                        <div class="position-absolute bottom-0 start-0 bg-black opacity-10 rounded-circle"
                            style="width: 150px; height: 150px; margin-bottom: -50px; margin-left: -50px; filter: blur(30px);">
                        </div>

                        <div class="card-body p-5 d-flex flex-column align-items-center text-center position-relative z-1">
                            <div class="p-4 rounded-4 shadow-lg mb-4"
                                style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px);">
                                <i class="bx bx-desktop text-white fs-1"></i>
                            </div>
                            <div class="mb-4">
                                <h2 class="h1 fw-black tracking-tight text-white mb-2">Wamaps App v1.3.0</h2>
                                <p class="text-white text-opacity-80 fw-medium">
                                    Pilih platform Anda dan mulai gunakan Wamaps Desktop sekarang.
                                    Lebih ngebut dan powerful.
                                </p>
                            </div>

                            <div class="d-flex flex-column flex-md-row gap-3">
                                <a href="https://tinyurl.com/243xnncc" target="_blank" rel="noopener noreferrer"
                                    class="btn btn-white text-primary btn-lg px-4 py-3 fw-black rounded-4 shadow-lg hover-scale transition-all d-flex align-items-center gap-3">
                                    <i class="bx bxl-windows fs-3"></i>
                                    Download Windows
                                </a>
                                <a href="https://tinyurl.com/2wec298k" target="_blank" rel="noopener noreferrer"
                                    class="btn btn-outline-light btn-lg px-4 py-3 fw-black rounded-4 shadow-lg hover-scale transition-all d-flex align-items-center gap-3 border-2"
                                    style="background-color: rgba(255, 255, 255, 0.1); backdrop-filter: blur(4px);">
                                    <i class="bx bxl-apple fs-3"></i>
                                    Download macOS
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tutorial Video Section -->
                    <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="p-3 bg-label-danger rounded-4">
                                    <i class="bx bx-play-circle fs-3"></i>
                                </div>
                                <h3 class="h4 fw-bold text-dark mb-0">Video Tutorial</h3>
                            </div>
                            <p class="text-muted fw-medium small mb-4">
                                Tonton panduan singkat ini untuk melihat cara instalasi dan penggunaan versi Desktop.
                            </p>

                            <div class="position-relative w-100 rounded-4 overflow-hidden shadow-inner"
                                style="aspect-ratio: 16/9; background: #000;">
                                <iframe class="w-100 h-100 position-relative z-1"
                                    src="https://www.youtube.com/embed/FLdqVpC4gvM?si=26WoEJfsKWMJ1ftu"
                                    title="Wamaps Tutorial Desktop" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Features (Right) -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-5 mb-4">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="h4 fw-bold text-dark mb-5">Kenapa Versi Desktop?</h3>

                            <div class="d-flex flex-column gap-4">
                                @php
                                    $features = [
                                        [
                                            'icon' => 'bx-zap',
                                            'title' => 'Lebih Cepat & Stabil',
                                            'desc' => 'Performa maksimal tanpa batasan memori browser Chrome.',
                                        ],
                                        [
                                            'icon' => 'bxl-linkedin',
                                            'title' => 'LinkedIn Scraper',
                                            'desc' =>
                                                'Ekstrak data profesional dan perusahaan langsung dari pencarian LinkedIn.',
                                        ],
                                        [
                                            'icon' => 'bx-globe',
                                            'title' => 'Tanpa Batas IP',
                                            'desc' =>
                                                'Scraping lebih leluasa karena dijalankan langsung dari local machine.',
                                        ],
                                        [
                                            'icon' => 'bx-shield-quarter',
                                            'title' => 'Anti-Ban Protection',
                                            'desc' => 'Sistem delay dan human-like behavior yang lebih canggih.',
                                        ],
                                        [
                                            'icon' => 'bx-terminal',
                                            'title' => 'Portable Chrome',
                                            'desc' => 'Otomatis mengelola browser session secara mandiri.',
                                        ],
                                    ];
                                @endphp

                                @foreach ($features as $f)
                                    <div class="d-flex gap-4">
                                        <div class="bg-light rounded-4 p-3 d-flex align-items-center justify-content-center h-fit shrink-0"
                                            style="width: 55px; height: 55px;">
                                            <i class="bx {{ $f['icon'] }} text-primary fs-3"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1">{{ $f['title'] }}</h5>
                                            <p class="small text-muted mb-0 leading-relaxed">{{ $f['desc'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-lg rounded-5 bg-dark text-white overflow-hidden">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="fw-bold text-light mb-4">Step Instalasi</h4>

                            <ul class="nav nav-pills mb-4 gap-2" id="installTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active btn btn-sm rounded-pill px-3 fw-bold text-white border-0"
                                        style="background: rgba(255,255,255,0.1);" id="win-tab" data-bs-toggle="pill"
                                        data-bs-target="#win-steps" type="button">
                                        <i class="bx bxl-windows me-1"></i> Windows
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link btn btn-sm rounded-pill px-3 fw-bold text-white border-0"
                                        style="background: rgba(255,255,255,0.1);" id="mac-tab" data-bs-toggle="pill"
                                        data-bs-target="#mac-steps" type="button">
                                        <i class="bx bxl-apple me-1"></i> macOS
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content" id="installTabContent">
                                <div class="tab-pane fade show active" id="win-steps" role="tabpanel">
                                    <div class="d-flex flex-column gap-3">
                                        @foreach (['Download file .zip dari link di atas', 'Ekstrak file ke folder di PC Anda (Contoh: D:/Wamaps)', 'Buka folder Wamaps dan jalankan MapsLeadScraper.exe'] as $step)
                                            <div class="d-flex gap-3 align-items-start">
                                                <i class="bx bx-check-circle text-success fs-4 shrink-0"></i>
                                                <span class="small text-white text-opacity-80">{{ $step }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="mac-steps" role="tabpanel">
                                    <div class="d-flex flex-column gap-3">
                                        @foreach (['Download file .dmg dari link di atas', 'Buka file .dmg dan drag Wamaps ke Applications', "Klik kanan aplikasi dan pilih 'Open' untuk pertama kali", "Jika muncul peringatan, pilih 'Open Anyway' di Privacy Settings"] as $step)
                                            <div class="d-flex gap-3 align-items-start">
                                                <i class="bx bx-check-circle text-info fs-4 shrink-0"></i>
                                                <span class="small text-white text-opacity-80">{{ $step }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .fw-black {
            font-weight: 900;
        }

        .rounded-4 {
            border-radius: 1.5rem !important;
        }

        .rounded-5 {
            border-radius: 2.5rem !important;
        }

        .btn-white {
            background-color: #fff;
            border-color: #fff;
        }

        .btn-white:hover {
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .hover-scale:hover {
            transform: translateY(-5px);
        }

        .backdrop-blur {
            backdrop-filter: blur(8px);
        }

        /* Tabs styling */
        .nav-pills .nav-link.active {
            background: #696cff !important;
            box-shadow: 0 4px 15px rgba(105, 108, 255, 0.4);
        }

        .nav-pills .nav-link:not(.active):hover {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
@endsection
