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
                        <td style="border-bottom: none;" width="45%">Produk</td>
                        <td style="border-bottom: none;" width="2%">:</td>
                        <td style="border-bottom: none;">{{ $product->product_name }}</</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;">Model</td>
                        <td style="border-bottom: none;">:</td>
                        <td style="border-bottom: none;">{{ $product->model_name }}</</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;">Merek</td>
                        <td style="border-bottom: none;">:</td>
                        <td style="border-bottom: none;">{{ $product->getBrandName() }}</</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;">Jenis Produk</td>
                        <td style="border-bottom: none;">:</td>
                        <td style="border-bottom: none;">{{ $product->getProductTypeName()  }}</</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;">Minimal Stok</td>
                        <td style="border-bottom: none;">:</td>
                        <td style="border-bottom: none;">{{ $product->minimal_stock  }}</</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: none;">Deskripsi</td>
                        <td style="border-bottom: none;">:</td>
                        <td style="border-bottom: none;">{{ $product->description }}</</td>
                    </tr>

                </table>
			</div>
		</div>
	</div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body text-center">
                @if ($product->file_photo)
                    <img class="ml-4" src="{{ asset('storage/product_photo/'. $product->file_photo) }}" width="60%" style="justify-content:center" alt="">
                @else
                    <div>
                        Foto Belum Di Upload
                    </div>
                @endif
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
                        Detail Stok Di Setiap Gudang
                    </span>
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable">
                        <thead>
                            <tr>
                                <th>Gudang</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product->warehouseStock as $warehouseStock)
                                <tr>
                                    <td>{{ $warehouseStock->getWarehouseName() }}</td>
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
        $(function(){
            $('#dataTable').DataTable()
        })
    </script>
@endsection
