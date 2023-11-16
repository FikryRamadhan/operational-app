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

					<div class="float-right">

						<a class="btn btn-warning btn-sm" href="{{ route('reminder.edit', $reminder->id) }}">
							<i class="fa fa-pencil-alt"></i> Edit
						</a>

					</div>
				</h4>
			</div>

			<div class="card-body">

				<div class="form-group">
					<label> Nama Reminder </label>
					<div> {{ $reminder->reminder_name }} </div>
				</div>
					
				<div class="form-group">
					<label> Tanggal Acara </label>
					<div> {{ $reminder->dateFormatted() }} </div>
				</div>

				<div class="form-group">
					<label> Pesan </label>
					<div> {!! $reminder->notesHtml() !!} </div>
				</div>

				<div class="form-group">
					<label> Nomor Whatsapp Target Reminder </label>
					<div>
						@foreach($reminder->reminderTarget() as $target)
						<div class="py-2 px-3 border rounded d-inline-block"> {{ $target }} </div>
						@endforeach
					</div>
				</div>

				<div class="form-group">
					<label> Waktu Reminder </label>
					<div>
						@foreach($reminder->reminderDetails as $detail)
						<div class="py-2 px-3 mb-2 mr-2 border rounded">
							<b>{{ $detail->reminder_on }}</b> (Akan diingatkan pada <b>{{ $detail->reminderTimeFormatted('d F Y H:i') }}</b>)
							@if($detail->isPassed())
							<i class="fas fa-check-circle text-success"></i>
							@endif
						</div>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
@endsection