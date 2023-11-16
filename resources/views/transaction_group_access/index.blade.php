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
						<a class="btn btn-primary mr-2" href="{{ route('transaction_group_access.create') }}">
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
								<th> Nama </th>
								<th> Email </th>
								<th> Akses Grup Transaksi </th>
							</tr>
						</thead>

						<tfoot>
							<tr>
								<th> Nama </th>
								<th> Email </th>
								<th> Akses Grup Transaksi </th>
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

		const $filterForm = $('#formFilter');

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('transaction_group_access') }}"
			},
			columns : [
				{
					data : "name",
					name : "name",
				},
				{
					data : "email",
					name : "email",
				},
				{
					data : 'transaction_group_access',
					name : 'transaction_group_access',
					orderable : false,
					searchable : false,
				}
			],
			drawCallback : settings => {
				renderedEvent();
			}
		})

		const reloadDT = () => {
			$('#dataTable').DataTable().ajax.reload();
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
							reloadDT();
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