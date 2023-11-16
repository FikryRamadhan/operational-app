@extends('layouts.template')


@section('content')
<div class="row">

	<div class="col-lg-12">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title"> 
					<span class="d-inline-block">
						{{ $title ?? 'Judul' }}
					</span>
					<div class="float-right">

						<a class="btn btn-primary mr-2" href="{{ route('reminder.create') }}">
							<i class="fa fa-plus"></i> Tambah
						</a>

					</div>
				</h4>
			</div>

			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered" id="dataTable">
						
						<thead>
							<tr>
								<th> Tanggal Dibuat </th>
								<th> Nama Reminder </th>
								<th> Waktu </th>
								<th> Status </th>
								<th> Tanggal Acara </th>
								<th> Pesan </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>

						<tfoot>
							<tr>
								<th> Tanggal Dibuat </th>
								<th> Nama Reminder </th>
								<th> Waktu </th>
								<th> Status </th>
								<th> Tanggal Acara </th>
								<th> Pesan </th>
								<th> Aksi </th>
							</tr>
						</tfoot>

					</table>
				</div>
			</div>
		</div>
	</div>

</div>
@endsection


@section('scripts')
<script>
	
	$(function(){

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('reminder') }}"
			},
			columns : [
				{
					data : "created_at",
					name : "created_at",
				},
				{
					data : "reminder_name",
					name : "reminder_name",
				},
				{
					data: "time",
					name: "time",
				},
				{
					data: "status",
					name: "status",
				},
				{
					data : "date",
					name : "date",
				},
				{
					data : "notes",
					name : "notes",
				},
				{
					data : 'action',
					name : 'action',
					orderable : false,
					searchable : false,
				}
			],
			drawCallback : settings => {
				renderedEvent();
			},
			order: [[ '0', 'desc' ]]
		})

		const reloadDT = (softReload = false) => {
			if(softReload) {
				$('#dataTable').DataTable().ajax.reload(null, false);
			} else {
				$('#dataTable').DataTable().ajax.reload();
			}
		}

		const renderedEvent = () => {
			$.each($('.delete'), (i, deleteBtn) => {
				$(deleteBtn).off('click')
				$(deleteBtn).on('click', function(){
					let { deleteMessage, deleteHref } = $(this).data();
					confirmation(deleteMessage, function(){
						ajaxSetup()
						$.ajax({
							url: deleteHref,
							method: 'delete',
							dataType: 'json'
						})
						.done(response => {
							let { message } = response
							successNotification('Berhasil', message)
							reloadDT(true);
						})
						.fail(error => {
							ajaxErrorHandling(error);
						})
					})
				})
			})

			$.each($('.check'), (i, checkBtn) => {
				$(checkBtn).off('click')
				$(checkBtn).on('click', function(){
					let { checkMessage, checkHref } = $(this).data();
					confirmation(checkMessage, function(){
						ajaxSetup()
						$.ajax({
							url: checkHref,
							method: 'post',
							dataType: 'json'
						})
						.done(response => {
							let { message } = response
							successNotification('Berhasil', message)
							reloadDT(true);
						})
						.fail(error => {
							ajaxErrorHandling(error);
						})
					})
				})
			})
		}
	})

</script>
@endsection