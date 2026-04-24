@extends('template.app')

@section('content')
<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">WhatsApp Devices</h4>
            <p class="text-muted small mb-0">Hubungkan dan kelola akun WhatsApp Anda melalui Fonnte</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
            <i class="bx bx-plus me-1"></i> Tambah Device
        </button>
    </div>

    <style>
        .swal2-container {
            z-index: 99999 !important;
        }
    </style>

    @if($devices->isEmpty())
    <div class="card p-5 text-center shadow-none border-0 align-items-center" style="background-color: #f8f9fa; border-radius: 20px;">
        <div class="mb-3">
            <div class="bg-white d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 80px; height: 80px;">
                <i class="bx bx-mobile-alt text-primary" style="font-size: 40px;"></i>
            </div>
        </div>
        <h5 class="fw-bold">Belum ada Device</h5>
        <p class="text-muted mx-auto" style="max-width: 400px;">Anda belum menambahkan perangkat WhatsApp. Tambahkan perangkat untuk mulai mengirim pesan otomatis.</p>
        <button class="btn btn-outline-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addDeviceModal">Tambah Device</button>
    </div>
    @else
    <div class="row">
        @foreach($devices as $device)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0" style="border-radius: 20px; transition: transform 0.2s;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center {{ $device->status == 'connected' ? 'bg-label-success' : 'bg-label-danger' }}" style="width: 50px; height: 50px;">
                                <i class="bx bx-mobile-alt fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-truncate" style="max-width: 150px;">{{ $device->name }}</h6>
                                <p class="text-muted small mb-0 font-monospace">{{ $device->device_number }}</p>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteDevice({{ $device->id }})">
                                    <i class="bx bx-trash me-1"></i> Hapus Device
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        @if($device->status == 'connected')
                        <span class="badge bg-label-success rounded-pill px-3 py-2 w-100 d-flex align-items-center justify-content-center gap-2">
                            <span class="badge-dot bg-success"></span>
                            <span class="fw-bold" style="font-size: 10px;">CONNECTED</span>
                        </span>
                        @else
                        <span class="badge bg-label-danger rounded-pill px-3 py-2 w-100 d-flex align-items-center justify-content-center gap-2">
                            <span class="badge-dot bg-danger"></span>
                            <span class="fw-bold" style="font-size: 10px;">DISCONNECTED</span>
                        </span>
                        @endif
                    </div>

                    <div class="d-grid gap-2">
                        @if($device->status != 'connected')
                        <button class="btn btn-primary rounded-3 py-2 fw-bold" onclick="showQrModal({{ $device->id }}, '{{ $device->name }}')">
                            <i class="bx bx-qr-scan me-1"></i> Hubungkan WhatsApp
                        </button>
                        @else
                        <button class="btn btn-label-danger rounded-3 py-2 fw-bold" onclick="disconnectDevice({{ $device->id }})">
                            <i class="bx bx-log-out me-1"></i> Putuskan Koneksi
                        </button>
                        @endif
                        
                        <button class="btn btn-light rounded-3 py-2 fw-bold border" onclick="checkStatus({{ $device->id }})">
                            <i class="bx bx-refresh me-1"></i> Cek Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Modal Add Device -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Device Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addDeviceForm">
                @csrf
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Device</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: CS Utama" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-muted">Nomor WhatsApp</label>
                        <input type="text" name="device" class="form-control" placeholder="628123456789" required>
                        <small class="text-muted mt-1 d-block">Masukkan nomor lengkap dengan kode negara (62).</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Device</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Scan QR WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-5">
                <p class="text-muted mb-4" id="qrInstruction">Silakan scan QR ini menggunakan WhatsApp di HP Anda.</p>
                <div id="qrContainer" class="mb-4 d-flex justify-content-center align-items-center" style="min-height: 250px;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="qrInfo" class="alert alert-info py-2 small d-none"></div>
                <p class="small text-muted mb-0 mt-3"><i class="bx bx-info-circle me-1"></i> Jangan menutup jendela ini sampai koneksi berhasil.</p>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="refreshQr()">Refresh QR</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    let currentDeviceId = null;
    let qrCheckInterval = null;

    // Add Device
    $('#addDeviceForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.text();
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: "{{ route('whatsapp.devices.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Device berhasil ditambahkan.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).text(originalText);
                Swal.fire('Error', xhr.responseJSON.error || 'Gagal menambahkan device.', 'error');
            }
        });
    });

    // Show QR Modal
    function showQrModal(id, name) {
        currentDeviceId = id;
        $('#qrModal').modal('show');
        $('#qrContainer').html('<div class="spinner-border text-primary" role="status"></div>');
        $('#qrInfo').addClass('d-none');
        
        fetchQr();
        
        // Polling for status
        qrCheckInterval = setInterval(() => {
            checkConnectionStatus(id);
        }, 5000);
    }

    // Fetch QR from Fonnte
    function fetchQr() {
        $.ajax({
            url: `/whatsapp/devices/${currentDeviceId}/qr`,
            type: "GET",
            success: function(response) {
                let qrSource = response.url;
                if (qrSource) {
                    // Check if it's base64 without prefix
                    if (!qrSource.startsWith('http') && !qrSource.startsWith('data:')) {
                        qrSource = 'data:image/png;base64,' + qrSource;
                    }
                    $('#qrContainer').html(`<img src="${qrSource}" class="img-fluid rounded" style="width: 250px; border: 10px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">`);
                } else {
                    const reason = response.reason || response.message || 'Gagal mendapatkan QR Code dari Fonnte.';
                    $('#qrContainer').html(`<div class="alert alert-warning m-0">${reason}</div>`);
                    if (reason.toLowerCase().includes('connected')) {
                        clearInterval(qrCheckInterval);
                        setTimeout(() => window.location.reload(), 2000);
                    }
                }
            },
            error: function(xhr) {
                $('#qrContainer').html(`<div class="alert alert-danger m-0">Error: ${xhr.responseJSON?.error || 'Server error'}</div>`);
            }
        });
    }

    function refreshQr() {
        $('#qrContainer').html('<div class="spinner-border text-primary" role="status"></div>');
        fetchQr();
    }

    // Check connection status while QR is open
    function checkConnectionStatus(id) {
        $.post(`/whatsapp/devices/${id}/status`, { _token: "{{ csrf_token() }}" }, function(response) {
            if (response.status && response.device_status === 'connect') {
                clearInterval(qrCheckInterval);
                Swal.fire({
                    icon: 'success',
                    title: 'Terhubung!',
                    text: 'WhatsApp berhasil terhubung.',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    // Disconnect Device
    function disconnectDevice(id) {
        Swal.fire({
            title: 'Putuskan koneksi?',
            text: "Sesi WhatsApp akan dihentikan dari sistem ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e1d',
            confirmButtonText: 'Ya, Putuskan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/whatsapp/devices/${id}/disconnect`, { _token: "{{ csrf_token() }}" }, function(response) {
                    window.location.reload();
                });
            }
        });
    }

    // Delete Device
    function deleteDevice(id) {
        Swal.fire({
            title: 'Hapus device?',
            text: "Data device akan dihapus dari database lokal.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e1d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/whatsapp/devices/${id}`,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        window.location.reload();
                    }
                });
            }
        });
    }

    // Check Status Manual
    function checkStatus(id) {
        Swal.fire({
            title: 'Mengecek status...',
            didOpen: () => {
                Swal.showLoading();
                $.post(`/whatsapp/devices/${id}/status`, { _token: "{{ csrf_token() }}" }, function(response) {
                    Swal.close();
                    if (response.status) {
                        const status = response.device_status === 'connect' ? 'Terhubung' : 'Terputus';
                        const icon = response.device_status === 'connect' ? 'success' : 'info';
                        Swal.fire('Status Device', `Status saat ini: <b>${status}</b>`, icon).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Error', 'Gagal mengecek status.', 'error');
                    }
                });
            }
        });
    }

    // Cleanup interval when modal is closed
    $('#qrModal').on('hidden.bs.modal', function() {
        if (qrCheckInterval) clearInterval(qrCheckInterval);
    });
</script>
@endpush
@endsection
