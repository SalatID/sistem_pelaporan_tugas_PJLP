<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);
        $items = Kategori::orderBy('nama')->paginate(15);
        return view('pages.admin.kategori.index', compact('items'));
    }

    public function create(Request $request)
    {
        $this->ensureAdmin($request);
        return view('pages.admin.kategori.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);
        $data = $request->validate(['nama' => ['required', 'string', 'max:255']]);

        $auth = $request->user();
        $kategori = Kategori::create([
            'nama' => $data['nama'],
            'created_user' => $auth->id,
            'updated_user' => $auth->id,
        ]);

        return redirect()->route('kategori.show', $kategori)->with('success', 'Kategori dibuat.');
    }

    public function show(Request $request, Kategori $kategori)
    {
        $this->ensureAdmin($request);
        return view('pages.admin.kategori.show', compact('kategori'));
    }

    public function edit(Request $request, Kategori $kategori)
    {
        $this->ensureAdmin($request);
        return view('pages.admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $this->ensureAdmin($request);

        $data = $request->validate(['nama' => ['required', 'string', 'max:255']]);
        $kategori->nama = $data['nama'];
        $kategori->updated_user = $request->user()->id;
        $kategori->save();

        return redirect()->route('kategori.show', $kategori)->with('success', 'Kategori diperbarui.');
    }

    public function destroy(Request $request, Kategori $kategori)
    {
        $this->ensureAdmin($request);
        $kategori->delete();

        return redirect()->route('kategori.index')->with('success', 'Kategori dihapus.');
    }
}
