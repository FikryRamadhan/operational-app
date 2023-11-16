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
                        <td style="border-bottom: none;" width="45%">Gudang</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $warehouse->warehouse_name }}</< /td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;">Deskripsi</td>
                        <td style="border-bottom: none;">:</td>
                        <td style="border-bottom: none;">{{ $warehouse->description }}</< /td>
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
                                <th>Produk</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($warehouse->warehouseStock as $warehouseStock)
                            <tr>
                                <td>{{ $warehouseStock->getProduct() }}</td>
                                <td>{{ $warehouseStock->stock }}</td>
                            </tr>
                            @endforeach
                        </tbody>
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
            $('#dataTable').DataTable()
        })
</script>
@endsection