@extends('template.app')

@section('content')
<div class="col-md-12">
    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Database Leads
            <a href="{{ route('leads.index') }}" class="btn btn-sm btn-primary">
                <i class="bx bx-plus me-1"></i> Tambah Data
            </a>
        </h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Bisnis</th>
                        <th>Alamat</th>
                        <th>No. Telepon</th>
                        <th>Kategori</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($leads as $lead)
                    <tr>
                        <td>{{ $lead->id }}</td>
                        <td>
                            <i class="bx bx-store-alt text-primary me-2"></i>
                            <strong>{{ $lead->name }}</strong>
                        </td>
                        <td>
                            <span class="text-truncate d-inline-block" style="max-width: 350px;" title="{{ $lead->address }}">
                                {{ $lead->address }}
                            </span>
                        </td>
                        <td>{{ $lead->phone ?? '-' }}</td>
                        <td><span class="badge bg-label-info">{{ $lead->category ?? 'N/A' }}</span></td>
                        <td>
                            <span class="badge bg-label-warning">
                                <i class="bx bxs-star me-1"></i> {{ $lead->rating ?? '0' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data leads. Silakan lakukan pencarian terlebih dahulu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
        <div class="card-footer clearfix">
            <div class="float-end">
                {{ $leads->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
