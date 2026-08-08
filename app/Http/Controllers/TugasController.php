<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TugasController extends Controller
{
    // ambang batas hamming distance (dari 64 bit hash) untuk dianggap identik
    private const SIMILARITY_HAMMING_THRESHOLD = 5;

    private const FOTO_LABELS = [
        'foto_sebelum' => 'Foto Sebelum',
        'foto_pengerjaan' => 'Foto Pengerjaan',
        'foto_sesudah' => 'Foto Sesudah',
    ];

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
            'foto_sebelum' => ['nullable', 'image', 'max:2048'],
            'foto_pengerjaan' => ['nullable', 'image', 'max:2048'],
            'foto_sesudah' => ['nullable', 'image', 'max:2048'],
        ]);

        $this->assertNoSimilarImage($request, 'foto_sebelum');
        $this->assertNoSimilarImage($request, 'foto_pengerjaan');
        $this->assertNoSimilarImage($request, 'foto_sesudah');

        $foto_sebelum = null;
        $foto_pengerjaan = null;
        $foto_sesudah = null;

        if ($request->hasFile('foto_sebelum')) {
            $foto_sebelum = $request->file('foto_sebelum')->store('tugas', 'public');
        }

        if ($request->hasFile('foto_pengerjaan')) {
            $foto_pengerjaan = $request->file('foto_pengerjaan')->store('tugas', 'public');
        }

        if ($request->hasFile('foto_sesudah')) {
            $foto_sesudah = $request->file('foto_sesudah')->store('tugas', 'public');
        }

        $tugas = Tugas::create([
            'nama' => $data['nama'],
            'kategori_id' => $data['kategori_id'],
            'pengguna_id' => $user->id,
            'deskripsi' => $data['deskripsi'] ?? null,
            'foto_sebelum' => $foto_sebelum,
            'foto_pengerjaan' => $foto_pengerjaan,
            'foto_sesudah' => $foto_sesudah,
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
            'foto_sebelum' => ['nullable', 'image', 'max:2048'],
            'foto_pengerjaan' => ['nullable', 'image', 'max:2048'],
            'foto_sesudah' => ['nullable', 'image', 'max:2048'],
        ]);

        $this->assertNoSimilarImage($request, 'foto_sebelum', $tugas->id);
        $this->assertNoSimilarImage($request, 'foto_pengerjaan', $tugas->id);
        $this->assertNoSimilarImage($request, 'foto_sesudah', $tugas->id);

        // Handle foto_sebelum
        if ($request->hasFile('foto_sebelum')) {
            if ($tugas->foto_sebelum) {
                Storage::disk('public')->delete($tugas->foto_sebelum);
            }
            $tugas->foto_sebelum = $request->file('foto_sebelum')->store('tugas', 'public');
        }

        // Handle foto_pengerjaan
        if ($request->hasFile('foto_pengerjaan')) {
            if ($tugas->foto_pengerjaan) {
                Storage::disk('public')->delete($tugas->foto_pengerjaan);
            }
            $tugas->foto_pengerjaan = $request->file('foto_pengerjaan')->store('tugas', 'public');
        }

        // Handle foto_sesudah
        if ($request->hasFile('foto_sesudah')) {
            if ($tugas->foto_sesudah) {
                Storage::disk('public')->delete($tugas->foto_sesudah);
            }
            $tugas->foto_sesudah = $request->file('foto_sesudah')->store('tugas', 'public');
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

        if ($tugas->foto_sebelum) {
            Storage::disk('public')->delete($tugas->foto_sebelum);
        }
        if ($tugas->foto_pengerjaan) {
            Storage::disk('public')->delete($tugas->foto_pengerjaan);
        }
        if ($tugas->foto_sesudah) {
            Storage::disk('public')->delete($tugas->foto_sesudah);
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

    public function reject($id)
    {
        $user = auth()->user();
        $tugas = Tugas::findOrFail($id);
        // Pengawas boleh reject
        if (!$user->isPengawas()) {
            abort(403, 'Hanya Pengawas yang dapat reject.');
        }

        if ($tugas->status !== 'pending') {
            abort(422, 'Hanya tugas Pending yang dapat di-reject.');
        }

        $tugas->status = 'rejected';
        $tugas->updated_user = $user->id;
        $tugas->save();

        return redirect()->route('tugas.show', $tugas)->with('success', 'Tugas di-reject.');
    }

    // ========== Image Similarity Helpers ==========

    /**
     * Pastikan file yang diupload pada $field tidak identik dengan foto yang
     * sudah pernah tersimpan di kolom yang sama pada tabel tugas.
     */
    private function assertNoSimilarImage(Request $request, string $field, ?string $excludeId = null): void
    {
        if (!$request->hasFile($field)) {
            return;
        }

        $uploadedHash = $this->getImageHash($request->file($field)->getRealPath());
        if ($uploadedHash === null) {
            return;
        }

        $query = Tugas::query()->whereNotNull($field);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        foreach ($query->pluck($field) as $existingPath) {
            if (!$existingPath || !Storage::disk('public')->exists($existingPath)) {
                continue;
            }

            $existingHash = $this->getImageHash(Storage::disk('public')->path($existingPath));
            if ($existingHash === null) {
                continue;
            }

            if ($this->hammingDistance($uploadedHash, $existingHash) <= self::SIMILARITY_HAMMING_THRESHOLD) {
                throw ValidationException::withMessages([
                    $field => 'Tidak boleh menggunakan foto yang sama pada kolom ' . self::FOTO_LABELS[$field],
                ]);
            }
        }
    }

    /**
     * Hitung average-hash (64 bit) dari sebuah gambar untuk perbandingan kemiripan.
     */
    private function getImageHash(string $absolutePath): ?string
    {
        if (!is_file($absolutePath)) {
            return null;
        }

        $info = @getimagesize($absolutePath);
        if (!$info) {
            return null;
        }

        $image = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($absolutePath),
            IMAGETYPE_PNG => @imagecreatefrompng($absolutePath),
            IMAGETYPE_GIF => @imagecreatefromgif($absolutePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absolutePath) : null,
            default => null,
        };

        if (!$image) {
            return null;
        }

        $size = 8;
        $resized = imagecreatetruecolor($size, $size);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $size, $size, imagesx($image), imagesy($image));
        imagefilter($resized, IMG_FILTER_GRAYSCALE);

        $pixels = [];
        $total = 0;
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $gray = imagecolorat($resized, $x, $y) & 0xFF;
                $pixels[] = $gray;
                $total += $gray;
            }
        }

        imagedestroy($image);
        imagedestroy($resized);

        $average = $total / count($pixels);

        $hash = '';
        foreach ($pixels as $pixel) {
            $hash .= $pixel >= $average ? '1' : '0';
        }

        return $hash;
    }

    private function hammingDistance(string $hash1, string $hash2): int
    {
        if (strlen($hash1) !== strlen($hash2)) {
            return PHP_INT_MAX;
        }

        $distance = 0;
        for ($i = 0, $len = strlen($hash1); $i < $len; $i++) {
            if ($hash1[$i] !== $hash2[$i]) {
                $distance++;
            }
        }

        return $distance;
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
