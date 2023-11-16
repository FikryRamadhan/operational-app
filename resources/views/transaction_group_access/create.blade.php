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
						<label> Staff {!! Template::required() !!} </label>
						<select class="form-control" name="id_user">
							@foreach(\App\Models\User::all() as $user)
								@if($user->isStaff())
									<option value="{{ $user->id }}"> {{ $user->name }} </option>
								@endif
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Grup Transaksi {!! Template::required() !!} </label>
						<select class="form-control" name="id_transaction_group">
							@foreach(\App\Models\TransactionGroup::all() as $transactionGroup)
								<option value="{{ $transactionGroup->id }}"> {{ $transactionGroup->transaction_group_name }} </option>
							@endforeach
						</select>
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
				url: `{{ route('transaction_group_access.store') }}`,
				method: 'post',
				data: formData,
				dataType: 'json',
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

		const resetForm = () => {
			$form[0].reset();
			$form.find(`[name="id_user"]`).val('').trigger('change')
			$form.find(`[name="id_transaction_group"]`).val('').trigger('change')
		}

		resetForm();

	})

</script>
@endsection