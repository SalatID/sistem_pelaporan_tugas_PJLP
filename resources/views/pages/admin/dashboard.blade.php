@extends('layout.index_admin')
@section("title","Dashboard")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Dashboard</li>
  </ol>
@endsection
@section('content')
    
{{-- CONTENT ONLY: Dashboard --}}
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Dashboard</h4>
    <div class="text-muted">Hi, {{ auth()->user()->nma ?? auth()->user()->name ?? 'User' }}</div>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-muted">Total Tugas</div>
          <div class="fs-4 fw-semibold">{{ $stats['total'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-muted">Pending</div>
          <div class="fs-4 fw-semibold">{{ $stats['pending'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-muted">Approved</div>
          <div class="fs-4 fw-semibold">{{ $stats['approved'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-muted">Rejected</div>
          <div class="fs-4 fw-semibold">{{ $stats['rejected'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div class="fw-semibold">Tugas Terbaru</div>
      <a class="btn btn-sm btn-outline-primary" href="{{ route('tugas.index') }}">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Pelapor</th>
              <th>Status</th>
              <th>Dibuat</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($latest as $t)
              <tr>
                <td>{{ $t->nama }}</td>
                <td>{{ $t->kategori->nama ?? '-' }}</td>
                <td>{{ $t->pengguna->nma ?? '-' }}</td>
                <td>
                  <span class="badge bg-secondary">{{ $t->status }}</span>
                </td>
                <td>{{ optional($t->created_at)->format('d-m-Y H:i') }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('tugas.show', $t->id) }}">Detail</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection