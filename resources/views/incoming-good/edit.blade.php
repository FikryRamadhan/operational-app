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

            <form id="formUpdate" data-action="{{ route('incoming-goods.update', $incomingGood->id) }}"
                enctype="multipart/form-data">
                @method('PUT')
                <div class="card-body">
                    {!! Template::requiredBanner() !!}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label> No.Transaksi {!! Template::required() !!} </label>
                                <input type="text" readonly name="transaction_number" class="form-control"
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
                                <label> Supplier {!! Template::required() !!}</label>
                                <select class="form-select" name="id_supplier" style="width: 100%">
                                    <option value=""></option>
                                    @foreach ($supplier as $item)
                                    <option value="{{ $item->id }}">{{ $item->supplier_name }} </option>
                                    @endforeach
                                </select>
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
                                <input type="text" name="total_amount" id="total_amount" readonly class="form-control"
                                    placeholder="Total Jumlah">
                                <span class="invalid-feedback"></span>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label> Deskripsi </label>
                                <textarea type="text" name="description" class="form-control"
                                    placeholder="Deskripsi Barang Masuk"></textarea>
                                <span class="invalid-feedback"></span>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <button class="btn btn-primary ml-2" type="submit">
                        <i class="fas fa-check mr-2"></i> Simpan
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
            const $formUpdate = $('#formUpdate')
            const $formUpdateSubmitBtn = $formUpdate.find(`[type="submit"]`).ladda()

            $formUpdate.find(`[name="id_supplier"]`).select2({
                placeholder: '-- Pilih Supplierr --',
                allowClear: true
            })

            $formUpdate.find(`[name="id_warehouse"]`).select2({
                placeholder: '-- Pilih Gudang --',
                allowClear: true
            })

            // Menampilkan Value Data Sesuai Id
            $(function() {
                $formUpdate.find(`[name="transaction_number"]`).val(
                    "{{ $incomingGood->transaction_number }}")
                $formUpdate.find(`[name="date"]`).val("{{ $incomingGood->date->format('Y-m-d') }}").trigger(
                    'change')
                $formUpdate.find(`[name="id_supplier"]`).val("{{ $incomingGood->id_supplier }}").trigger(
                    'change')
                $formUpdate.find(`[name="id_warehouse"]`).val("{{ $incomingGood->id_warehouse }}").trigger(
                    'change')
                $formUpdate.find(`[name="total_amount"]`).val("{{ $incomingGood->total_amount }}").trigger(
                    'change')
                $formUpdate.find(`[name="description"]`).val(`{{ $incomingGood->description }}`).trigger(
                    'change')
            })
            
            $formUpdate.off('submit')
            $formUpdate.on('submit', function(e) {
                e.preventDefault()

                let url = $formUpdate.data('action')
                let formData = new FormData(this)
                $formUpdateSubmitBtn.ladda('start')

                ajaxSetup()
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                }).done(response => {
                    let {
                        message
                    } = response;
                    const url = "{{ route('incoming-goods') }}"
                    redirectUrlTo(1000, url)
                    successNotification('Berhasil', message);
                    clearForm();
                    $formUpdateSubmitBtn.ladda('stop');
                    
                }).fail(error => {
                    $formUpdateSubmitBtn.ladda('stop');
                    ajaxErrorHandling(error);
                })
            })


            const clearForm = () => {
                $formUpdate[0].reset()
            }
        })
</script>
@endsection