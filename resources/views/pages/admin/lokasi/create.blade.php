@extends('layout.index_admin')
@section("title","Lokasi List")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Master Data</li>
    <li class="breadcrumb-item active">Lokasi List</li>
  </ol>
@endsection
@section('content')
{{-- CONTENT ONLY: Lokasi - Create --}}
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-outline-secondary" href="{{ route('lokasi.index') }}">Kembali</a>
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

  <form method="POST" action="{{ route('lokasi.store') }}">
    @csrf

    <div class="card">
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nama Lokasi</label>
          <input
            name="nama"
            class="form-control"
            value="{{ old('nama') }}"
            required
            placeholder="Contoh: Taman Kota A / Area B / Kantor UPT"
          >
        </div>

        <div class="mb-3">
          <label class="form-label">Alamat (opsional)</label>
          <textarea
            name="alamat"
            class="form-control"
            rows="4"
            placeholder="Masukkan alamat lengkap atau keterangan lokasi"
          >{{ old('alamat') }}</textarea>
          <div class="form-text">Alamat boleh dikosongkan jika tidak diperlukan.</div>
        </div>
      </div>

      <div class="card-footer d-flex gap-2">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-outline-secondary" href="{{ route('lokasi.index') }}">Batal</a>
      </div>
    </div>
  </form>
</div>
@endsection