@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>
            {{ $method == 'create' ? 'Tambah Kategori' : 'Edit Kategori' }}
        </h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $method == 'create' ? route('kategori-items.store') : route('kategori-items.update', $kategori) }}"
            method="POST">

            @csrf

            @if ($method == 'edit')
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Kategori</label>
                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama', $kategoriItem->nama ?? '') }}" required>

                @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- <div class="mb-3">
                <label for="kode" class="form-label">Kode Kategori</label>
                <input type="text" name="kode" id="kode" class="form-control @error('kode') is-invalid @enderror"
                    value="{{ old('kode', $kategoriItem->kode ?? '') }}" required>

                @error('kode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="items">Pilih Barang</label>

                <select name="items[]" id="items" class="form-control" multiple>

                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(in_array($item->id, old('items', $selectedItems)))>
                            {{ $item->kode }} - {{ $item->nama }}
                        </option>
                    @endforeach
                </select>

                <small class="text-muted">
                    Tahan Ctrl (Windows) atau Command (Mac)
                    untuk memilih beberapa barang.
                </small>
            </div> --}}

            <button type="submit" class="btn btn-primary">
                Simpan
            </button>

            <a href="{{ route('kategori-items.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </form>
    </div>
@endsection
