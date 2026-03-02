@extends('layout.index_admin')
@section("title","Dashboard")
@section('breadcrumb')
<ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item active">Dashboard</li>
  </ol>
@endsection
@section('content')
    
{{-- CONTENT ONLY: Dashboard --}}
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Dashboard</h4>
    <div class="text-muted">Hi, {{ auth()->user()->nma ?? auth()->user()->name ?? 'User' }}</div>
  </div>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-muted">Total Tugas</div>
          <div class="fs-4 fw-semibold">{{ $stats['total'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-muted">Pending</div>
          <div class="fs-4 fw-semibold">{{ $stats['pending'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-muted">Approved</div>
          <div class="fs-4 fw-semibold">{{ $stats['approved'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="card">
        <div class="card-body">
          <div class="text-muted">Rejected</div>
          <div class="fs-4 fw-semibold">{{ $stats['rejected'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <div class="fw-semibold">Ringkasan Tugas Per Pengguna</div>
    </div>
    <div class="card-body">
      <div style="height: 400px;">
        <canvas id="userTaskChart"></canvas>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
      <div class="fw-semibold">Tugas Terbaru</div>
      <a class="btn btn-sm btn-outline-primary" href="{{ route('tugas.index') }}">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Kategori</th>
              <th>Pelapor</th>
              <th>Status</th>
              <th>Dibuat</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($latest as $t)
              <tr>
                <td>{{ $t->nama }}</td>
                <td>{{ $t->kategori->nama ?? '-' }}</td>
                <td>{{ $t->pengguna->nma ?? '-' }}</td>
                <td>
                  <span class="badge {{ $t->getStatusBadgeClass() }}">{{ $t->status }}</span>
                </td>
                <td>{{ optional($t->created_at)->format('d-m-Y H:i') }}</td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('tugas.show', $t->id) }}">Detail</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
  // Prepare data for chart
  const perUserStats = @json($perUserStats);
  
  if (perUserStats.length > 0) {
    const userNames = perUserStats.map(item => item.user);
    const totalData = perUserStats.map(item => item.total);
    const approvedData = perUserStats.map(item => item.approved);
    const pendingData = perUserStats.map(item => item.pending);
    const rejectedData = perUserStats.map(item => item.rejected);

    var ctx = document.getElementById('userTaskChart').getContext('2d');

    new Chart(ctx, {
      type: 'horizontalBar',
      data: {
        labels: userNames,
        datasets: [
          {
            label: 'Total Tugas',
            data: totalData,
            backgroundColor: '#dc3545'
          },
          {
            label: 'Approved',
            data: approvedData,
            backgroundColor: '#28a745'
          },
          {
            label: 'Pending',
            data: pendingData,
            backgroundColor: '#ffc107'
          },
          {
            label: 'Rejected',
            data: rejectedData,
            backgroundColor: '#007bff'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          xAxes: [{
            stacked: true,
            ticks: {
              callback: function(value) {
                return value;
              }
            }
          }],
          yAxes: [{
            stacked: true
          }]
        },
        tooltips: {
          mode: 'index',
          intersect: false,
          callbacks: {
            label: function(tooltipItem, data) {
              return data.datasets[tooltipItem.datasetIndex].label +
                ': ' + tooltipItem.xLabel;
            }
          }
        },
        legend: {
          position: 'top'
        },
        title: {
          display: true,
          text: 'Ringkasan Tugas Per Pengguna'
        }
      }
    });
  }
</script>
@endsection