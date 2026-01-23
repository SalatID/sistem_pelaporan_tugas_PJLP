{{-- CONTENT ONLY: Approvals - User Requests --}}
<div class="container-fluid">
  <h4 class="mb-3">Approval Perubahan Pengguna</h4>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Pengguna</th>
              <th>Diminta Oleh</th>
              <th>Jenis</th>
              <th>Status</th>
              <th>Diajukan</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($requests as $r)
              <tr>
                <td>{{ $r->user->nama ?? '-' }}</td>
                <td>{{ $r->requester->nama ?? '-' }}</td>
                <td>{{ $r->type }}</td>
                <td><span class="badge bg-secondary">{{ $r->status }}</span></td>
                <td>{{ optional($r->created_at)->format('d-m-Y H:i') }}</td>
                <td class="text-end">
                  <form class="d-inline" method="POST" action="{{ route('approvals.users.approve', $r->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-success" type="submit">Approve</button>
                  </form>
                  <form class="d-inline" method="POST" action="{{ route('approvals.users.reject', $r->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-warning" type="submit">Reject</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Tidak ada request pending.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if (method_exists($requests, 'links'))
      <div class="card-footer">
        {{ $requests->links() }}
      </div>
    @endif
  </div>
</div>
