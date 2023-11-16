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
				<form id="form">

					{!! Template::requiredBanner() !!}
					
					<div class="form-group">
						<label> Tanggal {!! Template::required() !!} </label>
						<input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Grup Transaksi {!! Template::required() !!} </label>
						<select class="form-control" name="id_transaction_group">
							@foreach(auth()->user()->getTransactionGroups() as $transactionGroup)
							<option value="{{ $transactionGroup->id }}"> {{ $transactionGroup->transaction_group_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jenis {!! Template::required() !!} </label>
						<select class="form-control" name="type">
							<option value="Income"> Pemasukan </option>
							<option value="Expense"> Pengeluaran </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Kategori (Opsional) </label>
						<div class="small mb-2"> * Pilih Jenis Terlebih Dahulu </div>
						<select class="form-control" name="id_category"></select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Deskripsi {!! Template::required() !!} </label>
						<textarea class="form-control" name="description" placeholder="Masukkan deskripsi" rows="2"></textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nominal {!! Template::required() !!} </label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text"> Rp </span>
							</div>
							<input type="number" name="nominal" class="form-control" placeholder="Masukkan nominal transaksi">
						</div>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Bukti Transaksi (Opsional) </label>
						<div class="small mb-2">
							* Mendukung Ekstensi .pdf, .jpg, .png, .gif, .tiff <br>
							* Ukuran Maksimal 1 MB 
						</div>
						<input type="file" name="file_transaction_proof_upload" class="form-control">
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

		$form.find(`[name="id_transaction_group"]`).select2({
			'placeholder': '- Pilih Grup Transaksi -',
		})

		$form.find(`[name="type"]`).select2({
			'placeholder': '- Pilih Jenis -'
		})

		$form.find(`[name="id_category"]`).select2({
			'placeholder': '- Pilih Kategori -',
			'allowClear': true
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this)
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('transaction.store') }}`,
				method: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
				resetForm()
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		})

		$form.find(`[name="type"]`).on('change', function(){
			const type = $(this).val();

			if(type) {
				$.get({
					url: `{{ route('get_category') }}?type=${type}`,
					dataType: 'json'
				})
				.done(response => {
					let html = '';
					const { categories } = response

					categories.forEach(category => {
						html += `<option value="${category.id}"> ${category.category_name} </option>`
					})
					$form.find(`[name="id_category"]`).html(html)
					$form.find(`[name="id_category"]`).val('').trigger('change')
				})
			}
		})

		const resetForm = () => {
			$form[0].reset();
			$form.find(`[name="type"]`).val(``).trigger('change')
			$form.find(`[name="id_transaction_group"]`).val('').trigger('change')
			$form.find(`[name="id_category"]`).val('').trigger('change')
			@if(isset($_GET['type']))
			$form.find(`[name="type"]`).val(`{{ $_GET['type'] }}`).trigger('change')
			@endif
		}

		resetForm();

	})

</script>
@endsection