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
						<label> Password Lama {!! Template::required() !!} </label>
						<input type="password" name="password" class="form-control" placeholder="Masukkan password Lama">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Password Baru {!! Template::required() !!} </label>
						<input type="password" name="new_password" class="form-control" placeholder="Masukkan password Baru">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Ulangi Password Baru {!! Template::required() !!} </label>
						<input type="password" name="confirm_password" class="form-control" placeholder="Ulangi Password Baru">
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<button class="btn btn-primary" type="submit">
							<i class="fa fa-save mr-1"></i> Simpan
						</button>
					</div>

				</form>

			</div>
		</div>
	</div>

</div>
@endsection


@section('scripts')
<script>
	
	$(function(){

		const $modal = $('#modal');
		const $form = $('#form');
		const $formSubmitBtn = $form.find(`[type="submit"]`).ladda();

		const formReset = () => {
			$form[0].reset();
			$form.find(`[name="old_password"]`).focus();
		}

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();
			$formSubmitBtn.ladda('start');

			ajaxSetup();
			$.ajax({
				url: `{{ route('setting.save_password') }}`,
				method: 'POST',
				data: formData,
				dataType: 'json'
			})
			.done(response => {
				let { message } = response;
				successNotification('Berhasil', message)
				$formSubmitBtn.ladda('stop');

				formReset();
			})
			.fail(error => {
				$formSubmitBtn.ladda('stop');
				ajaxErrorHandling(error, $form);
			})
		})

		formReset();

	})

</script>
@endsection