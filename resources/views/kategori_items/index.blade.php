@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Kategori Item</h3>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('kategori-items.create') }}" class="btn btn-primary mb-3">
            Tambah Kategori
        </a>

        <a href="{{ route('kategori-items.pdf') }}" class="btn btn-danger mb-3">
            Download PDF
        </a>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Kategori</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Item</th>
                        <th>Daftar Item</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($kategoriItems as $kategori)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $kategori->kode }}</td>

                            <td>{{ $kategori->nama }}</td>

                            <td>
                                <span class="badge bg-primary">
                                    {{ $kategori->items_count }} item
                                </span>
                            </td>

                            <td>
                                @forelse ($kategori->items as $item)
                                    <div class="mb-1">
                                        <span class="badge bg-secondary">
                                            {{ $item->kode }}
                                        </span>
                                        {{ $item->nama }}
                                    </div>
                                @empty
                                    <span class="text-muted">
                                        Belum ada item
                                    </span>
                                @endforelse
                            </td>

                            <td>
                                <a href="{{ route('kategori-items.edit', $kategori) }}" class="btn btn-warning btn-sm mb-1">
                                    Edit
                                </a>

                                <form action="{{ route('kategori-items.destroy', $kategori) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm mb-1">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                Belum ada kategori.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
