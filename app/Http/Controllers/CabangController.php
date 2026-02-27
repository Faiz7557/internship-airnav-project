<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\DailyFlightStat;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang' => 'required|unique:cabangs,kode_cabang',
            'nama' => 'required'
        ]);

        Cabang::create([
            'kode_cabang' => strtoupper(trim($request->kode_cabang)),
            'nama' => trim($request->nama)
        ]);

        return back()->with('success', 'Cabang baru berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_cabang' => 'required|unique:cabangs,kode_cabang,'.$id,
            'nama' => 'required'
        ]);

        $cabang = Cabang::findOrFail($id);
        $oldKode = $cabang->kode_cabang;
        $newKode = strtoupper(trim($request->kode_cabang));

        if ($oldKode !== $newKode) {
            DailyFlightStat::where('branch_code', $oldKode)->update(['branch_code' => $newKode]);
        }

        $cabang->update([
            'kode_cabang' => $newKode,
            'nama' => trim($request->nama)
        ]);

        return back()->with('success', 'Data cabang berhasil diperbarui!');
    }
}
