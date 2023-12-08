@extends('layouts.template')


@section('content')
    <div class="row">

        <div class="col-lg-10">
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
                                    <th> Produk </th>
                                    <th> Model </th>
                                    <th> Merek </th>
                                    <th> Jenis Produk </th>
                                    <th> Stok </th>
                                    <th> Foto </th>
                                    <th> Minimal Stok </th>
                                    <th> Deskripsi </th>
                                    <th width="100"> Aksi </th>
                                </tr>
                            </thead>

                            <tfoot>
                                <tr>
                                    <th> Produk </th>
                                    <th> Model </th>
                                    <th> Merek </th>
                                    <th> Jenis Produk </th>
                                    <th> Stok </th>
                                    <th> Foto </th>
                                    <th> Minimal Stok</th>
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
                                    <label> Nama Produk {!! Template::required() !!} </label>
                                    <input type="text" name="product_name" class="form-control"
                                        placeholder="Nama Produk">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Model Produk </label>
                                    <input type="text" name="model_name" class="form-control"
                                        placeholder="Model Produk">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Merek </label>
                                    <select class="form-select" name="id_brand" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($brand as $item)
                                            <option value="{{ $item->id }}">{{ $item->brand_name }} </option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Jenis Product {!! Template::required() !!} </label>
                                    <select class="form-select" name="id_product_type" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($productType as $item)
                                            <option value="{{ $item->id }}">{{ $item->product_type_name }} </option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Foto Barang </label>
                                    <input type="file" name="file_photo" class="form-control">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Minimal Stok {!! Template::required() !!} </label>
                                    <input type="number" name="minimal_stock" placeholder="Minimal Stok" class="form-control">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea type="text" name="description" class="form-control" placeholder="Deskripsi Produk"></textarea>
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
                    @method('PUT')
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
                                    <label> Nama Product {!! Template::required() !!} </label>
                                    <input type="text" name="product_name" class="form-control"
                                        placeholder="Nama Produk">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Model Product </label>
                                    <input type="text" name="model_name" class="form-control"
                                        placeholder="Model Produk">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Merek </label>
                                    <select class="form-select" name="id_brand" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($brand as $item)
                                            <option value="{{ $item->id }}">{{ $item->brand_name }} </option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Jenis Product {!! Template::required() !!} </label>
                                    <select class="form-select" name="id_product_type" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($productType as $item)
                                            <option value="{{ $item->id }}">{{ $item->product_type_name }} </option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Stok </label>
                                    <input type="number" name="stock" readonly class="form-control" placeholder="Stok" value="">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Foto Barang </label>
                                    <input type="file" name="file_photo" class="form-control">
                                    <div class="text-success valuePhoto"></div>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Minimal Stok {!! Template::required() !!} </label>
                                    <input type="number" name="minimal_stock" placeholder="Minimal Stok" class="form-control">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea type="text" name="description" class="form-control" placeholder="Deskripsi Produk"></textarea>
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
                                        href="{{ route('import_templates', 'Template_Produk.xlsx') }}" download> Klik
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
        $(function() {
            const $modalCreate = $('#modalCreate');
            const $modalUpdate = $('#modalUpdate');
            const $formCreate = $('#formCreate');
            const $formUpdate = $('#formUpdate');
            const $formCreateSubmitBtn = $formCreate.find(`[type="submit"]`).ladda();
            const $formUpdateSubmitBtn = $formUpdate.find(`[type="submit"]`).ladda();

            $formCreate.find(`[name="id_brand"]`).select2({
                dropdownParent: $modalCreate,
                placeholder: '-- Pilih Merek --',
                allowClear: true
            });

            $formCreate.find(`[name="id_product_type"]`).select2({
                dropdownParent: $modalCreate,
                placeholder: '-- Pilih Jenis Produk --',
                allowClear: true
            });

            $formUpdate.find(`[name="id_brand"]`).select2({
                dropdownParent: $modalUpdate,
                placeholder: '-- Pilih Merek --',
                allowClear: true
            });

            $formUpdate.find(`[name="id_product_type"]`).select2({
                dropdownParent: $modalUpdate,
                placeholder: '-- Pilih Jenis Produk --',
                allowClear: true
            });

            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('product') }}"
                },
                columns: [{
                    data: 'product_name',
                    name: 'product_name',
                }, {
                    data: 'model_name',
                    name: 'model_name',
                }, {
                    data: 'brand.brand_name',
                    name: 'brands.brand_name',
                    visible: false
                }, {
                    data: 'productType.product_type_name',
                    name: 'product_types.product_type_name',
                    visible: false
                }, {
                    data: 'stock',
                    name: 'stock',
                }, {
                    data: 'file_photo',
                    name: 'file_photo',
                    visible: false
                }, {
                    data: 'minimal_stock',
                    name: 'minimal_stock',
                }, {
                    data: 'description',
                    name: 'description',
                    visible: false
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                }],
                drawCallback: settings => {
                    renderedEvent()
                }
            })

            const clearFormCreate = () => {
                $formCreate[0].reset()
            }

            const reloadDT = () => {
                $('#dataTable').DataTable().ajax.reload()
            }

            const renderedEvent = () => {
                $.each($('.delete'), (i, deleteBtn) => {
                    $(deleteBtn).off('click')
                    $(deleteBtn).on('click', function(e) {
                        let {
                            deleteMessage,
                            deleteHref
                        } = $(this).data();
                        confirmation(deleteMessage, function() {
                            ajaxSetup();
                            $.ajax({
                                url: deleteHref,
                                method: 'delete',
                                dataType: 'json'
                            }).done(response => {
                                let {
                                    message
                                } = response;
                                successNotification('Berhasil', message);
                                reloadDT()
                            }).fail(error => {
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
                                    product
                                } = response;
                                clearInvalid();
                                $modalUpdate.modal('show')
                                $formUpdate.attr('action', editHref)
                                $formUpdate.find(`[name="product_name"]`).val(product
                                    .product_name);
                                $formUpdate.find(`[name="model_name"]`).val(product
                                    .model_name);
                                $formUpdate.find(`[name="stock"]`).val(product.stock);
                                $formUpdate.find(`[name="id_product_type"]`).val(product
                                    .id_product_type).trigger('change');
                                $formUpdate.find(`[name="id_brand"]`).val(product.id_brand).trigger('change');
                                $formUpdate.find(`[name="description"]`).val(product
                                    .description)
                                // $formUpdate.find('[name="file_photo"]').val("{{ asset('/storage/product_photo/') }}"+product.file_photo).trigger('change')
                                $formUpdate.find('[name="minimal_stock"]').val(product.minimal_stock).trigger('change')
                                // if(product.file_photo){
                                //     $('.valuePhoto').append('Lihat Product File Photo').trigger('change')
                                //     $linkValue = $('.valuePhoto')
                                //     $linkValue.off('click')
                                //     $linkValue.on('click', function(e) {
                                //         e.preventDefault
                                //         ajaxSetup()
                                //         $.ajax({
                                //             url: "{{ asset('/storage/product_photo/') }}"+product.file_photo,
                                //             type: 'get'
                                //         })
                                //     })
                                // }


                                    
                                formSubmit(
                                    $modalUpdate,
                                    $formUpdate,
                                    $formUpdateSubmitBtn,
                                    editHref,
                                    'POST'
                                );
                            })
                            .fail(error => {
                                ajaxErrorHandling(error);
                            })
                    })
                })
            }

            const formSubmit = ($modal, $form, $submit, $href, $method, addedAction = null) => {
                $form.off('submit')
                $form.on('submit', function(e) {
                    e.preventDefault();
                    clearInvalid();

                    let formData = new FormData(this);
                    $submit.ladda('start');

                    ajaxSetup();
                    $.ajax({
                            url: $href,
                            method: $method,
                            data: formData,
                            dataType: 'json',
                            contentType: false,
                            processData: false,
                        })
                        .done(response => {
                            let {
                                message
                            } = response;
                            successNotification('Berhasil', message)
                            $submit.ladda('stop');
                            $modal.modal('hide');
                            reloadDT();
                            $formCreate[0].reset();

                            if (addedAction) {
                                addedAction();
                            }
                        })
                        .fail(error => {
                            $submit.ladda('stop')
                            ajaxErrorHandling(error, $form)
                        })

                })
            };



            formSubmit(
                $modalCreate,
                $formCreate,
                $formCreateSubmitBtn,
                `{{ route('product.store') }}`,
                'POST',
                () => {
                    clearFormCreate();
                }
            );

            let $importForm = $('#formImport')
            let $importSubmitBtn = $importForm.find(`[type="submit"]`).ladda()

            $importForm.on('submit', function(e) {
                e.preventDefault();
                clearInvalid();

                let formData = new FormData(this);
                $importSubmitBtn.ladda('start');

                ajaxSetup();
                $.ajax({
                    url: "{{ route('product.import') }}",
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
