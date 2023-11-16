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
						<label> Nama {!! Template::required() !!} </label>
						<input type="text" name="name" class="form-control" placeholder="Nama" value="{{ $reportReceiver->name }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nomor Whatsapp {!! Template::required() !!} </label>
						<input type="text" name="phone_number" class="form-control" placeholder="62xxxxxxx" value="{{ $reportReceiver->phone_number }}" required>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Catatan (Opsional) </label>
						<textarea class="form-control" name="notes" placeholder="Catatan (Opsional"> {{ $reportReceiver->notes }} </textarea>
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="table-responsive">
						<table class="table table-hover" id="transaction-group-table">
							<thead>
								<tr>
									<th> Grup Transaksi </th>
									<th> Aksi </th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
							<tfoot>
								<tr>
									<td colspan="2" align="right">
										<button type="button" class="btn btn-primary btn-sm" id="add-transaction-group">
											<i class="fas fa-plus mr-2"></i> Tambah
										</button>
									</td>
								</tr>
							</tfoot>
						</table>
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

		$form.find(`[name="name"]`).focus()

		$form.find(`[name="id_user"]`).select2({
			'placeholder': '- Pilih Staff -'
		})

		$form.find(`[name="id_transaction_group"]`).select2({
			'placeholder': '- Pilih Grup Transaksi -'
		})

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize()
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('report_receiver.update', $reportReceiver->id) }}`,
				method: 'put',
				data: formData,
				dataType: 'json',
			})
			.done(response => {
				$submitBtn.ladda('stop')
				ajaxSuccessHandling(response)
			})
			.fail(error => {
				$submitBtn.ladda('stop')
				ajaxErrorHandling(error, $form)
			})
		})

		const addTransactionGroupItem = (transactionGroupId = null) => {
			let html = $('#transaction-group-item-template').text()
			if(transactionGroupId) {
				html = html.replaceAll(`option value="${transactionGroupId}"`, `option value="${transactionGroupId}" selected`)
			}

			$('#transaction-group-table').find('tbody').append(html)
			renderEvent();

			if(!transactionGroupId) {
				$('#transaction-group-table').find('.transaction-group').last().val('').trigger('change')
			}
		}

		const renderEvent = () => {
			$('.transaction-group').select2({
				'placeholder': '- Pilih Grup Transaksi -'
			})

			$('.remove').off('click')
			$('.remove').on('click', function(){
				$(this).parents('tr').remove()
				renderEvent()
			})
		}

		$('#add-transaction-group').on('click', function(){
			addTransactionGroupItem()
		})

		@foreach($reportReceiver->reportReceiverDetails as $detail)
		addTransactionGroupItem(`{{ $detail->id_transaction_group }}`)
		@endforeach


	})

</script>

<script type="text/html" id="transaction-group-item-template">
	<tr class="transaction-group-item">
		<td>
			<select class="transaction-group" style="width: 100%;" name="id_transaction_group[]">
				@foreach(\App\Models\TransactionGroup::all() as $transactionGroup)
				<option value="{{ $transactionGroup->id }}"> {{ $transactionGroup->transaction_group_name }} </option>
				@endforeach
			</select>
		</td>
		<td>
			<button class="btn btn-danger px-2 py-1 remove">
				<i class="fas fa-times"></i>
			</button>
		</td>
	</tr>
</script>
@endsection