@extends('template.app')

@section('content')
<div class="col-md-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Message Templates</h4>
            <p class="text-muted small mb-0">Kelola template pesan WhatsApp untuk broadcast Anda</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="showAddModal()">
            <i class="bx bx-plus me-1"></i> Tambah Template
        </button>
    </div>

    <style>
        .swal2-container {
            z-index: 99999 !important;
        }
    </style>

    @if($templates->isEmpty())
    <div class="card p-5 text-center shadow-none border-0 align-items-center" style="background-color: #f8f9fa; border-radius: 20px;">
        <div class="mb-3">
            <div class="bg-white d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 80px; height: 80px;">
                <i class="bx bx-message-square-detail text-primary" style="font-size: 40px;"></i>
            </div>
        </div>
        <h5 class="fw-bold">Belum ada Template</h5>
        <p class="text-muted mx-auto" style="max-width: 400px;">Anda belum menambahkan template pesan. Buat template untuk memudahkan proses pengiriman broadcast.</p>
        <button class="btn btn-outline-primary btn-sm px-4" onclick="showAddModal()">Tambah Template</button>
    </div>
    @else
    <div class="row">
        @foreach($templates as $template)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0 group" style="border-radius: 20px; transition: all 0.3s;">
                <div class="card-body p-4 flex-column d-flex">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-label-primary" style="width: 35px; height: 35px;">
                                <i class="bx bx-file"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-truncate" style="max-width: 180px;">{{ $template->name }}</h6>
                        </div>
                        <div class="dropdown">
                            <button class="btn p-0" type="button" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="editTemplate({{ $template->id }})">
                                    <i class="bx bx-edit-alt me-1"></i> Edit
                                </a>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteTemplate({{ $template->id }})">
                                    <i class="bx bx-trash me-1"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex-grow-1 mb-3">
                        <div class="p-3 bg-light rounded-3 text-muted small" style="min-height: 100px; max-height: 150px; overflow-y: auto; white-space: pre-wrap; line-height: 1.6; border: 1px solid #eee;">{{ $template->content }}</div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                        <span class="text-muted" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">DIBUAT {{ \Carbon\Carbon::parse($template->created_at)->format('d M Y') }}</span>
                        <button class="btn btn-sm btn-label-primary px-3" onclick="editTemplate({{ $template->id }})">Edit</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Modal Template -->
<div class="modal fade" id="templateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Template Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="templateForm">
                @csrf
                <input type="hidden" name="id" id="templateId">
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nama Template</label>
                        <input type="text" name="name" id="templateName" class="form-control" placeholder="Contoh: Welcome Message" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted">Isi Pesan</label>
                        <textarea name="content" id="templateContent" class="form-control" rows="8" placeholder="Tulis pesan WhatsApp Anda di sini..." required></textarea>
                    </div>
                    <div>
                        <p class="text-[10px] fw-bold text-muted text-uppercase mb-2" style="font-size: 10px; letter-spacing: 1px;">Variabel (Klik untuk menyisipkan)</p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('@{{name}}')">Nama Bisnis</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('@{{address}}')">Alamat</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('@{{phone}}')">Nomor HP</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="insertVariable('@{{category}}')">Kategori</button>
                        </div>
                        <p class="small text-muted mt-2 mb-0 italic"><i>Variabel akan otomatis diganti dengan data asli saat broadcast.</i></p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" id="btnSubmit">Simpan Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    const templateModal = new bootstrap.Modal(document.getElementById('templateModal'));
    
    function showAddModal() {
        $('#modalTitle').text('Tambah Template Baru');
        $('#templateId').val('');
        $('#templateName').val('');
        $('#templateContent').val('');
        $('#btnSubmit').text('Simpan Template');
        templateModal.show();
    }

    function insertVariable(variable) {
        const textarea = document.getElementById('templateContent');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const before = text.substring(0, start);
        const after = text.substring(end, text.length);
        textarea.value = before + variable + after;
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + variable.length;
    }

    $('#templateForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#templateId').val();
        const url = id ? `/whatsapp/templates/${id}` : "{{ route('whatsapp.templates.store') }}";
        const method = id ? 'PUT' : 'POST';
        
        const btn = $('#btnSubmit');
        const originalText = btn.text();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

        $.ajax({
            url: url,
            type: method,
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Template berhasil disimpan.',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).text(originalText);
                Swal.fire('Error', xhr.responseJSON.error || 'Gagal menyimpan template.', 'error');
            }
        });
    });

    function editTemplate(id) {
        $.get(`/whatsapp/templates/${id}`, function(response) {
            $('#modalTitle').text('Edit Template');
            $('#templateId').val(response.id);
            $('#templateName').val(response.name);
            $('#templateContent').val(response.content);
            $('#btnSubmit').text('Simpan Perubahan');
            templateModal.show();
        });
    }

    function deleteTemplate(id) {
        Swal.fire({
            title: 'Hapus template?',
            text: "Data template ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e1d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/whatsapp/templates/${id}`,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Template berhasil dihapus.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
