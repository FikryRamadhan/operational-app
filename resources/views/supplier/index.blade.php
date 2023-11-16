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
                            <button class="btn btn-success mr-2" data-toggle="modal" data-target="#modalImport">
                                <i class="fa fa-upload"></i> Import
                            </button>
                            <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#modalCreate">
                                <i class="fa fa-plus"></i> Tambah
                            </button>
                        </div>
                    </h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable">

                            <thead>
                                <tr>
                                    <th> Nama </th>
                                    <th> Email </th>
                                    <th> No.Hp </th>
                                    <th> Alamat </th>
                                    <th> Deskripsi </th>
                                    <th width="100"> Aksi </th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th> Nama </th>
                                    <th> Email </th>
                                    <th> No.Hp </th>
                                    <th> Alamat </th>
                                    <th> Deskripsi </th>
                                    <th width="100"> Aksi </th>
                                </tr>
                            </tfoot>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade " id="modalCreate" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formCreate" enctype="multipart/form-data">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-plus"></i> Tambah
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        {!! Template::requiredBanner() !!}

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Nama Supplier {!! Template::required() !!} </label>
                                    <input type="text" name="supplier_name" class="form-control"
                                        placeholder="Nama Supplier">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> No.Hp </label>
                                    <input type="number" name="phone_number" class="form-control" placeholder="Nomor Telepon">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Alamat </label>
                                    <textarea type="text" name="address" class="form-control"
                                        placeholder="Alamat"></textarea>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Email </label>
                                    <input type="email" name="email" class="form-control" placeholder="Email">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea type="text" name="description" class="form-control" placeholder="Deskripsi Product"></textarea>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Tutup
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade " id="modalUpdate" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formUpdate" enctype="multipart/form-data">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-plus"></i> Tambah
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        {!! Template::requiredBanner() !!}

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Nama Supplier {!! Template::required() !!} </label>
                                    <input type="text" name="supplier_name" class="form-control"
                                        placeholder="Nama Product">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Alamat </label>
                                    <input type="text" name="address" class="form-control"
                                        placeholder="Model Product">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>


                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> No.Hp </label>
                                    <input type="number" name="phone_number" class="form-control" placeholder="Nomor Telepon">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Email </label>
                                    <input type="email" name="email" class="form-control" placeholder="Email">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea type="text" name="description" class="form-control" placeholder="Deskripsi"></textarea>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Tutup
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalImport" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="formImport">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fa fa-upload"></i> Import
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="p-1">
                            <p class="mb-2"> Catatan : </p>
                            <ul class="pl-4">
                                <li> Import wajib menggunakan template yg kita sediakan. </li>
                                <li> Download template dengan <a
                                        href="{{ route('import_templates', 'Template_Supplier.xlsx') }}" download> Klik
                                        Disini </a>. </li>
                                <li> Kolom dengan background merah wajib diisi. </li>
                            </ul>
                        </div>


                        {!! Template::requiredBanner() !!}

                        <div class="form-group">
                            <label> File {!! Template::required() !!} </label>
                            <input type="file" name="file_excel" class="form-control">
                            <span class="invalid-feedback"></span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> Tutup
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload mr-1"></i> Import
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function(){
            const $modalCreate = $('#modalCreate');
            const $modalUpdate = $('#modalUpdate');
            const $formCreate = $('#formCreate');
            const $formUpdate = $('#formUpdate');
            const $formCreateSubmitBtn = $formCreate.find(`[type="submit"]`).ladda();
            const $formUpdateSubmitBtn = $formUpdate.find(`[type="submit"]`).ladda();

            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('supplier') }}"
                },
                columns: [{
                    data: 'supplier_name',
                    name: 'supplier_name',
                },{
                    data: 'email',
                    name: 'email',
                },{
                    data: 'phone_number',
                    name: 'phone_number',
                },{
                    data: 'address',
                    name: 'address',
                },{
                    data: 'description',
                    name: 'description',
                },{
                    data: 'action',
                    name: 'action'
                }],
                drawCallback: settings => {
                    renderedEvent()
                }
            })

            const reloadDT = () => {
                $('#dataTable').DataTable().ajax.reload()
            }

            const clearFormCreate = () => {
                $formCreate[0].reset()
            }

            const formSubmit = ($modal, $form, $submit, $href, $method, $addedAction = null) => {
                    $form.off('submit')
                    $form.on('submit', function(e) {
                        e.preventDefault();
                        clearInvalid();

                        let formData = $(this).serialize();
                        $submit.ladda('start');

                        ajaxSetup();
                        $.ajax({
                            url: $href,
                            method: $method,
                            data: formData,
                            dataType: 'json',
                        }).done(response => {
                            let {
                                message
                            } = response;
                            successNotification('Berhasil', message);
                            reloadDT();
                            clearFormCreate();
                            $submit.ladda('stop');
                            $modal.modal('hide');

                            if(addedAction) {
                                addedAction();
                            }
                        }).fail(error => {
                            $submit.ladda('stop');
				        	ajaxErrorHandling(error, $form);
                        })
                    })
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
                                        } = response
                                        successNotification('Berhasil', message)
                                        reloadDT();
                                    })
                                    .fail(error => {
                                        ajaxErrorHandling(error);
                                    })
                            })
                        })
                    })

                    $.each($('.edit'), (i, editBtn) => {
                        $(editBtn).off('click')
                        $(editBtn).on('click', function() {
                            let {
                                editHref,
                                getHref
                            } = $(this).data();
                            $.get({
                                    url: getHref,
                                    dataType: 'json'
                                })
                                .done(response => {
                                    let {
                                        supplier
                                    } = response;
                                    clearInvalid();
                                    $modalUpdate.modal('show')
                                    $formUpdate.attr('action', editHref)
                                    $formUpdate.find(`[name="supplier_name"]`).val(supplier
                                        .supplier_name);
                                    $formUpdate.find(`[name="address"]`).val(supplier.address);
                                    $formUpdate.find(`[name="phone_number"]`).val(supplier.phone_number);
                                    $formUpdate.find(`[name="email"]`).val(supplier.email);
                                    $formUpdate.find(`[name="description"]`).val(supplier.description);

                                    formSubmit(
                                        $modalUpdate,
                                        $formUpdate,
                                        $formUpdateSubmitBtn,
                                        editHref,
                                        'put'
                                    );
                                })
                                .fail(error => {
                                    ajaxErrorHandling(error);
                                })
                        })
                    })
                }

            formSubmit(
                $modalCreate,
                $formCreate,
                $formCreateSubmitBtn,
                `{{ route('supplier.store') }}`,
                'POST',
                () => {
                    clearFormCreate()
                }
            )

            let $importForm = $('#formImport')
            let $importSubmitBtn = $importForm.find(`[type="submit"]`).ladda()

            $importForm.on('submit', function(e) {
                e.preventDefault();
                clearInvalid();

                let formData = new FormData(this);
                $importSubmitBtn.ladda('start');

                ajaxSetup();
                $.ajax({
                    url: "{{ route('supplier.import') }}",
                    method: 'post',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                }).done(response => {
                    $importSubmitBtn.ladda('stop');
                    ajaxSuccessHandling(response)
                    $importForm[0].reset();
                    reloadDT()
                    $('#modalImport').modal('hide')
                }).fail(error => {
                    $importSubmitBtn.ladda('stop')
                    ajaxErrorHandling(error, $importForm)
                })
            })
        })
    </script>
@endsection
