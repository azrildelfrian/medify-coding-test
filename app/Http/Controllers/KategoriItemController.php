<?php

namespace App\Http\Controllers;

use App\Models\KategoriItem;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class KategoriItemController extends Controller
{

    public function index()
    {
        $kategoriItems = KategoriItem::with('items:id,kode,nama')
            ->withCount('items')
            ->get();

        return view('kategori_items.index', compact('kategoriItems'));
    }

    public function create()
    {
        $items = MasterItem::orderBy('nama')->get();

        return view('kategori_items.form', [
            'kategori' => new KategoriItem(),
            'items' => $items,
            'selectedItems' => [],
            'method' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'items' => 'nullable|array',
            'items.*' => 'exists:master_items,id',
        ]);

        $kode = (KategoriItem::max('id') ?? 0) + 1;
        $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

        $kategori = KategoriItem::create([
            'kode' => $kode,
            'nama' => $validated['nama'],
        ]);

        $kategori->items()->sync($validated['items'] ?? []);

        return redirect()
            ->route('kategori-items.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show($id)
    {
        //
    }

    public function edit(KategoriItem $kategori_item)
    {
        $items = MasterItem::orderBy('nama')->get();

        return view('kategori_items.form', [
            'kategori' => $kategori_item,
            'items' => $items,
            'selectedItems' => $kategori_item->items()
                ->pluck('master_items.id')
                ->toArray(),
            'method' => 'edit',
        ]);
    }

    public function update(Request $request, KategoriItem $kategoriItem)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'items' => 'nullable|array',
            'items.*' => 'exists:master_items,id',
        ]);

        $kategoriItem->update([
            'nama' => $validated['nama'],
        ]);

        $kategoriItem->items()->sync($validated['items'] ?? []);

        return redirect()
            ->route('kategori-items.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }


    public function downloadPdf()
    {
        $kategoriItems = KategoriItem::with('items:id,kode,nama')
            ->orderBy('kode')
            ->get();

        $tanggalCetak = now()->format('d-m-Y H:i:s');

        $pdf = Pdf::loadView('kategori_items.pdf', [
            'kategoriItems' => $kategoriItems,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $pdf->render();

        $canvas = $pdf->getDomPDF()->getCanvas();

        $canvas->page_text(
            40,
            575,
            'Tanggal cetak: ' . $tanggalCetak,
            null,
            9,
            [0, 0, 0]
        );

        $canvas->page_text(
            745,
            575,
            'Halaman {PAGE_NUM} dari {PAGE_COUNT}',
            null,
            9,
            [0, 0, 0]
        );

        return $pdf->download('daftar-kategori-item.pdf');
    }

    public function destroy(KategoriItem $kategoriItem)
    {
        $kategoriItem->items()->detach();

        $kategoriItem->delete();

        return redirect()
            ->route('kategori-items.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
