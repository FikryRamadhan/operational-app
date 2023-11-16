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

					<div class="text-center my-2">
						<img src="{{ auth()->user()->avatarLink() }}" id="change-avatar">
					</div>

					<input type="file" name="upload_avatar" style="display: none;" accept="image/*">

					<div class="form-group">
						<label> Nama Lengkap {!! Template::required() !!} </label>
						<input type="text" name="name" class="form-control" placeholder="Masukkan Nama Lengkap" value="{{ auth()->user()->name }}">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Email {!! Template::required() !!} </label>
						<p class="small text-muted">
							* Digunakan untuk login
						</p>
						<input type="email" name="email" class="form-control" placeholder="Masukkan Email" value="{{ auth()->user()->email }}">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nomor Telepon {!! Template::required() !!} </label>
						<p class="small text-muted">
							* Digunakan untuk menerima laporan kas mingguan dan bulanan, atau keperluan lain
						</p>
						<input type="text" name="phone_number" class="form-control" placeholder="Masukkan Nomor Telepon" value="{{ auth()->user()->phone_number }}">
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

		$form.find(`[name="name"]`).focus();

		$form.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = new FormData(this);
			$formSubmitBtn.ladda('start');

			ajaxSetup();
			$.ajax({
				url: `{{ route('setting.save_profile') }}`,
				method: 'POST',
				data: formData,
				dataType: 'json',
				contentType: false,
				processData: false
			})
			.done(response => {
				let { message } = response;
				successNotification('Berhasil', message)
				setTimeout(() => {
					window.location.reload()
				}, 1000)

				formReset();
			})
			.fail(error => {
				$formSubmitBtn.ladda('stop');
				ajaxErrorHandling(error, $form);
			})
		})


		$form.find('#change-avatar').on('click', function(){
			$form.find(`[name="upload_avatar"]`).click()
		})

		$form.find('[name="upload_avatar"]').on('change', function(){
			let file = $(this).val();
			
			if(!isEmpty(file)) {
				let fileType = this.files[0].type;

				if(fileType.substring(0, 5) != "image") {
					toastrAlert();
					toastr.warning('File harus berupa foto', 'Peringatan')
					$(this).val('');
				} else {
					let reader = new FileReader();

					reader.onload = function(e) {
						$form.find('#change-avatar').attr('src', e.target.result);
					}
					reader.readAsDataURL(this.files[0]);
					$form.find('#change-avatar').show();
				}
			} else {
				$form.find('#change-avatar').hide();
			}
		});

	})

</script>
@endsection


@section('styles')
<style type="text/css">
	#change-avatar {
		width: 150px;
		height: 150px;
		object-fit: cover;
		object-position: center;
		border-radius: 100%;
		border: 1px solid #cdcdcd;
		cursor: pointer;
	}
</style>
@endsection