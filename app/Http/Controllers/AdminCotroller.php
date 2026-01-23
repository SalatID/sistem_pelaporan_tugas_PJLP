<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;

class AdminCotroller extends Controller
{
    public function dashboard(Request $request){
        $user = $request->user();

        // Contoh ringkas statistik dashboard
        $q = Tugas::query();
        if ($user->isPetugas()) {
            $q->where('pengguna_id', $user->id);
        } elseif ($user->isKordinator()) {
            // contoh: kordinator lihat tugas lokasi sendiri
            $q->whereHas('pengguna', fn($qq) => $qq->where('lokasi_id', $user->lokasi_id));
        }

        $stats = [
            'total' => (clone $q)->count(),
            'pending' => (clone $q)->where('status', 'PENDING')->count(),
            'approved' => (clone $q)->where('status', 'APPROVED')->count(),
            'rejected' => (clone $q)->where('status', 'REJECTED')->count(),
        ];

        $latest = (clone $q)->latest()->limit(10)->get();

        return view('pages.admin.dashboard', compact('stats', 'latest'));
    }
    public function index(){

    }
    public function create(){
        
    }
    public function store(){
        
    }
    public function show(){
        
    }
    public function edit(){
        
    }
    public function update(){
        
    }
    public function destroy(){
        
    }
}
