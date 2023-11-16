@extends('layouts.template')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <span class="d-inline-block">
                        {{ $title ?? 'Judul' }}
                    </span>
                </h4>
            </div>

            <div class="card-body">
                <table class="table" style="width: 100%;">
                    <tr>
                        <td style="border-bottom: none;" width="45%">Tanggal</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $stockAdjustment->date->format('d F Y') }}</< /td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">No Transaksi</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $stockAdjustment->transaction_number }}</< /td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">Gudang</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $stockAdjustment->warehouse->warehouse_name }}</< /td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">Total Jumlah</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $stockAdjustment->total_amount }}</< /td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">Penginput</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $stockAdjustment->user->name }}</< /td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;" width="45%">Deskripsi</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $stockAdjustment->description }}</< /td>
                    </tr>

                </table>

            </div>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    <span class="d-inline-block">
                        Daftar Produk
                    </span>
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th width="100">Jumlah</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($stockAdjustmentDetails as $item)
                            <tr>
                                <td>{{ $item->product->product_name }}</td>
                                <td>{{ $item->amount }}</td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th>Product</th>
                                <th width="100">Jumlah</th>
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
    $(function(){
            $('#dataTable').DataTable()
        })
</script>
@endsection
