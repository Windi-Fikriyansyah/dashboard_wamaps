@extends('template.app')

@section('content')
    <div class="col-md-12">
        <div class="px-md-4 max-w-6xl mx-auto mb-5">
            <!-- Header Section -->
            <div
                class="d-flex flex-column flex-md-row gap-4 align-items-start align-items-md-center justify-content-between border-bottom pb-5 mb-5">
                <div class="max-w-2xl">
                    <div
                        class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-label-primary rounded-pill text-xs fw-bold text-uppercase tracking-wider mb-3">
                        <i class="bx bx-extension"></i>
                        Pusat Ekstensi Wamaps
                    </div>
                    <h1 class="display-4 fw-black tracking-tight text-dark mb-2">
                        Lengkapi <span class="text-primary">Alat Anda</span>
                    </h1>
                    <p class="fs-5 text-muted mb-0">
                        Unduh ekstensi Chrome resmi kami untuk mengotomatiskan alur kerja Anda. Install versi ZIP terbaru
                        untuk fungsionalitas penuh.
                    </p>
                </div>
            </div>

            <!-- Extension Cards Grid -->
            <div class="row g-4 mb-5">
                <!-- Scrape Maps Card -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-lg position-relative overflow-hidden group"
                        style="border-radius: 2.5rem; background: linear-gradient(135deg, #696cff 0%, #3f42ff 100%); color: white;">
                        <div class="card-body p-5 d-flex flex-column justify-content-between position-relative z-1">
                            <div class="mb-4">
                                <div class="backdrop-blur rounded-4 d-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px; background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <i class="bx bxl-chrome fs-2"></i>
                                </div>
                                <h2 class="h2 fw-black mb-3">Maps Scraper</h2>
                                <p class="text-white text-opacity-80 mb-4 fs-6 leading-relaxed">
                                    Ekstrak data bisnis dari Google Maps secara otomatis dan ekspor langsung ke sistem lead
                                    Anda.
                                </p>

                                <div class="d-flex flex-column gap-3 mb-5">
                                    @foreach(["Auto-Scrape Data Google Maps", "Export to Excel/Leads", "Unlimited"] as $feature)
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-opacity-10"
                                                style="width: 24px; height: 24px; background-color: rgba(255, 255, 255, 0.2);">
                                                <i class="bx bx-check small"></i>
                                            </div>
                                            <span class="fw-medium small">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-auto">
                                <a href="/extensions/scrapemapswamaps.zip" download="extension-scraper-maps.zip"
                                    class="btn btn-white text-primary w-100 py-3 fw-black rounded-4 shadow-sm transition-transform hover-scale">
                                    <i class="bx bx-download me-2"></i>
                                    Download Scraper Maps
                                </a>
                            </div>
                        </div>
                        <!-- Decorative background icon -->
                        <i class="bx bx-map-pin position-absolute top-0 end-0 text-white opacity-10"
                            style="font-size: 15rem; transform: translate(30%, -20%) rotate(15deg);"></i>
                    </div>
                </div>

                <!-- Broadcast / WhatsApp Card -->
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-lg position-relative overflow-hidden group"
                        style="border-radius: 2.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                        <div class="card-body p-5 d-flex flex-column justify-content-between position-relative z-1">
                            <div class="mb-4">
                                <div class="backdrop-blur rounded-4 d-flex align-items-center justify-content-center mb-4"
                                    style="width: 60px; height: 60px; background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <i class="bx bx-message-square-dots fs-2"></i>
                                </div>
                                <h2 class="h2 fw-black mb-3">Broadcast Sender</h2>
                                <p class="text-white text-opacity-80 mb-4 fs-6 leading-relaxed">
                                    Kirim pesan WhatsApp massal secara otomatis dengan aman dan efisien langsung dari
                                    browser Anda.
                                </p>

                                <div class="d-flex flex-column gap-3 mb-5">
                                    @foreach(["Atur Delay", "Kirim Gambar", "Unlimited"] as $feature)
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-opacity-10"
                                                style="width: 24px; height: 24px; background-color: rgba(255, 255, 255, 0.2);">
                                                <i class="bx bx-check small"></i>
                                            </div>
                                            <span class="fw-medium small">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-auto">
                                <a href="/extensions/extensionwhatsapp.zip" download="extension-broadcast-wa.zip"
                                    class="btn btn-white text-success w-100 py-3 fw-black rounded-4 shadow-sm transition-transform hover-scale">
                                    <i class="bx bx-download me-2"></i>
                                    Download Broadcast Extension
                                </a>
                            </div>
                        </div>
                        <!-- Decorative background icon -->
                        <i class="bx bxl-whatsapp position-absolute top-0 end-0 text-white opacity-10"
                            style="font-size: 15rem; transform: translate(30%, -20%) rotate(-15deg);"></i>
                    </div>
                </div>
            </div>

            <!-- Installation Guide Section -->
            <div class="bg-light rounded-5 p-4 p-md-5 mt-5 border shadow-sm" style="border-radius: 2.5rem !important;">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <h3 class="h3 fw-black text-dark d-flex align-items-center gap-3 mb-4">
                            Panduan Instalasi <i class="bx bx-right-arrow-alt text-primary fs-2"></i>
                        </h3>
                        <p class="text-muted mb-4">
                            Ikuti langkah-langkah sederhana di samping untuk mengaktifkan ekstensi di Google Chrome Anda.
                        </p>
                        <div class="p-4 bg-label-primary rounded-4 border">
                            <p class="small text-primary fw-medium mb-0">
                                <b class="d-block mb-1">Note:</b> Pastikan untuk mengekstrak file ZIP terlebih dahulu
                                sebelum dimuat ke dalam Chrome.
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="row g-4">
                            @php
                                $guide = [
                                    ['step' => '1', 'title' => 'Ekstrak ZIP', 'desc' => "Klik kanan file ZIP yang didownload dan pilih 'Extract All' ke folder tujuan."],
                                    ['step' => '2', 'title' => 'Buka chrome://extensions', 'desc' => 'Ketik URL tersebut di address bar Chrome Anda dan tekan Enter.'],
                                    ['step' => '3', 'title' => 'Aktifkan Developer Mode', 'desc' => "Cari switch 'Developer mode' di pojok kanan atas dan nyalakan."],
                                    ['step' => '4', 'title' => 'Load Unpacked', 'desc' => 'Klik tombol "Load unpacked" dan pilih folder hasil ekstrak tadi.']
                                ];
                            @endphp

                            @foreach($guide as $item)
                                <div class="col-md-6">
                                    <div class="d-flex gap-4">
                                        <div class="bg-white rounded-3 border shadow-sm text-dark fw-black d-flex align-items-center justify-content-center shrink-0"
                                            style="width: 45px; height: 45px; flex: 0 0 45px;">
                                            {{ $item['step'] }}
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1">{{ $item['title'] }}</h5>
                                            <p class="small text-muted mb-0">{{ $item['desc'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
            transform: scale(1.02);
            transition: transform 0.2s ease;
        }

        .backdrop-blur {
            backdrop-filter: blur(8px);
        }
    </style>
@endsection