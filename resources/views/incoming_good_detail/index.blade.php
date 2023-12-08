@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-lg-7">
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
                            <td style="border-bottom: none;">{{ $incomingGood->date->format('d F Y') }}</< /td>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;" width="45%">No Transaksi</td>
                            <td style="border-bottom: none;" width="2%">:</td>
                            <td style="border-bottom: none;">{{ $incomingGood->transaction_number }}</< /td>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;" width="45%">Gudang</td>
                            <td style="border-bottom: none;" width="2%">:</td>
                            <td style="border-bottom: none;">{{ $incomingGood->warehouse->warehouse_name }}</< /td>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;" width="45%">Supplier</td>
                            <td style="border-bottom: none;" width="2%">:</td>
                            <td style="border-bottom: none;">{{ $incomingGood->supplier->supplier_name }}</< /td>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;" width="45%">Total Jumlah</td>
                            <td style="border-bottom: none;" width="2%">:</td>
                            <td style="border-bottom: none;">{{ $incomingGood->total_amount }}</< /td>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;" width="45%">Penginput</td>
                            <td style="border-bottom: none;" width="2%">:</td>
                            <td style="border-bottom: none;">{{ $incomingGood->user->name }}</< /td>
                        </tr>
                        <tr>
                            <td style="border-bottom: none;" width="45%">Deskripsi</td>
                            <td style="border-bottom: none;" width="2%">:</td>
                            <td style="border-bottom: none;">{{ $incomingGood->description }}</< /td>
                        </tr>

                    </table>

                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-lg-7">
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
                                    <th> Foto </th>
                                    <th> Aksi </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($incomingGoodDetails as $item)
                                    <tr>
                                        <td>{{ $item->product->product_name }}</td>
                                        <td>{{ $item->amount }}</td>
                                        @if ($item->file_photo)
                                            <td><a target="_blank" class="text-success"
                                                    href="{{ asset('storage/incoming_good_detail_file_photo/' . $item->file_photo) }}">Lihat
                                                    Foto</a></td>
                                        @else
                                            <td><span class="text-danger">Tidak Melampirkan Foto</span></td>
                                        @endif
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Pilih Aksi
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item edit"
                                                        href="{{ route('incoming-good-detail.edit', $item->id) }}">
                                                        <i class="fas fa-pencil-alt mr-1" hre></i> Edit
                                                    </a>
                                                    <a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin Ingin Menghapus<strong>Data Ini</strong>" data-delete-href="{{ route('incoming-good-detail.destroy', $item->id) }}">
                                                        <i class="fas fa-trash mr-1"></i> Hapus
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th>Product</th>
                                    <th width="100">Jumlah</th>
                                    <th> Foto </th>
                                    <th> Aksi </th>
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
            $('#dataTable').DataTable()

            const reloadDT = () => {
                $('#dataTable').DataTable().ajax.reload();
            }

            $.each($('.delete'), (i, deleteBtn) => {
                $(deleteBtn).off('click')
                $(deleteBtn).on('click', function() {
                    let {
                        deleteMessage,
                        deleteHref
                    } = $(this).data();
                    confirmation(deleteMessage, function() {
                        ajaxSetup()
                        $.ajax({
                                url: deleteHref,
                                method: 'delete',
                                dataType: 'json'
                            })
                            .done(response => {
                                let {
                                    message
                                } = response
                                successNotification('Berhasil', message)
                                windowReload(500)
                            })
                            .fail(error => {
                                ajaxErrorHandling(error);
                            })
                    })
                })
            })
        })
    </script>
@endsection