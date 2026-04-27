@extends('template.app')

@push('css')
    <style>
        /* Compact Table Styles */
        #leadsTableContainer {
            font-size: 11px !important;
            border: 1px solid #f1f5f9;
        }

        #leadsTableContainer thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #566a7f;
            border-bottom: 1px solid #d9dee3;
        }

        #leadsTableContainer th,
        #leadsTableContainer td {
            padding: 10px 12px !important;
            vertical-align: middle;
            border-color: #f1f5f9 !important;
        }

        .business-name {
            font-weight: 800;
            color: #32475c;
            display: block;
            margin-bottom: 2px;
            white-space: normal !important;
            max-width: 200px;
            line-height: 1.4;
        }

        .rating-badge {
            background-color: #fff8e1;
            color: #ffab00;
            padding: 4px 8px;
            border-radius: 50px;
            font-weight: 700;
            border: 1px solid #ffe58f;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .action-btns {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }

        .text-wrap-custom {
            white-space: normal !important;
            line-height: 1.4;
            max-width: 220px;
            color: #697a8d;
        }
    </style>
@endpush

@section('content')
<div class="col-md-12">
    <div class="card">
        <h5 class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <span>Database Leads <span class="badge bg-label-primary ms-1" id="totalLeadsBadge">0</span></span>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-sm btn-danger d-none" id="btnDeleteSelected" onclick="deleteSelectedLeads()">
                    <i class="bx bx-trash me-1"></i> Hapus Terpilih (<span id="selectedLeadsCount">0</span>)
                </button>
                <a href="{{ route('leads.index') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Data
                </a>
                <button class="btn btn-sm btn-success" onclick="exportToExcel()">
                    <i class="bx bxs-file-export me-1"></i> Excel
                </button>
                <button class="btn btn-sm btn-info" onclick="exportToCSV()">
                    <i class="bx bx-file me-1"></i> CSV
                </button>
            </div>
        </h5>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100" id="leadsTableContainer">
                    <thead>
                        <tr>
                            <th style="width: 30px;"><input type="checkbox" class="form-check-input" id="selectAllLeads"></th>
                            <th>Business Name</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Rating</th>
                            <th>Kategori</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <!-- Data fetched via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <!-- XLSX for Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        let leadsTable = null;

        $(document).ready(function() {
            leadsTable = $('#leadsTableContainer').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('leads.data') }}",
                    "type": "GET"
                },
                "pageLength": 15,
                "order": [[1, "desc"]],
                "scrollX": true,
                "autoWidth": false,
                "language": {
                    "search": "Cari Database:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "processing": '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Loading...',
                    "paginate": {
                        "next": '<i class="bx bx-chevron-right"></i>',
                        "previous": '<i class="bx bx-chevron-left"></i>'
                    }
                },
                "columns": [
                    { "data": "checkbox", "name": "user_saved_leads.id", "orderable": false, "searchable": false },
                    { "data": "name_html", "name": "leads.name" },
                    { "data": "address_html", "name": "leads.address" },
                    { "data": "contact_html", "name": "leads.phone", "orderable": false, "searchable": false },
                    { "data": "rating_html", "name": "leads.rating" },
                    { "data": "category_html", "name": "user_saved_leads.category" },
                    { "data": "action", "name": "action", "orderable": false, "searchable": false, "className": "text-end" }
                ],
                "drawCallback": function(settings) {
                    $('#totalLeadsBadge').text(settings._iRecordsTotal);
                    $('#selectAllLeads').prop('checked', false);
                    updateBulkDeleteButton();
                }
            });

            // Select All logic
            $('#selectAllLeads').on('change', function() {
                $('.lead-checkbox').prop('checked', $(this).prop('checked'));
                updateBulkDeleteButton();
            });

            // Individual checkbox logic
            $(document).on('change', '.lead-checkbox', function() {
                updateBulkDeleteButton();
            });
        });

        function updateBulkDeleteButton() {
            const selectedCount = $('.lead-checkbox:checked').length;
            if (selectedCount > 0) {
                $('#btnDeleteSelected').removeClass('d-none');
                $('#selectedLeadsCount').text(selectedCount);
            } else {
                $('#btnDeleteSelected').addClass('d-none');
            }
        }

        /**
         * Delete multiple leads
         */
        function deleteSelectedLeads() {
            const selectedIds = $('.lead-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Hapus Terpilih?',
                text: `Anda akan menghapus ${selectedIds.length} lead terpilih secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff3e1d',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('leads.delete-batch') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        },
                        success: function(response) {
                            leadsTable.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.error || 'Gagal menghapus data.',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        }
                    });
                }
            });
        }

        /**
         * Export all leads to Excel
         */
        async function exportToExcel() {
            try {
                const response = await fetch("{{ route('leads.export') }}");
                const data = await response.json();
                
                if (data.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diekspor', 'info');
                    return;
                }

                const worksheetData = data.map(lead => ({
                    'Nama Bisnis': lead.name,
                    'Alamat': lead.address,
                    'Telepon': lead.phone || '-',
                    'Website': lead.website || '-',
                    'Rating': lead.rating || '0',
                    'Kategori': lead.category || 'N/A',
                    'Google Maps Link': `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lead.name + ' ' + (lead.address || ''))}`
                }));

                const ws = XLSX.utils.json_to_sheet(worksheetData);
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Leads");

                const filename = `database_leads_export_${new Date().getTime()}.xlsx`;
                XLSX.writeFile(wb, filename);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Gagal mengambil data untuk ekspor', 'error');
            }
        }

        /**
         * Export all leads to CSV
         */
        async function exportToCSV() {
            try {
                const response = await fetch("{{ route('leads.export') }}");
                const data = await response.json();

                if (data.length === 0) {
                    Swal.fire('Info', 'Tidak ada data untuk diekspor', 'info');
                    return;
                }

                const headers = ['Nama Bisnis', 'Alamat', 'Telepon', 'Website', 'Rating', 'Kategori', 'Google Maps Link'];
                const csvRows = [headers.join(',')];

                data.forEach(lead => {
                    const mapsLink = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lead.name + ' ' + (lead.address || ''))}`;
                    const row = [
                        `"${(lead.name || '').replace(/"/g, '""')}"`,
                        `"${(lead.address || '').replace(/"/g, '""')}"`,
                        `"${(lead.phone || '').replace(/"/g, '""')}"`,
                        `"${(lead.website || '').replace(/"/g, '""')}"`,
                        lead.rating || '0',
                        `"${(lead.category || '').replace(/"/g, '""')}"`,
                        `"${mapsLink}"`
                    ];
                    csvRows.push(row.join(','));
                });

                const csvString = csvRows.join('\n');
                const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                const url = URL.createObjectURL(blob);

                link.setAttribute("href", url);
                link.setAttribute("download", `database_leads_export_${new Date().getTime()}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Gagal mengambil data untuk ekspor', 'error');
            }
        }

        /**
         * Delete lead from database
         */
        function deleteLead(id) {
            Swal.fire({
                title: 'Hapus Lead?',
                text: "Data lead ini akan dihapus permanen dari database.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff3e1d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/leads/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            leadsTable.ajax.reload();
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: 'Lead berhasil dihapus.',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.error || 'Gagal menghapus lead.',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
