@extends('layout.index_admin')
@section("title","Detail Jabatan")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Master Data</li>
    <li class="breadcrumb-item active">Detail Jabatan</li>
  </ol>
@endsection
@section('content')
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-outline-secondary" href="{{ route('jabatan.index') }}">Kembali</a>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="text-muted">Nama</div>
      <div class="fs-5 fw-semibold">{{ $jabatan->nama }}</div>
    </div>
    <div class="card-footer d-flex gap-2">
      <a class="btn btn-outline-primary" href="{{ route('jabatan.edit', $jabatan->id) }}">Edit</a>
      <form method="POST" action="{{ route('jabatan.destroy', $jabatan->id) }}" onsubmit="return confirm('Hapus jabatan ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Hapus</button>
      </form>
    </div>
  </div>
</div>
@endsection
