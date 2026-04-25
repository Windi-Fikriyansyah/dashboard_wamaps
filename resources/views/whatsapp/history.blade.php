@extends('template.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">WhatsApp /</span> History Pesan
            </h4>
            <p class="text-muted mb-0">Lacak status pengiriman pesan WhatsApp Anda secara real-time.</p>
        </div>
        <button type="button" id="btnRefresh" class="btn btn-primary shadow-sm" onclick="refreshStatus()">
            <i class="bx bx-refresh me-1"></i> Refresh Status
        </button>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap p-4">
                <table class="table table-hover" id="tableHistory">
                    <thead>
                        <tr>
                            <th class="ps-2">Waktu</th>
                            <th>Penerima</th>
                            <th>Pesan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($histories as $history)
                        <tr>
                            <td class="ps-2">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y') }}</span>
                                    <span class="small text-muted">{{ \Carbon\Carbon::parse($history->created_at)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-primary rounded-pill">{{ $history->target }}</span>
                            </td>
                            <td>
                                <div class="text-wrap" style="min-width: 300px; max-width: 500px;">
                                    <p class="mb-0 small text-truncate-custom" title="{{ $history->message }}">
                                        {{ \Illuminate\Support\Str::limit($history->message, 100) }}
                                    </p>
                                </div>
                            </td>
                            <td>
                                @php
                                    $status = strtolower($history->status);
                                    $badgeClass = 'bg-label-secondary';
                                    $icon = 'bx-time-five';
                                    
                                    if ($status == 'sent') {
                                        $badgeClass = 'bg-label-success';
                                        $icon = 'bx-check-circle';
                                    } elseif (in_array($status, ['pending', 'waiting', 'processing'])) {
                                        $badgeClass = 'bg-label-warning';
                                        $icon = 'bx-loader-circle bx-spin';
                                    } elseif (in_array($status, ['failed', 'invalid', 'expired'])) {
                                        $badgeClass = 'bg-label-danger';
                                        $icon = 'bx-x-circle';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} d-inline-flex align-items-center gap-1">
                                    <i class="bx {{ $icon }} bx-xs"></i>
                                    {{ ucfirst($history->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-center">
                                    <i class="bx bx-history bx-lg text-muted mb-3"></i>
                                    <p class="mb-0">Belum ada history pengiriman pesan.</p>
                                    <a href="{{ route('whatsapp.broadcast') }}" class="btn btn-sm btn-primary mt-3">Mulai Broadcast</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .text-truncate-custom {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        white-space: normal;
    }
    
    #tableHistory thead th {
        background-color: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        font-weight: 700;
        border-top: none;
        padding: 15px 20px;
    }
    
    #tableHistory tbody td {
        padding: 15px 20px;
        vertical-align: middle;
    }

    .card {
        overflow: hidden;
    }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#tableHistory tbody tr').length > 1) {
            $('#tableHistory').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari history...",
                    lengthMenu: "_MENU_",
                },
                dom: '<"d-flex justify-content-between align-items-center header-actions mx-1 row mt-2" <"col-sm-12 col-md-4" l> <"col-sm-12 col-md-8" <"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1" f>> > t <"d-flex justify-content-between mx-2 row mb-1" <"col-sm-12 col-md-6" i> <"col-sm-12 col-md-6" p> >',
            });
        }
    });

    function refreshStatus() {
        const btn = $('#btnRefresh');
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Updating...');
        
        $.ajax({
            url: "{{ route('whatsapp.history.refresh') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalHtml);
                Swal.fire('Error', xhr.responseJSON?.error || 'Gagal merefresh status.', 'error');
            }
        });
    }
</script>
@endpush
@endsection
