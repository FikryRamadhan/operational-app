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

                <form id="formUpdate" data-action="{{ route('stock-adjustment.update', $stockAdjustment->id) }}">
                    <div class="card-body">
                        {!! Template::requiredBanner() !!}
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> No.Transaksi {!! Template::required() !!} </label>
                                    <input type="text" name="transaction_number"
                                        value="{{ $stockAdjustment->transaction_number }}" readonly class="form-control"
                                        placeholder="No.Transaksi">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Tanggal {!! Template::required() !!}</label>
                                    <input type="date" name="date" class="form-control">
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
                                    <input type="text" name="total_amount" id="total_amount"
                                        value="{{ $stockAdjustment->total_amount }}" readonly class="form-control"
                                        placeholder="Total Jumlah">
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Deskripsi </label>
                                    <textarea type="text" name="description" class="form-control" placeholder="Deskripsi Penyesuaian Stok Barang"></textarea>
                                    <span class="invalid-feedback"></span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-hover" id="stock-adjustment-detail-table">
                                <thead>
                                    <tr>
                                        <th> Produk </th>
                                        <th> Jumlah </th>
                                        <th> Aksi </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($stockAdjustmentDetail)
                                        @foreach ($stockAdjustmentDetail as $stockAdjustmentDetail)
                                            <tr class="stock-adjustment-detail-item">
                                                <td>
                                                    <select class="stock-adjustment-details" name="id_product[]"
                                                        style="width: 100%;">
                                                        @foreach (\App\Models\Product::all() as $item)
                                                            <option value="{{ $item->id }}"
                                                                {{ $item->id == $stockAdjustmentDetail->id_product ? 'selected' : '' }}>
                                                                {{ $item->product_name }} </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="amount[]"
                                                        value="{{ $stockAdjustmentDetail->amount }}"
                                                        class="form-control amount">
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger px-2 py-1 remove"
                                                        data-url="{{ route('stock-adjustment-detail.destroy', $stockAdjustmentDetail->id) }}">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" align="right">
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
            const $formUpdate = $('#formUpdate')
            const $formUpdateSubmitBtn = $formUpdate.find(`[type="submit"]`).ladda()

            $formUpdate.find(`[name="id_warehouse"]`).select2({
                placeholder: '-- Pilih Gudang --',
                allowClear: true
            })


            $formUpdate.off('submit')
            $formUpdate.on('submit', function(e) {
                e.preventDefault();
                clearInvalid();

                let formData = $(this).serialize()
                $formUpdateSubmitBtn.ladda('start')
                let url = $formUpdate.data('action')

                ajaxSetup()
                $.ajax({
                        url: url,
                        method: 'PUT',
                        data: formData,
                        dataType: 'json'
                    })
                    .done(response => {
                        let {
                            message
                        } = response;
                        const url = "{{ route('outgoing-goods') }}"
                        redirectUrlTo(1000, url)
                        successNotification('Berhasil', message);
                        clearForm();
                        $formUpdateSubmitBtn.ladda('stop');
                    })
                    .fail(error => {
                        $formUpdateSubmitBtn.ladda('stop');
                        ajaxErrorHandling(error);
                    })
            })


            // Menampilkan Value 
            $(function() {
                $formUpdate.find(`[name="transaction_number"]`).val(
                    "{{ $stockAdjustment->transaction_number }}")
                $formUpdate.find(`[name="date"]`).val("{{ $stockAdjustment->date->format('Y-m-d') }}")
                    .trigger('change')
                $formUpdate.find(`[name="id_warehouse"]`).val("{{ $stockAdjustment->id_warehouse }}")
                    .trigger('change')
                $formUpdate.find(`[name="total_amount"]`).val("{{ $stockAdjustment->total_amount }}")
                    .trigger('change')
                $formUpdate.find(`[name="description"]`).val(`{{ $stockAdjustment->description }}`)
                    .trigger('change')
            })

            const clearForm = () => {
                $formUpdate[0].reset()
            }

            // klik tambah
            const addProductItem = () => {
                let html = $('#stock-adjustment-detail-template').text()
                $('#stock-adjustment-detail-table').find('tbody').append(html)
                renderedEvent();
                $('#stock-adjustment-detail-table').find('.stock-adjustment-details').last().val('').trigger('change')
            }

            const totalAmount = () => {
                $(function() {
                    var totalAmount = 0;

                    $('.amount').each(function() {
                        var amount = $(this).val()
                        if (amount != 0) {
                            totalAmount += parseFloat(amount)
                        }
                    })
                    $('#total_amount').val(totalAmount)
                })
            }

            const renderedEvent = () => {
                $('.stock-adjustment-details').select2({
                    placeholder: '-- Pilih Produk --',
                })

                $('.remove').off('click')
                $('.remove').on('click', function() {
                    let url = $(this).data('url')
                    $(this).parents('tr').remove()
                    renderedEvent()
                    ajaxSetup()
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        dataType: 'json'
                    })
                    totalAmount()
                })
            }

            $('#add-product-item').on('click', function(e) {
                e.preventDefault()
                addProductItem()
                $('.amount').keyup(function() {
                    totalAmount();
                })
            })

            renderedEvent()
            clearForm()

            // menampilkan Total Amount
            $('.amount').keyup(function() {
                totalAmount()
            })

        })
    </script>
    <script type="text/html" id="stock-adjustment-detail-template">
    <tr class="stock-adjustment-detail-item">
        <td>
            <select class="stock-adjustment-details" name="id_product[]" style="width: 100%;">
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
            <button class="btn btn-danger px-2 py-1 remove">
                <i class="fas fa-times"></i>
            </button>
        </td>
    </tr>
</script>
@endsection
