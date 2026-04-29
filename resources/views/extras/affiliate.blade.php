@extends('template.app')

@section('content')
    <div class="col-md-12">
        <div class="px-md-4 max-w-5xl mx-auto mb-5">
            <div class="d-flex flex-column gap-2 mb-4">
                <div
                    class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-label-primary w-fit small fw-bold mb-3 border">
                    <i class="bx bx-money"></i>
                    <span>Program Afiliasi</span>
                </div>
                <h1 class="display-5 fw-black tracking-tight mb-2">
                    Jadilah <span
                        class="text-primary bg-clip-text text-transparent bg-gradient-to-r from-primary to-info">Affiliate
                        Kami</span>
                </h1>
                <p class="text-muted fs-5 mt-2 max-w-3xl">
                    Rekomendasikan layanan kami dan dapatkan komisi sebesar <strong>20%</strong> untuk setiap penjualan yang
                    berhasil melalui
                    link referral Anda.
                </p>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 position-relative group"
                style="border-radius: 25px !important;">
                <div class="card-body p-4 p-md-5 position-relative z-1">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center gap-4 mb-4">
                                <div class="p-3 bg-primary text-white rounded-4 shadow-lg"
                                    style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bx bx-group fs-1"></i>
                                </div>
                                <div>
                                    <h2 class="h3 fw-bold mb-1">Keuntungan Menjadi Affiliate</h2>
                                    <p class="text-muted mb-0">Komisi 20% • Pembayaran Cepat • Support Prioritas</p>
                                </div>
                            </div>

                            <p class="text-muted fs-6 leading-relaxed max-w-2xl mb-4">
                                Bergabung dengan program afiliasi kami sangat mudah. Cukup promosikan layanan kami
                                menggunakan link unik Anda, dan dapatkan komisi untuk setiap pelanggan baru yang membeli.
                                Sistem kami transparan dan menguntungkan.
                            </p>

                            <div class="d-flex flex-column flex-sm-row gap-3">
                                <a href="https://wa.me/6289678386070?text=Halo%20Admin,%20saya%20ingin%20mengajukan%20diri%20menjadi%20affiliate"
                                    target="_blank"
                                    class="btn btn-primary btn-lg px-4 py-3 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="bx bxl-whatsapp fs-4"></i>
                                    Ajukan Sekarang (via WA)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="position-absolute top-0 end-0 bg-primary opacity-10 rounded-circle"
                    style="width: 300px; height: 300px; margin-top: -150px; margin-right: -150px; filter: blur(60px);">
                </div>
            </div>

            <div class="mt-5">
                <h3 class="fw-bold d-flex align-items-center gap-2 mb-4">
                    <i class="bx bx-check-circle text-success fs-2"></i>
                    Cara Kerja Afiliasi
                </h3>

                <div class="row g-4">
                    @php
                        $steps = [
                            [
                                'step' => 1,
                                'title' => 'Daftar Afiliasi',
                                'desc' => 'Ajukan diri Anda melalui tombol WhatsApp di atas. Tim kami akan meninjau dan merespons secepatnya.'
                            ],
                            [
                                'step' => 2,
                                'title' => 'Dapatkan Link Unik',
                                'desc' => 'Setelah disetujui, Anda akan mendapatkan link referral khusus untuk mempromosikan layanan kami.'
                            ],
                            [
                                'step' => 3,
                                'title' => 'Promosikan',
                                'desc' => 'Bagikan link tersebut di media sosial, blog, website, atau ke jaringan teman-teman Anda.'
                            ],
                            [
                                'step' => 4,
                                'title' => 'Dapatkan Komisi 20%',
                                'desc' => 'Terima komisi sebesar 20% untuk setiap pembelian yang berhasil berasal dari link referral Anda.'
                            ]
                        ];
                    @endphp

                    @foreach($steps as $item)
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border-0 shadow-none bg-light rounded-4 transition-hover">
                                <div class="card-body p-4">
                                    <div class="w-12 h-12 rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold fs-5 mb-3 shadow-sm border"
                                        style="width: 45px; height: 45px;">
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
        .rounded-4 {
            border-radius: 1.5rem !important;
        }

        .fw-black {
            font-weight: 900;
        }

        .transition-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #696cff 0%, #03c3ec 100%);
        }
    </style>
@endsection