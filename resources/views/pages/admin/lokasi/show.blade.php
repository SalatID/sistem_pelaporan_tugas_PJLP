@extends('layout.index_admin')
@section("title","Lokasi List")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Master Data</li>
    <li class="breadcrumb-item active">Lokasi List</li>
  </ol>
@endsection
@section('content')
{{-- CONTENT ONLY: Lokasi - Show --}}
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-outline-secondary" href="{{ route('lokasi.index') }}">Kembali</a>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <div class="text-muted">Nama Lokasi</div>
          <div class="fs-5 fw-semibold">{{ $lokasi->nama }}</div>
        </div>

        <div class="col-12">
          <div class="text-muted mb-1">Alamat</div>
          <div class="border rounded p-3" style="white-space: pre-wrap;">
            {{ $lokasi->alamat ?: '-' }}
          </div>
        </div>

        <div class="col-12 col-md-6">
          <div class="text-muted">Dibuat</div>
          <div>{{ optional($lokasi->created_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>

        <div class="col-12 col-md-6">
          <div class="text-muted">Update Terakhir</div>
          <div>{{ optional($lokasi->updated_at)->format('d-m-Y H:i') ?: '-' }}</div>
        </div>
      </div>
    </div>

    <div class="card-footer d-flex gap-2">
      <a class="btn btn-outline-primary" href="{{ route('lokasi.edit', $lokasi->id) }}">Edit</a>

      <form method="POST" action="{{ route('lokasi.destroy', $lokasi->id) }}" onsubmit="return confirm('Hapus lokasi ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Hapus</button>
      </form>
    </div>
  </div>
</div>
@endsection