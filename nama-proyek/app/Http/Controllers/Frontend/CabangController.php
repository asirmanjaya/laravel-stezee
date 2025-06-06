<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use Illuminate\Support\Facades\Response;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all();
        return view('frontend.cabangs.index', compact('cabangs'));
    }

    public function show($id)
    {
        $cabang = Cabang::with('kamars.fasilitas')->findOrFail($id);
        return view('frontend.cabangs.show', compact('cabang'));
    }

    public function tampilkanGambar($id)
    {
        $cabang = Cabang::findOrFail($id);

        if ($cabang->gambar) {
            return response($cabang->gambar)
                ->header('Content-Type', 'image/png'); // sesuaikan tipe mime jika perlu
        }

        // fallback ke placeholder jika tidak ada gambar
        return response()->file(public_path('images/placeholder.png'));
    }
}
