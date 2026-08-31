<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AksesLembur
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $hasRole = in_array($user?->role?->name, [
            'Admin',
            'SDM Yayasan',
            'SDM Universitas',
            'Rektor',
            'Pimpinan',
            'Staff',
        ]);

        $jenisPegawai = $user?->pegawai?->jenis_pegawai?->jenis;

        if ($jenisPegawai === 'Tenaga Pendidik') {
            abort(403, 'Unauthorized');
        }

        if (!$hasRole) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
