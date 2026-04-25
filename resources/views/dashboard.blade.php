@extends('template.app')

@section('content')
    <div class="col-md-12">
        <div class="px-md-2 max-w-6xl mx-auto mb-5">

            <!-- Header Section -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4 mb-5">
                <div>
                    <h1 class="display-6 fw-black text-dark tracking-tight d-flex align-items-center gap-3 mb-1">
                        <i class="bx bx-grid-alt text-primary fs-1"></i>
                        Dashboard
                    </h1>
                    <p class="text-muted fw-medium fs-6 mb-0">
                        Selamat datang kembali, <span class="text-primary fw-bold">{{ Auth::user()->name }}</span>! Siap
                        untuk mencari leads hari ini?
                    </p>
                </div>
                <div
                    class="d-flex align-items-center gap-2 bg-primary text-white px-4 py-2 rounded-4 shadow-lg text-sm fw-bold align-self-start align-self-md-auto">
                    <i class="bx bx-shield-quarter"></i>
                    Status: Pro Account
                </div>
            </div>

            <!-- Desktop App Announcement -->
            <div class="card border-0 shadow-lg position-relative overflow-hidden mb-5"
                style="border-radius: 2.5rem; background: linear-gradient(135deg, #696cff 0%, #3f42ff 100%); color: white;">
                <!-- Decorative circle -->
                <div class="position-absolute top-0 end-0 bg-white opacity-10 rounded-circle"
                    style="width: 300px; height: 300px; margin-top: -150px; margin-right: -150px; filter: blur(80px);">
                </div>

                <div class="card-body p-4 p-md-5 position-relative z-1">
                    <div class="row align-items-center">
                        <div
                            class="col-lg-8 d-flex flex-column flex-md-row align-items-center align-items-md-start gap-4 text-center text-md-start">
                            <div class="p-4 rounded-4 shadow-xl transition-transform hover-scale shrink-0"
                                style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background-color: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px);">
                                <i class="bx bx-desktop text-white fs-1"></i>
                            </div>
                            <div>
                                <h2 class="h2 fw-black tracking-tight text-white mb-2">Wamaps Software Tersedia!</h2>
                                <p class="text-white text-opacity-80 fw-medium max-w-md mb-0">
                                    Nikmati pengalaman scrapping yang jauh lebih stabil, Unlimited tanpa batas, dan performa
                                    maksimal langsung dari PC Anda. Semua fitur versi web tersedia lengkap di versi software
                                    dengan kecepatan yang jauh lebih ngebut!
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4 d-flex justify-content-center justify-content-lg-end mt-4 mt-lg-0">
                            <a href="{{ route('software') }}"
                                class="btn btn-white text-primary btn-lg px-5 py-3 fw-black rounded-4 shadow-lg transition-all hover-translate-y">
                                <i class="bx bx-play-circle me-2"></i>
                                Lihat Tutorial
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wamaps Info & Action Section -->
            <div class="row g-4">
                <!-- Full Width: Info -->
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm rounded-5 overflow-hidden h-100">
                        <div class="card-header bg-white p-4 p-md-5 border-bottom border-light">
                            <h2 class="h4 fw-bold text-dark d-flex align-items-center gap-3 mb-0">
                                <i class="bx bx-target-lock text-primary fs-3"></i>
                                Apa itu Wamaps?
                            </h2>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <p class="text-muted fs-6 leading-relaxed fw-medium mb-4">
                                Wamaps adalah platform **Lead Generation & WhatsApp Automation** yang dirancang khusus untuk
                                membantu bisnis mencari, mengelola, dan menghubungi calon pelanggan potensial secara
                                otomatis.
                            </p>

                            <div class="row g-3 mb-5">
                                <div class="col-sm-6">
                                    <div class="p-4 bg-light rounded-4 d-flex align-items-start gap-3 border h-100">
                                        <div class="p-2 bg-label-primary rounded-3 shrink-0">
                                            <i class="bx bx-map-pin fs-5"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold fs-6 mb-1">Target Berbasis Lokasi</h5>
                                            <p class="small text-muted mb-0 italic" style="font-size: 11px;">Cari bisnis di
                                                sekitar Anda atau di kota manapun di dunia.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="p-4 bg-light rounded-4 d-flex align-items-start gap-3 border h-100">
                                        <div class="p-2 bg-label-success rounded-3 shrink-0">
                                            <i class="bx bx-message-square-dots fs-5"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold fs-6 mb-1">Automation Chat</h5>
                                            <p class="small text-muted mb-0 italic" style="font-size: 11px;">Kirim pesan
                                                WhatsApp massal tanpa perlu simpan nomor satu-satu.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h5 class="fw-bold text-dark mb-4">Fitur Utama Anda:</h5>
                                <div class="row g-2">
                                    @php
                                        $features = [
                                            "Auto-Scrapping data dari Google Maps secara Real-time.",
                                            "Sistem Manajemen Leads yang terorganisir.",
                                            "Broadcast WhatsApp Batch dengan delay human-like.",
                                            "Template pesan yang dinamis dan personal.",
                                            "Integrasi Extension untuk workflow yang lebih cepat.",
                                            "Install Versi Software untuk fitur yang lebih canggih."
                                        ];
                                    @endphp
                                    @foreach($features as $f)
                                        <div class="col-md-6 mb-2">
                                            <div class="d-flex align-items-center gap-3 text-muted fw-medium small">
                                                <div class="bg-primary rounded-circle" style="width: 6px; height: 6px;"></div>
                                                {{ $f }}
                                            </div>
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
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }

        .hover-translate-y:hover {
            transform: translateY(-5px);
        }

        .backdrop-blur {
            backdrop-filter: blur(8px);
        }
    </style>
@endsection