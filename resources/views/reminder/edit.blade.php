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
						<label> Nama Reminder {!! Template::required() !!} </label>
						<input type="text" name="reminder_name" class="form-control" placeholder="Nama Reminder" value="{{ $reminder->reminder_name }}">
						<span class="invalid-feedback"></span>
					</div>
					
					<div class="form-group">
						<label> Waktu {!! Template::required() !!} </label>
						<input type="time" name="time" class="form-control" placeholder="Waktu">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Tanggal Acara {!! Template::required() !!} </label>
						<input type="date" name="date" class="form-control" value="{{ $reminder->date }}">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Pesan (Opsional) </label>
						<textarea name="notes" class="form-control" placeholder="Nama Reminder (Opsional)" rows="3">{{ $reminder->notes }}</textarea>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Nomor Whatsapp Target Reminder {!! Template::required() !!} </label>
						<div class="mb-2">
							* Jika lebih dari 1 pisahkan dengan menggunakan simbol koma (,)
						</div>
						<input type="text" name="reminder_target" class="form-control" placeholder="Nomor Whatsapp Target Reminder" value="{{ $reminder->reminder_target }}">
						<span class="invalid-feedback"></span>
					</div>

					<hr>

					<div class="form-group">
						<label> Tambah Waktu Reminder </label>
						<div class="row mb-3">
							<div class="col-lg-8">
								<select class="form-control" id="reminder-time" style="width: 100%;">
									<option value="10" data-on="10 Menit Sebelumnya"> 10 Menit Sebelumnya </option>
									<option value="30" data-on="30 Menit Sebelumnya"> 30 Menit Sebelumnya </option>
									<option value="60" data-on="1 Jam Sebelumnya"> 1 Jam Sebelumnya </option>
									<option value="180" data-on="3 Jam Sebelumnya"> 3 Jam Sebelumnya </option>
									<option value="360" data-on="6 Jam Sebelumnya"> 6 Jam Sebelumnya </option>
									<option value="720" data-on="12 Jam Sebelumnya"> 12 Jam Sebelumnya </option>
									<option value="1440" data-on="1 Hari Sebelumnya"> 1 Hari Sebelumnya </option>
									<option value="10080" data-on="1 Minggu Sebelumnya"> 1 Minggu Sebelumnya </option>
									<option value="20160" data-on="2 Minggu Sebelumnya"> 2 Minggu Sebelumnya </option>
									<option value="43200" data-on="1 Bulan Sebelumnya"> 1 Bulan Sebelumnya </option>
									<option value="86400" data-on="2 Bulan Sebelumnya"> 2 Bulan Sebelumnya </option>
									<option value="129600" data-on="3 Bulan Sebelumnya"> 3 Bulan Sebelumnya </option>
									<option value="259200" data-on="6 Bulan Sebelumnya"> 6 Bulan Sebelumnya </option>
								</select>
							</div>
							<div class="col-lg-4">
								<button class="btn btn-primary btn-block" type="button" id="add-reminder-time">
									<i class="fas fa-plus mr-2"></i> Tambah
								</button>
							</div>
						</div>
						<div id="reminder-time-container">
						</div>
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

			let formData = $(this).serialize();
			$submitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('reminder.update', $reminder->id) }}`,
				method: 'put',
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
			$form.find(`[name="reminder_name"]`).focus()
		}

		const renderEvent = () => {
			$('.reminder-time-item').find('.remove').off('click')
			$('.reminder-time-item').find('.remove').on('click', function(){
				$(this).parents('.reminder-time-item').remove()
			})
		}

		resetForm();

		$reminderTime = $('#reminder-time')
		$reminderTimeBtn = $('#add-reminder-time')

		$form.find('#reminder-time').select2({
			'placeholder': '- Pilih Waktu Reminder -'
		})
		$form.find('#reminder-time').val('').trigger('change')

		$reminderTimeBtn.on('click', function(){
			const minutes = $reminderTime.val()
			if(minutes) {
				const check = $('#reminder-time-container').find(`[value="${minutes}"]`)
				if(check.length == 0) {
					const on = $reminderTime.find('option:selected').data('on')
					const html = $('#reminder-time-item-template').text()
									.replaceAll('{reminder_on}', on)
									.replaceAll('{reminder_minutes}', minutes)
					$('#reminder-time-container').append(html)
					$reminderTime.val('').trigger('change')
					renderEvent();
				} else {
					warningNotification('Peringatan', 'Sudah ditambahkan')
				}
			}
		})

		$form.find(`#reminder-time-container`).html($('#reminder-time-item-default-template').text())
		let detailHtml = ''
		@foreach($reminder->reminderDetails as $detail)
		@if($detail->reminder_minutes > 0)
		detailHtml = $('#reminder-time-item-template').text()
						.replaceAll('{reminder_on}', `{{ $detail->reminder_on }}`)
						.replaceAll('{reminder_minutes}', `{{ $detail->reminder_minutes }}`)
		$('#reminder-time-container').append(detailHtml)
		@endif
		@endforeach
		renderEvent()

	})

</script>

<script type="text/html" id="reminder-time-item-default-template">
	<div class="reminder-time-item">
		<span> Saat Tanggal Acara </span>
		<input type="hidden" name="reminder_detail[on][]" value="Saat Tanggal Acara">
		<input type="hidden" name="reminder_detail[minutes][]" value="0">
	</div>
</script>

<script type="text/html" id="reminder-time-item-template">
	<div class="reminder-time-item">
		<span> {reminder_on} </span>
		<span class="remove">
			<i class="fas fa-times"></i>
		</span>
		<input type="hidden" name="reminder_detail[on][]" value="{reminder_on}">
		<input type="hidden" name="reminder_detail[minutes][]" value="{reminder_minutes}">
	</div>
</script>
@endsection


@section('styles')
<style type="text/css">
	
	.reminder-time-item {
		border: 1px solid #dee2e6!important;
		padding: .5rem 1rem;
		border-radius: .25rem;
		position: relative;
		margin-bottom: 1rem;
	}

	.reminder-time-item .remove {
		background: var(--danger);
		color: white;
		padding: .1rem .5rem;
		position: absolute;
		top: -7px;
		right: -7px;
		height: 25px;
		width: 25px;
		border-radius: .25rem;
		cursor: pointer;
	}

</style>
@endsection