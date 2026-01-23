@extends('layout.index_admin')
@section("title","Lokasi List")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Master Data</li>
    <li class="breadcrumb-item active">Lokasi List</li>
  </ol>
@endsection
@section('content')
{{-- CONTENT ONLY: Lokasi - Index --}}
<div class="container-fluid">
  <div class="mb-3 text-end">
    <a class="btn btn-primary" href="{{ route('lokasi.create') }}">Tambah Lokasi</a>
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
              <th style="width: 28%;">Nama Lokasi</th>
              <th>Alamat</th>
              <th style="width: 18%;" class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($items as $l)
              <tr>
                <td class="fw-semibold">{{ $l->nama }}</td>
                <td class="text-muted" style="white-space: pre-wrap;">
                  {{ $l->alamat ?: '-' }}
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('lokasi.show', $l->id) }}">Detail</a>
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('lokasi.edit', $l->id) }}">Edit</a>

                  <form class="d-inline" method="POST" action="{{ route('lokasi.destroy', $l->id) }}" onsubmit="return confirm('Hapus lokasi ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-4">Belum ada data lokasi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if (method_exists($items, 'links'))
      <div class="card-footer">
        {{ $items->links() }}
      </div>
    @endif
  </div>
</div>
@endsection