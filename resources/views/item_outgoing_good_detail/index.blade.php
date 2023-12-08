@extends('layouts.template')

@section('content')
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <span class="d-inline-block">
                            {{ $title ?? 'Judul' }}
                        </span>
                    </h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nomor Transaksi</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis Produk</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nomor Transaksi</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis Produk</th>
                                    <th>Jumlah</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('item-outgoing-good-detail') }}"
                },
                order: [
                    [1, 'desc']
                ],
                colomnDefs: [{
                    defaultContent: '-',
                    targets: '_all',
                }],
                columns: [{
                        data: 'outgoingGood.date',
                        name: 'outgoingGood.date',
                    },
                    {
                        data: 'outgoingGood.transaction_number',
                        name: 'outgoingGood.transaction_number',
                    },
                    {
                        data: 'product.product_name',
                        name: 'product.product_name',
                    },
                    {
                        data: 'product.productType.product_type_name',
                        name: 'product.productType.product_type_name',
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                    }
                ],
            })
        })
    </script>
@endsection









