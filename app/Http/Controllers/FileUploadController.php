<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,pdf|max:2048',
        ]);

        $path = $request->file('file')->store('uploads','public');

        return back()->with('success', 'File berhasil diupload.')->with('file', $path);
    }

    public function destroy(Request $request)
    {
        Storage::delete('public/' . $request->file);

        return back()->with('success', 'File berhasil dihapus!');
    }
}