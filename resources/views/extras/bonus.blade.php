@extends('template.app')

@section('content')
<div class="col-md-12">
    <div class="px-md-4 max-w-5xl mx-auto mb-5">
        <div class="d-flex flex-column gap-2 mb-4">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-label-primary w-fit small fw-bold mb-3 border">
                <i class="bx bx-share-alt"></i>
                <span>Bonus Eksklusif VIP</span>
            </div>
            <h1 class="display-5 fw-black tracking-tight mb-2">
                Chrome Extension <span class="text-primary bg-clip-text text-transparent bg-gradient-to-r from-primary to-info">FB Auto Post</span>
            </h1>
            <p class="text-muted fs-5 mt-2 max-w-3xl">
                Tingkatkan jangkauan promosi Anda dengan ekstensi browser Auto Post Grup Facebook. Jadwalkan dan kirim postingan promosi ke ratusan grup secara otomatis hanya dengan sekali klik.
            </p>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 position-relative group" style="border-radius: 25px !important;">
            <div class="card-body p-4 p-md-5 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="p-3 bg-primary text-white rounded-4 shadow-lg" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                <i class="bx bxl-facebook-circle fs-1"></i>
                            </div>
                            <div>
                                <h2 class="h3 fw-bold mb-1">FB Group Auto Poster</h2>
                                <p class="text-muted mb-0">Ekstensi Premium • Mendukung Google Chrome & Edge</p>
                            </div>
                        </div>

                        <p class="text-muted fs-6 leading-relaxed max-w-2xl mb-4">
                            Ekstensi ini adalah tool tambahan yang wajib dimiliki untuk pemasaran digital Anda. Tidak perlu lagi capek copy-paste memposting satu per satu ke grup Facebook. Biarkan sistem yang bekerja menyebarkan konten promosi, tautan, atau penawaran Anda ke ratusan target grup secara otomatis dan efisien.
                        </p>

                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <a 
                                href="/extensions/fb-autopost-extension.zip" 
                                download="fb-autopost-extension.zip"
                                class="btn btn-primary btn-lg px-4 py-3 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2"
                            >
                                <i class="bx bx-download fs-4"></i>
                                Download Ekstensi (.zip)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Decorative circle -->
            <div class="position-absolute top-0 end-0 bg-primary opacity-10 rounded-circle" style="width: 300px; height: 300px; margin-top: -150px; margin-right: -150px; filter: blur(60px);"></div>
        </div>

        <div class="mt-5">
            <h3 class="fw-bold d-flex align-items-center gap-2 mb-4">
                <i class="bx bx-check-circle text-success fs-2"></i>
                Cara Pemasangan Ekstensi
            </h3>
            
            <div class="row g-4">
                @php
                    $steps = [
                        [
                            'step' => 1,
                            'title' => 'Download File',
                            'desc' => 'Unduh file ekstensi berbentuk .zip melalui tombol download biru yang tersedia di atas.'
                        ],
                        [
                            'step' => 2,
                            'title' => 'Ekstrak Folder',
                            'desc' => 'Klik kanan pada file .zip yang diunduh, lalu pilih "Extract Here" atau ekstrak ke sebuah folder.'
                        ],
                        [
                            'step' => 3,
                            'title' => 'Developer Mode',
                            'desc' => 'Buka browser Chrome, akses url chrome://extensions/ pada address bar lalu aktifkan tombol "Developer Mode" di pojok kanan atas.'
                        ],
                        [
                            'step' => 4,
                            'title' => 'Load Unpacked',
                            'desc' => 'Klik tombol "Load unpacked" yang muncul, kemudian pilih folder yang sudah Anda ekstrak pada langkah ke-2.'
                        ]
                    ];
                @endphp

                @foreach($steps as $item)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-none bg-light rounded-4 transition-hover">
                        <div class="card-body p-4">
                            <div class="w-12 h-12 rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold fs-5 mb-3 shadow-sm border" style="width: 45px; height: 45px;">
                                {{ $item['step'] }}
                            </div>
                            <h5 class="fw-bold mb-2">{{ $item['title'] }}</h5>
                            <p class="small text-muted mb-0 leading-relaxed">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .rounded-4 { border-radius: 1.5rem !important; }
    .fw-black { font-weight: 900; }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        transition: all 0.3s ease;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #696cff 0%, #03c3ec 100%);
    }
</style>
@endsection
