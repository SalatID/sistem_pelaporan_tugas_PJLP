@extends('layout.index_admin')
@section("title","Detail Kategori")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Master Data</li>
    <li class="breadcrumb-item active">Detail Kategori</li>
  </ol>
@endsection
@section('content')
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-outline-secondary" href="{{ route('kategori.index') }}">Kembali</a>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="text-muted">Nama</div>
      <div class="fs-5 fw-semibold">{{ $kategori->nama }}</div>
    </div>
    <div class="card-footer d-flex gap-2">
      <a class="btn btn-outline-primary" href="{{ route('kategori.edit', $kategori->id) }}">Edit</a>
      <form method="POST" action="{{ route('kategori.destroy', $kategori->id) }}" onsubmit="return confirm('Hapus kategori ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit">Hapus</button>
      </form>
    </div>
  </div>
</div>
@endsection
