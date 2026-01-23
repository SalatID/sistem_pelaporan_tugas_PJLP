<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);
        $items = Lokasi::orderBy('nama')->paginate(15);
        return view('pages.admin.lokasi.index', compact('items'));
    }

    public function create(Request $request)
    {
        $this->ensureAdmin($request);
        return view('pages.admin.lokasi.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
        ]);

        $auth = $request->user();
        $lokasi = Lokasi::create([
            'nama' => $data['nama'],
            'alamat' => $data['alamat'] ?? null,
            'created_user' => $auth->id,
            'updated_user' => $auth->id,
        ]);

        return redirect()->route('lokasi.show', $lokasi)->with('success', 'Lokasi dibuat.');
    }

    public function show(Request $request, Lokasi $lokasi)
    {
        $this->ensureAdmin($request);
        return view('pages.admin.lokasi.show', compact('lokasi'));
    }

    public function edit(Request $request, Lokasi $lokasi)
    {
        $this->ensureAdmin($request);
        return view('pages.admin.lokasi.edit', compact('lokasi'));
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $this->ensureAdmin($request);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
        ]);

        $lokasi->nama = $data['nama'];
        $lokasi->alamat = $data['alamat'] ?? null;
        $lokasi->updated_user = $request->user()->id;
        $lokasi->save();

        return redirect()->route('lokasi.show', $lokasi)->with('success', 'Lokasi diperbarui.');
    }

    public function destroy(Request $request, Lokasi $lokasi)
    {
        $this->ensureAdmin($request);
        $lokasi->delete();

        return redirect()->route('lokasi.index')->with('success', 'Lokasi dihapus.');
    }
}
