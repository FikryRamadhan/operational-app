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
				<form id="form" enctype="multipart/form-data">
					{!! Template::requiredBanner() !!}
					@method('PUT')

					<div class="form-group">
						<label> Produk {!! Template::required() !!} </label>
						<select class="form-control" name="id_product">
							@foreach($product as $p)
							    <option value="{{ $p->id }}" {{ $incomingGoodDetail->id_product == $p->id ? 'selected':'' }}> {{ $p->product_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" class="form-control" name="amount" value="{{ $incomingGoodDetail->amount }}" placeholder="Jumlah Produk">
                        <span class="invalid-feedback"></span>
                    </div>

                    <div class="form-group">
                        <label>Foto (Opsional)</label>
                        <input type="file" class="form-control" name="file_photo" placeholder="Jumlah Produk">
                        <span class="invalid-feedback"></span>
                    </div>

					<hr>

					<button class="btn btn-primary" type="submit">
						<i class="fas fa-check mr-2"></i> Simpan
					</button>

				</form>
			</div>
		</div>
	</div>

</div>
@endsection


@section('scripts')
<script>
	
	$(function(){
		const $form = $('#form');
		const $submitBtn = $form.find(`[type="submit"]`).ladda();

		$form.find(`[name="id_product"]`).select2({
			'placeholder': '-- Pilih Produk --',
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this)
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('incoming-good-detail.update', $incomingGoodDetail->id) }}`,
				method: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
                redirectUrlTo(500, `{{ route('incoming-goods.detail', $incomingGoodDetail->id_incoming_good) }}`)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		})
	})

</script> 
@endsection