<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DamageReportController extends Controller
{
    /**
     * Tampilkan daftar laporan fasilitas sekolah dari publik.
     */
    public function index(): View
    {
        return view('reports.index');
    }

    /**
     * Tampilkan formulir pelaporan kerusakan baru (Langkah 1).
     */
    public function create(): View
    {
        return view('reports.create');
    }
}

