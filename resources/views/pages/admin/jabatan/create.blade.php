@extends('layout.index_admin')
@section("title","Create Jabatan")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Master Data</li>
    <li class="breadcrumb-item active">Create Jabatan</li>
  </ol>
@endsection
@section('content')
<div class="container-fluid">
  <h4 class="mb-3">Tambah Jabatan</h4>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('jabatan.store') }}">
    @csrf

    <div class="card">
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nama Jabatan</label>
          <input name="nama" class="form-control" value="{{ old('nama') }}" required>
        </div>
      </div>
      <div class="card-footer d-flex gap-2">
        <button class="btn btn-primary" type="submit">Simpan</button>
        <a class="btn btn-outline-secondary" href="{{ route('jabatan.index') }}">Batal</a>
      </div>
    </div>
  </form>
</div>
@endsection
