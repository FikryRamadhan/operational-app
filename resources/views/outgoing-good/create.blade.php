@extends('layouts.template')


@section('content')
    <div class="row">

        <div class="col-lg-11">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <span class="d-inline-block">
                            {{ $title ?? 'Judul' }}
                        </span>
                    </h4>
                </div>

                <form id="formCreate" enctype="multipart/form-data">
                    <div class="card-body">
                        {!! Template::requiredBanner() !!}
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> No.Transaksi {!! Template::required() !!} </label>
                                    <input type="text" name="transaction_number" value="{{ \App\Models\OutgoingGood::createFormatTransaksi() }}" readonly class="form-control"
                                        placeholder="No.Transaksi">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Tanggal {!! Template::required() !!}</label>
                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Gudang {!! Template::required() !!} </label>
                                    <select class="form-select" name="id_warehouse" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($warehouse as $item)
                                            <option value="{{ $item->id }}">{{ $item->warehouse_name }} </option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Total Jumlah</label>
                                    <input type="text" name="total_amount" id="total_amount" readonly
                                        class="form-control" placeholder="Total Jumlah">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Deskripsi </label>
                                    <textarea type="text" name="description" class="form-control" placeholder="Deskripsi Barang Keluar"></textarea>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-hover" id="outgoing-good-detail-table">
                                <thead>
                                    <tr>
                                        <th> Produk </th>
                                        <th> Jumlah </th>
                                        <th> Foto Produk (Opsional)</th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" align="right">
                                            <button type="button" class="btn btn-primary btn-sm" id="add-product-item">
                                                <i class="fas fa-plus mr-2"></i> Tambah
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <hr>

                        <button class="btn btn-primary ml-2" type="submit">
                            <i class="fas fa-check mr-2"></i> Simpan
                        </button </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    $(function() {
        const $formCreate = $('#formCreate')
        const $formCreateSubmitBtn = $formCreate.find(`[type="submit"]`).ladda()

        $formCreate.find(`[name="id_warehouse"]`).select2({
            placeholder: '-- Pilih Gudang --',
            allowClear: true
        })

        $formCreate.off('submit')
        $formCreate.on('submit', function(e){
            e.preventDefault()
            clearInvalid()

            let formData = new FormData(this)
            $formCreateSubmitBtn.ladda('start')

            ajaxSetup();
            $.ajax({
                url: "{{ route('outgoing-goods.store') }}",
                method: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false
            })
            .done(response => {
                const url = "{{ route('outgoing-goods') }}"
                redirectUrlTo(1000, url)
                let { message } = response;
                successNotification('Berhasil', message);
                clearForm();
                $formCreateSubmitBtn.ladda('stop');
            })
            .fail(error => {
                $formCreateSubmitBtn.ladda('stop')
                ajaxErrorHandling(error, $formCreate)
            })
        })
        
        const clearForm = () => {
            $formCreate[0].reset()
        }

        // klik tambah
        const addProductItem = () => {
            let html = $('#outgoing-good-detail-template').text()
            $('#outgoing-good-detail-table').find('tbody').append(html)
            renderedEvent();
            $('#outgoing-good-detail-table').find('outgoing-good-details').last().val('').trigger('change')
            $('#outgoing-good-detail-table').find('outgoing-good-details').last().val('').trigger('change')
        }

        const totalAmount = () => {
            $(function() {
                var totalAmount = 0;

                $('.amount').each(function(){
                    var amount = $(this).val()
                    if(amount != 0){
                        totalAmount += parseFloat(amount)
                    }
                })

                $('#total_amount').val(totalAmount)
            })
        }


        const renderedEvent = () => {
            $('.outgoing-good-details').select2({
                placeholder: '-- Pilih Product --',
            })

            $('.remove').off('click')
            $('.remove').on('click', function(){
                $(this).parents('tr').remove()
                renderedEvent()
            })
        }

        $('#add-product-item').on('click', function(e){
            e.preventDefault()
            addProductItem();
            $('.amount').keyup(function(){
                totalAmount();
            })
        })


        addProductItem()
        clearForm()

        // menampilkan total amount
        $('.amount').keyup(function(){
            totalAmount();
        })
    })
</script>
<script type="text/html" id="outgoing-good-detail-template">
    <tr class="outgoig-good-detail-item">
        <td>
            <select class="outgoing-good-details" name="id_product[]" style="width: 100%;" required>
                @foreach(\App\Models\Product::all() as $item)
                <option value="{{ $item->id }}"> {{ $item->product_name }} </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="amount[]" class="form-control amount">
            <span class="invalid-feedback"></span>
        </td>
        <td>
            <input type="file" name="file_photo[]" class="form-control file_photo">
            <span class="invalid-feedback"></span>
        </td>
        <td>
            <button class="btn btn-danger px-2 py-1 remove">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</script>
@endsection