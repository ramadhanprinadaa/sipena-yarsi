<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPendidikan;
use Illuminate\Support\Facades\Storage;

class RiwayatPendidikanPreviewController extends Controller
{
    public function __invoke(RiwayatPendidikan $riwayatPendidikan)
    {
        $path = "{$riwayatPendidikan->file_path}/{$riwayatPendidikan->file_ijazah}";

        if (!$riwayatPendidikan->file_ijazah || !Storage::exists($path)) {
            abort(404, 'File ijazah tidak ditemukan.');
        }

        return response()->file(Storage::path($path), [
            'Content-Disposition' => 'inline; filename="' . $riwayatPendidikan->file_ijazah . '"',
        ]);
    }
}