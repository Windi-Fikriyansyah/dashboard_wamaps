@extends('template.app')

@push('css')
    <style>
        #radius {
            accent-color: #696cff;
        }

        .form-range::-webkit-slider-thumb {
            background: #696cff;
        }

        .form-range::-moz-range-thumb {
            background: #696cff;
        }

        .form-range::-ms-thumb {
            background: #696cff;
        }

        /* Compact Table Styles */
        #resultsTableContainer {
            font-size: 11px !important;
            border: 1px solid #f1f5f9;
        }

        #resultsTableContainer thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #566a7f;
            border-bottom: 1px solid #d9dee3;
        }

        #resultsTableContainer th,
        #resultsTableContainer td {
            padding: 10px 12px !important;
            vertical-align: middle;
            border-color: #f1f5f9 !important;
        }

        .business-name {
            font-weight: 800;
            color: #32475c;
            display: block;
            margin-bottom: 2px;
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

        .btn-save-lead {
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 6px;
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
        @if (empty(Auth::user()->search_api_key) || empty(Auth::user()->fonnte_token))
            <div class="alert alert-danger d-flex align-items-center mb-4 p-3 shadow-sm border-start border-danger border-4" role="alert">
                <span class="badge badge-center rounded-pill bg-danger me-3 p-2" style="width: 40px; height: 40px;">
                    <i class="bx bx-error fs-3"></i>
                </span>
                <div class="d-flex flex-column">
                    <h6 class="alert-heading mb-1 fw-bold text-danger">Konfigurasi Belum Lengkap!</h6>
                    <span class="small">
                        Silakan isi <strong>SearchAPI.io Key</strong> dan <strong>Fonnte Token</strong> Anda di 
                        <a href="{{ route('settings.index') }}" class="alert-link text-decoration-underline">Pengaturan Akun</a> 
                        terlebih dahulu agar fitur pencarian dan pengiriman pesan dapat digunakan secara maksimal.
                    </span>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Search Leads (Google Maps)</h5>
                <small class="text-muted float-end">Mencari data bisnis dari Google Maps</small>
            </div>
            <div class="card-body">
                <form id="searchForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="keyword">Kata Kunci (Contoh: Cafe, Bengkel, Apotek)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" id="keyword" name="keyword" class="form-control" placeholder="Cafe"
                                    required />
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="location_name">Lokasi (Kota atau Daerah)</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-map"></i></span>
                                <input type="text" id="location_name" name="location_name" class="form-control"
                                    placeholder="Jakarta Selatan" required />
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label" for="radius">Radius: <span id="radiusVal"
                                    class="fw-bold text-primary">5</span> Km</label>
                            <input type="range" id="radius" name="radius" class="form-range" value="5" min="1" max="50"
                                step="1" oninput="document.getElementById('radiusVal').innerText = this.value" required />
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label" for="max_results">Max Hasil</label>
                            <select id="max_results" name="max_results" class="form-select">
                                <option value="20">20 Leads</option>
                                <option value="40">40 Leads</option>
                                <option value="60">60 Leads</option>
                                <option value="100">100 Leads (Eksperimental)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-2 d-flex justify-content-end">
                        <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
                        <button type="submit" class="btn btn-primary" id="searchBtn">
                            <span id="btnText"><i class="bx bx-search me-1"></i> Mulai Pencarian</span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm me-1" role="status"
                                aria-hidden="true" style="display: none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Section -->
        <div id="resultsWrapper" style="display: none;">
            <div class="card">
                <h5 class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <span>Hasil Pencarian <span class="badge bg-label-primary ms-1" id="resultCount">0</span></span>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-label-info d-flex align-items-center" id="sourceBadge"></span>
                        <button class="btn btn-sm btn-primary" onclick="saveAllLeads()" id="saveAllBtn">
                            <i class="bx bx-save me-1"></i> Simpan Semua
                        </button>
                        <button class="btn btn-sm btn-success" onclick="exportToExcel()">
                            <i class="bx bxs-file-export me-1"></i> Excel
                        </button>
                        <button class="btn btn-sm btn-info" onclick="exportToCSV()">
                            <i class="bx bx-file me-1"></i> CSV
                        </button>
                    </div>
                </h5>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover" id="resultsTableContainer">
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
                            <tbody id="resultsTable" class="table-border-bottom-0">
                                <!-- DataTable will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Category Modal -->
    <div class="modal fade" id="saveCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom pb-3">
                    <h5 class="modal-title fw-bold" id="modalCenterTitle">Simpan ke Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-4">Pilih atau buat kategori baru untuk menyimpan data ini agar lebih rapi.</p>
                    
                    <div class="mb-3">
                        <label for="categoryInput" class="form-label fw-bold">Nama Kategori</label>
                        <input type="text" id="categoryInput" class="form-control" placeholder="Contoh: Restoran Jakarta, Prospek Hot..." value="General">
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <button type="button" class="btn btn-xs btn-outline-primary cat-tag" onclick="setCategory('General')">General</button>
                        <button type="button" class="btn btn-xs btn-outline-primary cat-tag" onclick="setCategory('Hot Leads')">Hot Leads</button>
                        <button type="button" class="btn btn-xs btn-outline-primary cat-tag" onclick="setCategory('Follow Up')">Follow Up</button>
                        <button type="button" class="btn btn-xs btn-outline-primary cat-tag" onclick="setCategory('Closed')">Closed</button>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmSaveBatchBtn" onclick="confirmSaveBatch()">
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let leadsTable = null;
        let currentLeads = [];

        document.getElementById('searchForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const form = e.target;
            const btn = document.getElementById('searchBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            const resultsWrapper = document.getElementById('resultsWrapper');
            const resultsTable = document.getElementById('resultsTable');
            const resultCount = document.getElementById('resultCount');
            const sourceBadge = document.getElementById('sourceBadge');

            // Reset & Loading State
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoader.style.display = 'inline-block';

            const formData = new FormData(form);

            try {
                const response = await fetch("{{ route('leads.search') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    resultsWrapper.style.display = 'block';
                    currentLeads = data.leads;

                    resultCount.innerText = data.total;

                    sourceBadge.className = data.source === 'cache' ? 'badge bg-label-success me-2' : 'badge bg-label-info me-2';

                    // Initialize or Re-initialize DataTables
                    if ($.fn.DataTable.isDataTable('#resultsTableContainer')) {
                        $('#resultsTableContainer').DataTable().destroy();
                    }

                    // Empty the existing table body first
                    resultsTable.innerHTML = '';

                    // Add rows to table
                    data.leads.forEach(lead => {
                        const phoneHtml = lead.phone ? `
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <i class="bx bx-phone bx-xs text-primary"></i>
                                <span class="small">${lead.phone}</span>
                            </div>
                        ` : '';

                        const websiteHtml = lead.website ? `
                            <div class="d-flex align-items-center gap-1">
                                <i class="bx bx-globe bx-xs text-info"></i>
                                <a href="${lead.website}" target="_blank" class="small text-info text-decoration-underline">Website</a>
                            </div>
                        ` : '';

                        resultsTable.innerHTML += `
                            <tr>
                                <td>
                                    <span class="business-name">${lead.name}</span>
                                </td>
                                <td>
                                    <span class="text-wrap-custom d-inline-block" title="${lead.address}">
                                        ${lead.address}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        ${phoneHtml}
                                        ${websiteHtml}
                                        ${!lead.phone && !lead.website ? '<span class="text-muted small">-</span>' : ''}
                                    </div>
                                </td>
                                <td>
                                    <div class="rating-badge">
                                        <i class="bx bxs-star"></i> ${lead.rating || '0'}
                                    </div>
                                </td>
                                <td><span class="badge bg-label-primary rounded-pill" style="font-size: 10px;">${lead.category || 'N/A'}</span></td>
                                <td>
                                    <div class="action-btns">
                                        ${lead.is_saved ? `
                                            <button class="btn btn-success btn-save-lead" disabled>
                                                <i class="bx bx-check-circle me-1"></i> Tersimpan
                                            </button>
                                        ` : `
                                            <button class="btn btn-outline-primary btn-save-lead" onclick="saveSingleLead(${lead.id}, this)">
                                                <i class="bx bx-save me-1"></i> Simpan
                                            </button>
                                        `}
                                        <a href="https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(lead.name + ' ' + lead.address)}" 
                                           target="_blank" class="btn btn-sm btn-icon btn-label-secondary">
                                            <i class="bx bx-link-external"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    // Initialize DataTable
                    leadsTable = $('#resultsTableContainer').DataTable({
                        "pageLength": 10,
                        "order": [[3, "desc"]], // Sort by Rating desc by default
                        "scrollX": true,
                        "autoWidth": false,
                        "language": {
                            "search": "Cari di hasil:",
                            "lengthMenu": "Tampilkan _MENU_ entri",
                            "paginate": {
                                "next": '<i class="bx bx-chevron-right"></i>',
                                "previous": '<i class="bx bx-chevron-left"></i>'
                            }
                        },
                        "columnDefs": [
                            { "orderable": false, "targets": [1, 2, 5] }, // Address, Contact, Aksi not sortable
                            { "width": "20%", "targets": 0 },
                            { "width": "25%", "targets": 1 },
                            { "width": "15%", "targets": 2 }
                        ]
                    });

                    // Scroll to results
                    resultsWrapper.scrollIntoView({ behavior: 'smooth' });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pencarian Gagal',
                        text: data.error || 'Terjadi kesalahan pada server.'
                    });
                }
            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menghubungi server. Silakan coba lagi.'
                });
            } finally {
                btn.disabled = false;
                btnText.style.display = 'inline-block';
                btnLoader.style.display = 'none';
            }
        });

        /**
         * Save a single lead
         */
        async function saveSingleLead(leadId, btn) {
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const response = await fetch("{{ route('leads.save') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        lead_id: leadId,
                        category: 'General'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-success');
                    btn.innerHTML = '<i class="bx bx-check-circle"></i> Tersimpan';
                    btn.onclick = null; // Prevent re-click
                } else {
                    alert(data.error || 'Gagal menyimpan');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error(error);
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        /**
         * Set category in modal
         */
        function setCategory(cat) {
            document.getElementById('categoryInput').value = cat;
        }

        /**
         * Open Category Modal for Batch Saving
         */
        function saveAllLeads() {
            if (!currentLeads || currentLeads.length === 0) return;
            const myModal = new bootstrap.Modal(document.getElementById('saveCategoryModal'));
            myModal.show();
        }

        /**
         * Confirm and execute Batch Save
         */
        async function confirmSaveBatch() {
            const btn = document.getElementById('confirmSaveBatchBtn');
            const category = document.getElementById('categoryInput').value;
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            try {
                const response = await fetch("{{ route('leads.save-batch') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        lead_ids: currentLeads.map(l => l.id),
                        category: category
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update all buttons in the table to "Tersimpan"
                    document.querySelectorAll('.btn-save-lead').forEach(btn => {
                        if (!btn.disabled) {
                            btn.classList.remove('btn-outline-primary');
                            btn.classList.add('btn-success');
                            btn.innerHTML = '<i class="bx bx-check-circle me-1"></i> Tersimpan';
                            btn.disabled = true;
                            btn.onclick = null;
                        }
                    });

                    // Update local state
                    currentLeads.forEach(l => l.is_saved = true);

                    // Close modal
                    const modalEl = document.getElementById('saveCategoryModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.error || 'Terjadi kesalahan'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menghubungi server.'
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        /**
         * Export current leads to Excel
         */
        function exportToExcel() {
            if (!currentLeads || currentLeads.length === 0) return;

            const worksheetData = currentLeads.map(lead => ({
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

            const filename = `leads_export_${new Date().getTime()}.xlsx`;
            XLSX.writeFile(wb, filename);
        }

        /**
         * Export current leads to CSV
         */
        function exportToCSV() {
            if (!currentLeads || currentLeads.length === 0) return;

            const headers = ['Nama Bisnis', 'Alamat', 'Telepon', 'Website', 'Rating', 'Kategori', 'Google Maps Link'];
            const csvRows = [headers.join(',')];

            currentLeads.forEach(lead => {
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
            link.setAttribute("download", `leads_export_${new Date().getTime()}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- XLSX for Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
@endpush