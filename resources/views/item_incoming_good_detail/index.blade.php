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
                                    <th>No Transaksi</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No Transaksi</th>
                                    <th>Nama Barang</th>
                                    <th>Jenis</th>
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
                serverSide:true,
                autoWidth:false,
                ajax: {
                    url: "{{ route('item-incoming-good-details') }}"
                },
                order: [[1, 'desc']],
                columnDefs: [
                    {
                        defaultContent: '-',
                        targets : '_all',
                    }
                ],
                columns: [{
                    data: 'incomingGoods.date',
                    name: 'incomingGoods.date',
                },{
                    data: 'incomingGoods.transaction_number',
                    name: 'incomingGoods.transaction_number',
                },{
                    data: 'product.product_name',
                    name: 'product.product_name',
                },{
                    data: 'product.productType.product_type_name',
                    name: 'product.productType.product_type_name',
                },{
                    data: 'amount',
                    name: 'amount',
                },
                ]
            })
        })
    </script>
@endsection









