@extends('layout.index_admin')
@section("title","Tugas List")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Tugas Management</li>
    <li class="breadcrumb-item active">Tugas List</li>
  </ol>
@endsection
@section('content')
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-primary" href="{{ route('tugas.create') }}">Tambah Tugas</a>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Pelapor</th>
              <th>Status</th>
              <th>Update</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($tugas as $t)
              <tr>
                <td>{{ $t->nama }}</td>
                <td>{{ $t->kategori->nama ?? '-' }}</td>
                <td>{{ $t->pengguna->nama ?? $t->pengguna->fullname ?? '-' }}</td>
                <td><span class="badge bg-secondary">{{ $t->status }}</span></td>
                <td>{{ optional($t->updated_at)->format('d-m-Y H:i') }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('tugas.show', $t->id) }}">Detail</a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('tugas.edit', $t->id) }}">Edit</a>
                  <form class="d-inline" method="POST" action="{{ route('tugas.destroy', $t->id) }}" onsubmit="return confirm('Hapus tugas ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Belum ada data tugas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if (method_exists($tugas, 'links'))
      <div class="card-footer">
        {{ $tugas->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
