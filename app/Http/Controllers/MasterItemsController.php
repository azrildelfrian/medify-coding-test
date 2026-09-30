<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use App\Models\KategoriItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MasterItemsController extends Controller
{
    public function index()
    {
        return view('master_items.index.index');
    }

    public function search(Request $request)
    {
        $data_search = MasterItem::query()
            ->with('kategoriItems:id,kode,nama');

        if ($request->filled('kode')) {
            $data_search->where('kode', $request->kode);
        }

        if ($request->filled('nama')) {
            $data_search->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        if ($request->filled('hargamin')) {
            $data_search->where('harga_beli', '>=', $request->hargamin);
        }

        if ($request->filled('hargamax')) {
            $data_search->where('harga_beli', '<=', $request->hargamax);
        }

        if ($request->filled('kategori_id')) {
            $data_search->whereHas('kategoriItems', function ($query) use ($request) {
                $query->where('kategori_items.id', $request->kategori_id);
            });
        }

        $data_search = $data_search
            ->select(
                'id',
                'kode',
                'nama',
                'jenis',
                'harga_beli',
                'laba',
                'supplier',
                'foto',
            )
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => $data_search,
        ]);
    }

    public function formView($method, $id = 0)
    {
        if ($method == 'new') {
            $item = new MasterItem();
        } else {
            $item = MasterItem::with('kategoriItems')->findOrFail($id);
        }

        $data['item'] = $item;
        $data['method'] = $method;
        $data['kategoriItems'] = KategoriItem::all();

        return view('master_items.form.index', $data);
    }

    public function singleView($kode)
    {
        $data['data'] = MasterItem::where('kode', $kode)->first();
        return view('master_items.single.index', $data);
    }


    public function formSubmit(Request $request, $method, $id = 0)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga_beli' => 'required|numeric|min:0',
            'laba' => 'required|numeric|min:0',
            'supplier' => 'required|string',
            'jenis' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori_items' => 'nullable|array',
            'kategori_items.*' => 'exists:kategori_items,id',
        ]);

        if ($method === 'new') {
            $data_item = new MasterItem();

            $kode = str_pad(
                (MasterItem::max('id') ?? 0) + 1,
                5,
                '0',
                STR_PAD_LEFT
            );
        } else {
            $data_item = MasterItem::findOrFail($id);
            $kode = $data_item->kode;
        }

        $data_item->nama = $request->nama;
        $data_item->harga_beli = $request->harga_beli;
        $data_item->laba = $request->laba;
        $data_item->kode = $kode;
        $data_item->supplier = $request->supplier;
        $data_item->jenis = $request->jenis;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');

            $folderFoto = public_path('images/items');

            if (!is_dir($folderFoto)) {
                mkdir($folderFoto, 0755, true);
            }

            $namaFoto = Str::uuid() . '.' . $foto->extension();

            $foto->move($folderFoto, $namaFoto);

            $fotoLama = $data_item->foto;
            $data_item->foto = 'images/items/' . $namaFoto;
        }

        $data_item->save();

        $data_item->kategoriItems()->sync(
            $request->input('kategori_items', [])
        );

        if (
            $request->hasFile('foto') &&
            !empty($fotoLama)
        ) {
            $pathFotoLama = public_path($fotoLama);

            if (is_file($pathFotoLama)) {
                unlink($pathFotoLama);
            }
        }

        return redirect('master-items')
            ->with('success', 'Data item berhasil disimpan.');
    }

    public function delete($id)
    {
        MasterItem::find($id)->delete();
        return redirect('master-items');
    }

    public function updateRandomData()
    {
        $data = MasterItem::get();
        foreach ($data as $item) {
            $kode = $item->id;
            $kode = str_pad($kode, 5, '0', STR_PAD_LEFT);

            $item->harga_beli = rand(100, 1000000);
            $item->laba = rand(10, 99);
            $item->kode = $kode;
            $item->supplier = $this->getRandomSupplier();
            $item->jenis = $this->getRandomJenis();
            $item->save();
        }
    }

    private function getRandomSupplier()
    {
        $array = ['Tokopaedi', 'Bukulapuk', 'TokoBagas', 'E Commurz', 'Blublu'];
        $random = rand(0, 4);
        return $array[$random];
    }

    private function getRandomJenis()
    {
        $array = ['Obat', 'Alkes', 'Matkes', 'Umum', 'ATK'];
        $random = rand(0, 4);
        return $array[$random];
    }

    public function exportExcel()
    {
        $items = MasterItem::with('kategoriItems')
            ->orderBy('id')
            ->get();

        $namaFile = 'master-data-items-' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');

            // BOM agar karakter UTF-8 terbaca dengan baik di Excel
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No',
                'List Kategori',
                'Nama Item',
                'Supplier',
                'Harga Beli',
                'Laba (%)',
                'Harga Jual',
            ], ';');

            foreach ($items as $index => $item) {
                $hargaJual = round(
                    $item->harga_beli +
                        ($item->harga_beli * $item->laba / 100)
                );

                $listKategori = $item->kategoriItems
                    ->pluck('nama')
                    ->implode(', ');

                fputcsv($handle, [
                    $index + 1,
                    $listKategori ?: '-',
                    $item->nama,
                    $item->supplier,
                    $item->harga_beli,
                    $item->laba,
                    $hargaJual,
                ], ';');
            }

            fclose($handle);
        }, $namaFile, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
