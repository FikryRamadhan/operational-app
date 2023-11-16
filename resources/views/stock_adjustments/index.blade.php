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
                        <div class="float-right">
                            <a class="btn btn-primary mr-2" href="{{ route('stock-adjustment.create') }}">
                                <i class="fa fa-plus"></i> Tambah
                            </a>
                        </div>
                    </h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">

                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No Transaksi</th>
                                    <th>Gudang</th>
                                    <th>Deskripsi</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No Transaksi</th>
                                    <th>Gudang</th>
                                    <th>Deskripsi</th>
                                    <th width="100">Aksi</th>
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
                    url: "{{ route('stock-adjustment') }}"
                },
                order: [
                    [1, 'desc']
                ],
                columnDefs: [{
                    defaultContent: '-',
                    targets: '_all',
                }],

                columns: [{
                    data: 'date',
                    name: 'date'
                }, {
                    data: 'transaction_number',
                    name: 'transaction_number'
                }, {
                    data: 'warehouse.warehouse_name',
                    name: 'warehouse.warehouse_name'
                }, {
                    data: 'description',
                    name: 'description'
                }, {
                    data: 'action',
                    name: 'action'
                }],
                drawCallback: Settings => {
                    renderedEvent();
                }
            })

            const reloadDT = () => {
                $('#dataTable').DataTable().ajax.reload()
            }

            const renderedEvent = () => {
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
                                    } = response;
                                    successNotification('Berhasil', message)
                                    reloadDT()
                                })
                                .fail(error => {
                                    ajaxErrorHandling(error)
                                })
                        })
                    })
                })
            }

        })
    </script>
@endsection
