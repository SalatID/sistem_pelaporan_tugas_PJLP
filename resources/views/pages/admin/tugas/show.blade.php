@extends('layout.index_admin')
@section("title","Detail Tugas")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Tugas Management</li>
    <li class="breadcrumb-item active">Detail Tugas</li>
  </ol>
@endsection
@section('content')
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-outline-secondary" href="{{ route('tugas.index') }}">Kembali</a>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12 col-md-8">
          <div class="mb-2 text-muted">Nama</div>
          <div class="fs-5 fw-semibold">{{ $tugas->nama }}</div>

          <hr>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="text-muted">Kategori</div>
              <div>{{ $tugas->kategori->nama ?? '-' }}</div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-muted">Pelapor</div>
              <div>{{ $tugas->pengguna->nama ?? $tugas->pengguna->fullname ?? '-' }}</div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-muted">Status</div>
              <span class="badge bg-secondary">{{ $tugas->status }}</span>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-muted">Dibuat Pada</div>
              <div>{{ optional($tugas->created_at)->format('d-m-Y H:i') }}</div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-muted">Update Terakhir</div>
              <div>{{ optional($tugas->updated_at)->format('d-m-Y H:i') }}</div>
            </div>
          </div>

          <hr>

          <div class="text-muted mb-2">Deskripsi</div>
          <div style="white-space: pre-wrap;">{{ $tugas->deskripsi ?: '-' }}</div>
        </div>
        <div class="row g-3">
          <div class="col-12 col-md-4">
            <div class="text-muted mb-2">Foto Sebelum</div>
            @if ($tugas->foto_sebelum)
              <img src="{{ asset('storage/' . $tugas->foto_sebelum) }}" alt="Foto Sebelum" class="img-fluid mb-2" style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px;">
              <div>
                <a href="{{ asset('storage/' . $tugas->foto_sebelum) }}" download class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-download"></i> Download
                </a>
              </div>
            @else
              <div class="text-muted">Tidak ada foto.</div>
            @endif
          </div>

          <div class="col-12 col-md-4">
            <div class="text-muted mb-2">Foto Pengerjaan</div>
            @if ($tugas->foto_pengerjaan)
              <img src="{{ asset('storage/' . $tugas->foto_pengerjaan) }}" alt="Foto Pengerjaan" class="img-fluid mb-2" style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px;">
              <div>
                <a href="{{ asset('storage/' . $tugas->foto_pengerjaan) }}" download class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-download"></i> Download
                </a>
              </div>
            @else
              <div class="text-muted">Tidak ada foto.</div>
            @endif
          </div>

          <div class="col-12 col-md-4">
            <div class="text-muted mb-2">Foto Sesudah</div>
            @if ($tugas->foto_sesudah)
              <img src="{{ asset('storage/' . $tugas->foto_sesudah) }}" alt="Foto Sesudah" class="img-fluid mb-2" style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px;">
              <div>
                <a href="{{ asset('storage/' . $tugas->foto_sesudah) }}" download class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-download"></i> Download
                </a>
              </div>
            @else
              <div class="text-muted">Tidak ada foto.</div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="card-footer d-flex flex-wrap gap-2">
      {{-- Aksi umum --}}
      <a class="btn btn-outline-primary" href="{{ route('tugas.edit', $tugas->id) }}">Edit</a>
      <form method="POST" action="{{ route('tugas.destroy', $tugas->id) }}" onsubmit="return confirm('Hapus tugas ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Hapus</button>
      </form>

      {{-- Workflow: Submit (pemilik) --}}
      @if (auth()->id() === $tugas->pengguna_id && in_array($tugas->status, ['DRAFT','REJECTED']))
        <form method="POST" action="{{ route('tugas.submit', $tugas->id) }}">
          @csrf
          <button class="btn btn-primary" type="submit">Submit (Pending)</button>
        </form>
      @endif

      {{-- Workflow: Approve/Reject (pengawas) --}}
      @if (auth()->user()->isPengawas() && $tugas->status === 'PENDING')
        <form method="POST" action="{{ route('tugas.approve', $tugas->id) }}">
          @csrf
          <button class="btn btn-success" type="submit">Approve</button>
        </form>
        <form method="POST" action="{{ route('tugas.reject', $tugas->id) }}">
          @csrf
          <button class="btn btn-warning" type="submit">Reject</button>
        </form>
      @endif
    </div>
  </div>
</div>
@endsection
