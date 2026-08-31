<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JenisPegawai
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$jenisPegawai): Response
    {
        $jenis = Auth::user()?->pegawai?->jenis_pegawai?->jenis;
        if (!Auth::check() || !in_array($jenis, $jenisPegawai)) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}