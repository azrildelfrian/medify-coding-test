<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Kategori Item</title>

    <style>
        @page {
            margin: 30px 30px 45px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 20px;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 7px;
            vertical-align: top;
        }

        th {
            background-color: #e9ecef;
            text-align: center;
        }

        .text-center {
            text-align: center;
        }

        .item-list {
            margin: 0;
            padding-left: 18px;
        }

        .item-list li {
            margin-bottom: 4px;
        }
    </style>
</head>

<body>

    <h2>DAFTAR KATEGORI ITEM</h2>
    <div class="subtitle">
        Daftar kategori beserta item yang terdaftar
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 15%;">Kode Kategori</th>
                <th style="width: 20%;">Nama Kategori</th>
                <th style="width: 60%;">Daftar Item</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($kategoriItems as $kategori)
                <tr>
                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $kategori->kode }}
                    </td>

                    <td>
                        {{ $kategori->nama }}
                    </td>

                    <td>
                        @forelse ($kategori->items as $item)
                            <div>
                                {{ $item->kode }} - {{ $item->nama }}
                            </div>
                        @empty
                            <em>Belum ada item</em>
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">
                        Belum ada data kategori.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
