<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TugasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $q = Tugas::query()->with(['kategori', 'pengguna']);

        if ($user->isPetugas()) {
            $q->where('pengguna_id', $user->id);
        } elseif ($user->isKordinator()) {
            // Kordinator: lihat tugas berdasarkan lokasi (sesuaikan kebutuhan)
            $q->whereHas('pengguna', fn($qq) => $qq->where('lokasi_id', $user->lokasi_id));
        } // Pengawas: lihat semua

        $tugas = $q->latest()->paginate(15);

        return view('pages.admin.tugas.index', compact('tugas'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        // Semua role boleh membuat (sesuai kebutuhan)
        $kategori = Kategori::orderBy('nama')->get();

        return view('pages.admin.tugas.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'uuid', Rule::exists('kategori', 'id')],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('tugas', 'public');
        }

        $tugas = Tugas::create([
            'nama' => $data['nama'],
            'kategori_id' => $data['kategori_id'],
            'pengguna_id' => $user->id,
            'deskripsi' => $data['deskripsi'] ?? null,
            'foto' => $path,
            'status' => 'pending', // default draft
            'created_user' => $user->id,
            'updated_user' => $user->id,
        ]);

        return redirect()->route('tugas.show', $tugas)->with('success', 'Tugas berhasil dibuat (Draft).');
    }

    public function show(Request $request, Tugas $tuga) // catatan: route-model binding default plural; rename param sesuai route
    {
        $tugas = $tuga->load(['kategori', 'pengguna']);
        $user = $request->user();

        $this->authorizeView($user, $tugas);

        return view('pages.admin.tugas.show', compact('tugas'));
    }

    public function edit(Request $request, Tugas $tuga)
    {
        $tugas = $tuga;
        $user = $request->user();

        $this->authorizeEdit($user, $tugas);

        $kategori = Kategori::orderBy('nama')->get();
        return view('pages.admin.tugas.edit', compact('tugas', 'kategori'));
    }

    public function update(Request $request, Tugas $tuga)
    {
        $tugas = $tuga;
        $user = $request->user();

        $this->authorizeEdit($user, $tugas);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'uuid', Rule::exists('kategori', 'id')],
            'deskripsi' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($tugas->foto) {
                Storage::disk('public')->delete($tugas->foto);
            }
            $tugas->foto = $request->file('foto')->store('tugas', 'public');
        }

        $tugas->fill([
            'nama' => $data['nama'],
            'kategori_id' => $data['kategori_id'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'updated_user' => $user->id,
        ])->save();

        return redirect()->route('tugas.show', $tugas)->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Request $request, Tugas $tuga)
    {
        $tugas = $tuga;
        $user = $request->user();

        $this->authorizeDelete($user, $tugas);

        if ($tugas->foto) {
            Storage::disk('public')->delete($tugas->foto);
        }
        $tugas->delete();

        return redirect()->route('tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }

    // ========== Workflow Status ==========

    public function approve($id)
    {
        $user = auth()->user();
        $tugas = Tugas::findOrFail($id);
        // Pengawas boleh approve; Kordinator opsional (silakan atur)
        if (!$user->isPengawas()) {
            abort(403, 'Hanya Pengawas yang dapat approve.');
        }

        if ($tugas->status !== 'pending') {
            abort(422, 'Hanya tugas Pending yang dapat di-approve.');
        }

        $tugas->status = 'approved';
        $tugas->updated_user = $user->id;
        $tugas->save();

        return redirect()->route('tugas.show', $tugas)->with('success', 'Tugas di-approve.');
    }

    // ========== Authorization Helpers ==========

    private function authorizeView($user, Tugas $tugas): void
    {
        if ($user->isPengawas()) return;

        if ($user->isPetugas() && $tugas->pengguna_id !== $user->id) {
            abort(403);
        }

        if ($user->isKordinator()) {
            // kordinator lihat tugas lokasi sendiri
            if ($tugas->pengguna?->lokasi_id !== $user->lokasi_id) {
                abort(403);
            }
        }
    }

    private function authorizeEdit($user, Tugas $tugas): void
    {
        // aturan dokumen: tugas APPROVED tidak bisa diubah/dihapus oleh kordinator
        if ($tugas->status === 'APPROVED' && !$user->isPengawas()) {
            abort(403, 'Tugas sudah di-approve dan tidak dapat diubah.');
        }

        // petugas hanya boleh edit miliknya
        if ($user->isPetugas() && $tugas->pengguna_id !== $user->id) {
            abort(403);
        }

        // kordinator boleh edit tugas lokasi sendiri
        if ($user->isKordinator() && $tugas->pengguna?->lokasi_id !== $user->lokasi_id) {
            abort(403);
        }
    }

    private function authorizeDelete($user, Tugas $tugas): void
    {
        // sama seperti edit
        $this->authorizeEdit($user, $tugas);
    }
}
