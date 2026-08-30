<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use Illuminate\Http\Request;

class LowonganController extends Controller
{
    public function index(Request $request)
    {
        $query = Lowongan::where('is_active', true);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%")
                    ->orWhere('jurusan', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('jurusan')) {
            $query->where('jurusan', $request->jurusan);
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'company') $query->orderBy('company');
        elseif ($sort === 'type') $query->orderBy('type');
        else $query->latest();

        return response()->json($query->get());
    }

    public function show(Lowongan $lowongan)
    {
        return response()->json($lowongan);
    }

    public function count()
    {
        return response()->json(['total' => Lowongan::where('is_active', true)->count()]);
    }
}
