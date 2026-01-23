@extends('layout.index_admin')
@section("title","Jabatan List")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Master Data</li>
    <li class="breadcrumb-item active">Jabatan List</li>
  </ol>
@endsection
@section('content')
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-primary" href="{{ route('jabatan.create') }}">Tambah Jabatan</a>
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
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($items as $j)
              <tr>
                <td>{{ $j->nama }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('jabatan.show', $j->id) }}">Detail</a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('jabatan.edit', $j->id) }}">Edit</a>
                  <form class="d-inline" method="POST" action="{{ route('jabatan.destroy', $j->id) }}" onsubmit="return confirm('Hapus jabatan ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="text-center text-muted py-4">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if (method_exists($items, 'links'))
      <div class="card-footer">{{ $items->links() }}</div>
    @endif
  </div>
</div>
@endsection
