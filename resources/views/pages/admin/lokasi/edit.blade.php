@extends('layout.index_admin')
@section("title","Lokasi List")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Master Data</li>
    <li class="breadcrumb-item active">Lokasi List</li>
  </ol>
@endsection
@section('content')
{{-- CONTENT ONLY: Lokasi - Edit --}}
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-outline-secondary" href="{{ route('lokasi.show', $lokasi->id) }}">Kembali</a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('lokasi.update', $lokasi->id) }}">
    @csrf
    @method('PUT')

    <div class="card">
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nama Lokasi</label>
          <input
            name="nama"
            class="form-control"
            value="{{ old('nama', $lokasi->nama) }}"
            required
          >
        </div>

        <div class="mb-3">
          <label class="form-label">Alamat (opsional)</label>
          <textarea
            name="alamat"
            class="form-control"
            rows="4"
          >{{ old('alamat', $lokasi->alamat) }}</textarea>
        </div>
      </div>

      <div class="card-footer d-flex gap-2">
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        <a class="btn btn-outline-secondary" href="{{ route('lokasi.show', $lokasi->id) }}">Batal</a>
      </div>
    </div>
  </form>
</div>
@endsection