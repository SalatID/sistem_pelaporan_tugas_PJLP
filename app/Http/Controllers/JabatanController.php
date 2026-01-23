<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->ensurePengawas($request);
        $items = Jabatan::orderBy('nama')->paginate(15);
        return view('pages.admin.jabatan.index', compact('items'));
    }

    public function create(Request $request)
    {
        $this->ensurePengawas($request);
        return view('pages.admin.jabatan.create');
    }

    public function store(Request $request)
    {
        $this->ensurePengawas($request);
        $data = $request->validate(['nama' => ['required', 'string', 'max:255']]);

        $auth = $request->user();
        $jabatan = Jabatan::create([
            'nama' => $data['nama'],
            'created_user' => $auth->id,
            'updated_user' => $auth->id,
        ]);

        return redirect()->route('jabatan.show', $jabatan)->with('success', 'Jabatan dibuat.');
    }

    public function show(Request $request, Jabatan $jabatan)
    {
        $this->ensurePengawas($request);
        return view('pages.admin.jabatan.show', compact('jabatan'));
    }

    public function edit(Request $request, Jabatan $jabatan)
    {
        $this->ensurePengawas($request);
        return view('pages.admin.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $this->ensurePengawas($request);

        $data = $request->validate(['nama' => ['required', 'string', 'max:255']]);
        $jabatan->nama = $data['nama'];
        $jabatan->updated_user = $request->user()->id;
        $jabatan->save();

        return redirect()->route('jabatan.show', $jabatan)->with('success', 'Jabatan diperbarui.');
    }

    public function destroy(Request $request, Jabatan $jabatan)
    {
        $this->ensurePengawas($request);
        $jabatan->delete();

        return redirect()->route('jabatan.index')->with('success', 'Jabatan dihapus.');
    }
}
