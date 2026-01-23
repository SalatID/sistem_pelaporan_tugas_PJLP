<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->ensurePengawas($request);

        $requests = UserChangeRequest::with(['user', 'requester'])
            ->where('status', 'PENDING')
            ->latest()
            ->paginate(15);

        return view('pages.admin.approvals.users.index', compact('requests'));
    }

    public function approve(Request $request, UserChangeRequest $userChangeRequest)
    {
        $this->ensurePengawas($request);

        if ($userChangeRequest->status !== 'PENDING') {
            abort(422, 'Request sudah diproses.');
        }

        $payload = json_decode($userChangeRequest->payload, true) ?? [];
        $user = User::findOrFail($userChangeRequest->user_id);

        // Terapkan perubahan
        $user->nama = $payload['nama'] ?? $user->nama;
        $user->nip = $payload['nip'] ?? $user->nip;
        $user->email = $payload['email'] ?? $user->email;
        $user->username = $payload['username'] ?? $user->username;
        $user->jabatan_id = $payload['jabatan_id'] ?? $user->jabatan_id;
        $user->lokasi_id = $payload['lokasi_id'] ?? $user->lokasi_id;

        if (!empty($payload['password'])) {
            $user->password = Hash::make($payload['password']);
        }

        $user->updated_user = $request->user()->id;
        $user->save();

        $userChangeRequest->status = 'APPROVED';
        $userChangeRequest->processed_by = $request->user()->id;
        $userChangeRequest->save();

        return redirect()->route('approvals.users.index')->with('success', 'Request disetujui.');
    }

    public function reject(Request $request, UserChangeRequest $userChangeRequest)
    {
        $this->ensurePengawas($request);

        if ($userChangeRequest->status !== 'PENDING') {
            abort(422, 'Request sudah diproses.');
        }

        $userChangeRequest->status = 'REJECTED';
        $userChangeRequest->processed_by = $request->user()->id;
        $userChangeRequest->save();

        return redirect()->route('approvals.users.index')->with('success', 'Request ditolak.');
    }
}
