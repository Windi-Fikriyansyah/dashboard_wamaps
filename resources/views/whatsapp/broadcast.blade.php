@extends('template.app')

@section('content')
<div class="col-md-12">
    <div class="d-flex flex-column flex-md-row align-items-md-center gap-3 mb-4">
        <div class="bg-label-primary p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
            <i class="bx bx-send fs-3"></i>
        </div>
        <div>
            <h4 class="fw-bold mb-1">WhatsApp Broadcast</h4>
            <p class="text-muted small mb-0">Kirim pesan massal ke lead yang telah Anda simpan</p>
        </div>
    </div>

    <div class="row">
        <!-- Left: Lead Selection -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bx bx-group text-muted"></i>
                        Pilih Lead (<span id="selectedCount">0</span>)
                    </h5>
                    <div class="d-flex flex-column flex-md-row gap-2">
                        <select class="form-select form-select-sm" id="categoryFilter" style="width: 150px; border-radius: 10px;">
                            <option value="All">Semua Kategori</option>
                            @php
                                $categories = $leads->pluck('saved_category')->unique();
                            @endphp
                            @foreach($categories as $cat)
                                <option value="{{ $cat ?: 'General' }}">{{ $cat ?: 'General' }}</option>
                            @endforeach
                        </select>
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <span class="input-group-text bg-light border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bx bx-search"></i></span>
                            <input type="text" id="leadSearch" class="form-control bg-light border-start-0" placeholder="Cari lead..." style="border-radius: 0 10px 10px 0;">
                        </div>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 500px;">
                    <table class="table table-hover align-middle mb-0" id="leadsTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 50px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>Nama Lead</th>
                                <th>Kategori</th>
                                <th>Nomor WA</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leads as $lead)
                            <tr class="lead-row" data-category="{{ $lead->saved_category ?: 'General' }}">
                                <td class="ps-4">
                                    <div class="form-check">
                                        <input class="form-check-input lead-checkbox" type="checkbox" value="{{ $lead->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $lead->name }}</div>
                                    <div class="text-muted" style="font-size: 11px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $lead->address }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-label-info rounded-pill" style="font-size: 10px;">{{ $lead->saved_category ?: 'General' }}</span>
                                </td>
                                <td class="font-monospace small">
                                    {{ $lead->phone ?: '-' }}
                                </td>
                                <td>
                                    @if($lead->phone)
                                        <span class="badge bg-label-success rounded-pill px-2 py-1" style="font-size: 9px; font-weight: 800;">READY</span>
                                    @else
                                        <span class="badge bg-label-danger rounded-pill px-2 py-1" style="font-size: 9px; font-weight: 800;">SKIPPED</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted italic">Belum ada lead yang disimpan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Config -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i class="bx bx-cog text-muted"></i>
                        Konfigurasi Pesan
                    </h5>

                    <form id="broadcastForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pilih Device</label>
                            <select name="device_id" id="deviceSelect" class="form-select border-0 bg-light py-2" style="border-radius: 12px;" required>
                                <option value="">Pilih device...</option>
                                @foreach($devices as $device)
                                <option value="{{ $device->id }}" {{ $device->status == 'connected' ? 'selected' : '' }}>
                                    {{ $device->name }} ({{ strtoupper($device->status) }})
                                </option>
                                @endforeach
                            </select>
                            @if($devices->isEmpty())
                                <small class="text-danger mt-1 d-block italic">Belum ada device. Silakan tambah di menu Devices.</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Pilih Template</label>
                            <select name="template_id" id="templateSelect" class="form-select border-0 bg-light py-2" style="border-radius: 12px;" required>
                                <option value="">Pilih template...</option>
                                @foreach($templates as $template)
                                <option value="{{ $template->id }}" data-content="{{ $template->content }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Delay Antar Pesan (Detik)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-0">Min</span>
                                        <input type="number" name="delay_min" class="form-control border-0 bg-light" value="30" min="1" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-0">Max</span>
                                        <input type="number" name="delay_max" class="form-control border-0 bg-light" value="60" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block" style="font-size: 10px;">Sangat disarankan jeda acak 30-60 detik untuk keamanan akun WA Anda.</small>
                        </div>

                        <div id="previewContainer" class="d-none mb-4">
                            <div class="p-3 bg-label-primary rounded-3 border-0">
                                <div class="text-uppercase fw-bold mb-2 d-flex justify-content-between" style="font-size: 9px; letter-spacing: 1px;">
                                    <span>Preview Pesan</span>
                                    <span class="text-lowercase opacity-50" id="previewTarget"></span>
                                </div>
                                <div id="messagePreview" class="small text-dark" style="white-space: pre-wrap; line-height: 1.5;"></div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" id="btnSend" class="btn btn-primary w-100 py-3 fw-bold shadow-sm {{ $isRunning ? 'd-none' : '' }}" style="border-radius: 15px;" disabled>
                                <i class="bx bx-send me-1"></i> Kirim ke <span id="btnSelectedCount">0</span> Lead
                            </button>
                            <button type="button" id="btnStop" class="btn btn-label-danger w-100 py-3 fw-bold shadow-sm {{ $isRunning ? '' : 'd-none' }}" style="border-radius: 15px;" onclick="stopBroadcast()">
                                <i class="bx bx-stop-circle me-1"></i> Berhentikan Pengiriman
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning border-0 d-flex gap-3 align-items-start shadow-none" style="border-radius: 20px; background-color: #fff8e1;">
                <i class="bx bx-info-circle fs-4 text-warning"></i>
                <div class="small">
                    <b class="d-block mb-1">Informasi Broadcast:</b>
                    <ul class="ps-3 mb-0">
                        <li>Pesan dikirim lewat background job.</li>
                        <li>Otomatis istirahat 15 menit setiap 20 pesan.</li>
                        <li>Pastikan koneksi WA di HP tetap aktif.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    const leads = @json($leads);
    const templates = @json($templates);
    
    // Select All
    $('#selectAll').on('change', function() {
        $('.lead-checkbox:visible').prop('checked', $(this).prop('checked'));
        updateSelectedCount();
    });

    $(document).on('change', '.lead-checkbox', function() {
        updateSelectedCount();
    });

    function updateSelectedCount() {
        const count = $('.lead-checkbox:checked').length;
        $('#selectedCount, #btnSelectedCount').text(count);
        $('#btnSend').prop('disabled', count === 0 || !$('#templateSelect').val() || !$('#deviceSelect').val());
        updatePreview();
    }

    // Filters
    $('#categoryFilter, #leadSearch').on('change keyup', function() {
        const category = $('#categoryFilter').val();
        const search = $('#leadSearch').val().toLowerCase();

        $('.lead-row').each(function() {
            const rowCat = $(this).data('category');
            const rowText = $(this).text().toLowerCase();
            const matchesCat = category === 'All' || rowCat === category;
            const matchesSearch = rowText.includes(search);

            if (matchesCat && matchesSearch) {
                $(this).show();
            } else {
                $(this).hide();
                $(this).find('.lead-checkbox').prop('checked', false);
            }
        });
        updateSelectedCount();
    });

    // Template Preview
    $('#templateSelect').on('change', function() {
        updateSelectedCount();
    });

    function updatePreview() {
        const templateId = $('#templateSelect').val();
        const selectedLeadId = $('.lead-checkbox:checked').first().val();
        
        if (!templateId) {
            $('#previewContainer').addClass('d-none');
            return;
        }

        const template = templates.find(t => t.id == templateId);
        if (!template) return;

        let content = template.content;
        
        if (selectedLeadId) {
            const lead = leads.find(l => l.id == selectedLeadId);
            if (lead) {
                $('#previewTarget').text(`Preview untuk ${lead.name}`);
                content = content
                    .replace(/@{{name}}/g, lead.name || '')
                    .replace(/@{{address}}/g, lead.address || '')
                    .replace(/@{{phone}}/g, lead.phone || '')
                    .replace(/@{{category}}/g, lead.saved_category || '');
            }
        } else {
            $('#previewTarget').text('');
        }

        $('#messagePreview').text(content);
        $('#previewContainer').removeClass('d-none');
    }

    // Submit
    $('#broadcastForm').on('submit', function(e) {
        e.preventDefault();
        const leadIds = $('.lead-checkbox:checked').map(function() { return $(this).val(); }).get();
        
        const btn = $('#btnSend');
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...');

        $.ajax({
            url: "{{ route('whatsapp.broadcast.send') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                lead_ids: leadIds,
                template_id: $('#templateSelect').val(),
                device_id: $('#deviceSelect').val(),
                delay_min: $('input[name="delay_min"]').val(),
                delay_max: $('input[name="delay_max"]').val()
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Broadcast Dimulai!',
                    text: response.message,
                    confirmButtonText: 'Oke'
                });
                // Show Stop button, hide Send button
                $('#btnSend').addClass('d-none');
                $('#btnStop').removeClass('d-none');
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                Swal.fire('Error', xhr.responseJSON?.error || 'Gagal memulai broadcast.', 'error');
            }
        });
    });

    function stopBroadcast() {
        Swal.fire({
            title: 'Hentikan Pengiriman?',
            text: "Sinyal berhenti akan dikirim ke sistem background.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff3e1d',
            confirmButtonText: 'Ya, Berhenti!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const btn = $('#btnStop');
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menghentikan...');
                
                $.post("{{ route('whatsapp.broadcast.stop') }}", { _token: "{{ csrf_token() }}" }, function(response) {
                    Swal.fire('Berhasil', response.message, 'success').then(() => {
                        window.location.reload();
                    });
                });
            }
        });
    }
</script>
@endpush
@endsection
