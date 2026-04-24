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
                "order": [[0, "desc"]],
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
                    { "data": "name_html", "name": "name" },
                    { "data": "address_html", "name": "address" },
                    { "data": "contact_html", "name": "phone", "orderable": false, "searchable": false },
                    { "data": "rating_html", "name": "rating" },
                    { "data": "category_html", "name": "category" },
                    { "data": "action", "name": "action", "orderable": false, "searchable": false, "className": "text-end" }
                ],
                "drawCallback": function(settings) {
                    $('#totalLeadsBadge').text(settings._iRecordsTotal);
                }
            });
        });

        /**
         * Export current page leads to Excel
         */
        function exportToExcel() {
            const data = leadsTable.rows({page:'current'}).data().toArray();
            if (data.length === 0) return;

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
        }

        /**
         * Export current page leads to CSV
         */
        function exportToCSV() {
            const data = leadsTable.rows({page:'current'}).data().toArray();
            if (data.length === 0) return;

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
        }
    </script>
@endpush
