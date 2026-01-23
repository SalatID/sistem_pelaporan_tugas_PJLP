@extends('layout.index_admin')
@section("title","Edit Tugas")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Tugas Management</li>
    <li class="breadcrumb-item active">Edit Tugas</li>
  </ol>
@endsection
@section('content')
<div class="container-fluid">
  <h4 class="mb-3">Edit Tugas</h4>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('tugas.update', $tugas->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card">
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Nama Tugas</label>
          <input name="nama" class="form-control" value="{{ old('nama', $tugas->nama) }}" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Kategori</label>
          <select name="kategori_id" class="form-control" required>
            @foreach ($kategori as $k)
              <option value="{{ $k->id }}" @selected(old('kategori_id', $tugas->kategori_id) == $k->id)>{{ $k->nama }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Foto Bukti (opsional)</label>
          <input type="file" name="foto" class="form-control" accept="image/*">
          @if ($tugas->foto)
            <div class="mt-2">
              <div class="text-muted mb-1">Foto saat ini:</div>
              <img src="{{ asset('storage/' . $tugas->foto) }}" alt="Foto" style="max-width: 260px; border-radius: 8px;">
            </div>
          @endif
        </div>

        <div class="mb-2">
          <div class="text-muted">Status:</div>
          <span class="badge bg-secondary">{{ $tugas->status }}</span>
        </div>
      </div>

      <div class="card-footer d-flex gap-2">
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        <a class="btn btn-outline-secondary" href="{{ route('tugas.show', $tugas->id) }}">Batal</a>
      </div>
    </div>
  </form>
</div>
@endsection
