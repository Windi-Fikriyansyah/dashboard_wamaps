@extends('template.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="mb-4">
        <h4 class="fw-bold py-3 mb-0">Pengaturan</h4>
        <p class="text-muted">Kelola profil dan konfigurasi API Anda secara mandiri.</p>
    </div>

    <form id="formSettings" method="POST">
        @csrf
        <div class="row">
            <!-- Profile Section -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-body p-4 md:p-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="p-2 bg-label-primary rounded-3">
                                <i class="bx bx-user fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Profil Pengguna</h5>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control py-2 shadow-none border-light-subtle" 
                                   value="{{ $user->name }}" placeholder="Masukkan nama Anda" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control py-2 shadow-none border-light-subtle" 
                                   value="{{ $user->email }}" placeholder="email@example.com" required>
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label fw-bold">Password Baru</label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="password" id="password" class="form-control py-2 shadow-none border-light-subtle" 
                                       placeholder="••••••••">
                                <span class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <small class="text-muted mt-1 d-block">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>

                        <div class="mb-0 form-password-toggle">
                            <label class="form-label fw-bold">Konfirmasi Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control py-2 shadow-none border-light-subtle" 
                                       placeholder="••••••••">
                                <span class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Section -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4 md:p-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="p-2 bg-label-info rounded-3">
                                <i class="bx bx-key fs-4"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Konfigurasi API</h5>
                        </div>

                        <div class="mb-4 form-password-toggle">
                            <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                SearchAPI.io Key
                                <a href="https://www.searchapi.io" target="_blank" class="text-primary small fw-normal">Dapatkan Key</a>
                            </label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="search_api_key" id="search_api_key" class="form-control py-2 shadow-none border-light-subtle" 
                                       value="{{ $user->search_api_key }}" placeholder="Masukkan SearchAPI Key">
                                <span class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <div class="mt-3 p-3 bg-light rounded-3 small">
                                <p class="mb-2 fw-bold text-dark"><i class="bx bx-info-circle me-1"></i> Cara Mendapatkan:</p>
                                <ol class="ps-3 mb-0 text-muted">
                                    <li>Buka <a href="https://www.searchapi.io" target="_blank">SearchAPI.io</a></li>
                                    <li>Cari bagian <strong>API Key</strong> di Dashboard</li>
                                    <li>Copy & Paste ke kolom di atas</li>
                                </ol>
                            </div>
                        </div>

                        <div class="mb-0 form-password-toggle">
                            <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                Fonnte API Token
                                <a href="https://fonnte.com" target="_blank" class="text-primary small fw-normal">Dapatkan Token</a>
                            </label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="fonnte_token" id="fonnte_token" class="form-control py-2 shadow-none border-light-subtle" 
                                       value="{{ $user->fonnte_token }}" placeholder="Masukkan Fonnte Token">
                                <span class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <div class="mt-3 p-3 bg-light rounded-3 small">
                                <p class="mb-2 fw-bold text-dark"><i class="bx bx-info-circle me-1"></i> Cara Mendapatkan:</p>
                                <ol class="ps-3 mb-0 text-muted">
                                    <li>Buka <a href="https://fonnte.com" target="_blank">Fonnte.com</a></li>
                                    <li>Cari <strong>Account Token</strong> di halaman Setting</li>
                                    <li>Copy & Paste ke kolom di atas</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" id="btnSave" class="btn btn-primary py-3 fw-bold shadow-sm" style="border-radius: 15px;">
                        <i class="bx bx-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .form-control:focus {
        border-color: #696cff;
    }
    .bg-light {
        background-color: #f8f9fa !important;
    }
    .input-group-text {
        border-color: #f0f2f4;
        background-color: #f8f9fa;
    }
</style>

@push('js')
<script>
    $(document).ready(function() {
        $('#formSettings').on('submit', function(e) {
            e.preventDefault();
            saveSettings(this);
        });
    });

    function saveSettings(form) {
        const btn = $('#btnSave');
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        $.ajax({
            url: "{{ route('settings.update') }}",
            method: 'POST',
            data: data,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                const errors = xhr.responseJSON?.errors;
                let errorMsg = xhr.responseJSON?.message || 'Gagal menyimpan pengaturan.';
                
                if (errors) {
                    errorMsg = Object.values(errors).flat().join('<br>');
                }
                
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    }
</script>
@endpush
@endsection
