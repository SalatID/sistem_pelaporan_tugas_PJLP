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
            'pending' => (clone $q)->where('status', 'pending')->count(),
            'approved' => (clone $q)->where('status', 'approved')->count(),
            'rejected' => (clone $q)->where('status', 'rejected')->count(),
        ];

        $latest = (clone $q)->latest()->limit(10)->get();

        // Get per-user statistics for bar chart
        $perUserStats = Tugas::with('pengguna:id,fullname')
            ->get()
            ->groupBy('pengguna_id')
            ->map(function($tasks) {
                return [
                    'user' => $tasks->first()->pengguna->fullname ?? 'Unknown',
                    'total' => $tasks->count(),
                    'approved' => $tasks->where('status', 'approved')->count(),
                    'pending' => $tasks->where('status', 'pending')->count(),
                    'rejected' => $tasks->where('status', 'rejected')->count(),
                ];
            })
            ->values()
            ->take(10); // Limit to top 10 users

        return view('pages.admin.dashboard', compact('stats', 'latest', 'perUserStats'));
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
