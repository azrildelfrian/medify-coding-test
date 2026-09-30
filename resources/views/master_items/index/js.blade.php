<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>

<script>
    var start_date = '';
    var end_date = '';
    var data_per_fetch = 500;
    var data_fetched = 0;

    $(document).ready(function() {
        $('#table').DataTable({
            searching: false,
            order: [
                [0, 'desc']
            ],
        });
        getData()
    });

    $('.btn-get-data').click(function() {
        getData()
    })

    function getData() {

        $('#loading-filter').show();
        var dataTableObj = $('#table').DataTable();
        var filter_kode = $('#filter-kode').val()
        var filter_nama = $('#filter-nama').val()
        var filter_harga_min = $('#filter-harga-min').val()
        var filter_harga_max = $('#filter-harga-max').val()
        var kategoriId = new URLSearchParams(window.location.search)
            .get('kategori_id') || '';
        dataTableObj.clear().draw();

        $.ajax({
            url: '{{ url('master-items/search') }}',
            dataType: 'json',
            tryCount: 0,
            retryLimit: 3,
            data: {
                kode: filter_kode,
                nama: filter_nama,
                hargamin: filter_harga_min,
                hargamax: filter_harga_max,
                kategori_id: kategoriId
            },
            success: function(results) {
                var data = results.data

                $.each(data, function(index, item) {
                    var hargaJual = Math.round(
                        Number(item.harga_beli) +
                        Number(item.harga_beli) * Number(item.laba) / 100
                    );

                    var tombolView = `
                        <a href="{{ url('master-items/view') }}/${encodeURIComponent(item.kode)}"
                        class="btn btn-primary btn-sm">
                            View
                        </a>
                    `;

                    var kategoriHtml = '-';

                    if (item.kategori_items && item.kategori_items.length > 0) {
                        kategoriHtml = item.kategori_items.map(function(kategori) {
                            var namaKategori = $('<div>')
                                .text(kategori.nama)
                                .html();

                            return `
                        <a href="{{ url('master-items') }}?kategori_id=${kategori.id}"
                        class="badge bg-info text-dark text-decoration-none me-1">
                            ${namaKategori}
                        </a>
                    `;
                        }).join(' ');
                    }

                    var fotoHtml = '-';

                    if (item.foto) {
                        var fotoUrl = '{{ asset('') }}' + item.foto;

                        fotoHtml = `
                            <img
                                src="${fotoUrl}"
                                alt="Foto ${$('<div>').text(item.nama).html()}"
                                width="50"
                                height="50"
                                style="object-fit: cover; border-radius: 6px;"
                                onerror="this.style.display='none';"
                            >
                        `;
                    }

                    dataTableObj.row.add([
                        item.kode,
                        fotoHtml,
                        item.nama,
                        item.jenis,
                        item.harga_beli,
                        hargaJual,
                        item.supplier ?? '-',
                        kategoriHtml,
                        tombolView
                    ]);
                });

                dataTableObj.draw();
                $('#loading-filter').hide();
            },
            error: function(xhr, textStatus, errorThrown) {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) {
                    $.ajax(this);
                    return;
                }
                alert('Terjadi kesalahan server, tidak dapat mengambil data')
                $('#loading-filter').hide();

                return;
            }
        })
    }
</script>
