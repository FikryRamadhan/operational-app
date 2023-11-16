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
						<input type="text" name="name" class="form-control" placeholder="Masukkan nama">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Email {!! Template::required() !!} </label>
						<input type="email" name="email" class="form-control" placeholder="Masukkan email">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nomor Telepon (Opsional) </label>
						<input type="text" name="phone_number" class="form-control" placeholder="Masukkan nomor telepon">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Role {!! Template::required() !!} </label>
						<select class="form-control" name="role">
							<option value="" selected disabled> - Pilih Role - </option>
							<option value="Staff"> Staff </option>
							<option value="Owner"> Owner </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Password {!! Template::required() !!} </label>
						<input type="password" name="password" class="form-control" placeholder="Masukkan password">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Ulangi Password {!! Template::required() !!} </label>
						<input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password">
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

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize()
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('user.store') }}`,
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
		}

		resetForm();

	})

</script>
@endsection