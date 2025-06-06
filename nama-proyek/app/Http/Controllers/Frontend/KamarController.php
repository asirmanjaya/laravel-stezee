<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Kamar;

class KamarController extends Controller
{
    public function show($id)
    {
        // Ambil data kamar dengan relasi fasilitas dan cabang (untuk alamat)
        $kamar = Kamar::with(['fasilitas', 'cabang'])->findOrFail($id);

        // Kirim data ke view
        return view('frontend.kamars.show', compact('kamar'));
    }
}
