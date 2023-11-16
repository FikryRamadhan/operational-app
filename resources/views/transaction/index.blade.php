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

						@if(auth()->user()->isOwner())
						<div class="dropdown d-inline-block mr-2">
							<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: .4rem 1rem !important;">
								Verifikasi Transaksi
							</button>
							<div class="dropdown-menu">
								<a class="dropdown-item verify-selected" href="javascript:void(0)" data-verify-message="Yakin ingin memverifikasi transaksi yg dipilih?">
									<i class="fas fa-check-square mr-1"></i> Verifikasi Yang Dipilih
								</a>
								<a class="dropdown-item verify-all" href="javascript:void(0)" data-verify-message="Yakin ingin memverifikasi semua transaksi?">
									<i class="fas fa-check mr-1"></i> Verifikasi Semua
								</a>
							</div>
						</div>
						@endif

						<button class="btn btn-info mr-2" data-toggle="modal" data-target="#modalFilter">
							<i class="fa fa-filter"></i> Filter
						</button>

						@if(auth()->user()->isStaff())
						<button class="btn btn-success mr-2" data-toggle="modal" data-target="#modalImport">
							<i class="fa fa-upload"></i> Import
						</button>
						<a class="btn btn-primary mr-2" href="{{ route('transaction.create') }}">
							<i class="fa fa-plus"></i> Tambah
						</a>
						@endif
					</div>
				</h4>
			</div>

			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered" id="dataTable">

						<thead>
							<tr>
								@if(auth()->user()->isOwner())
								<th width="30"> </th>
								@endif
								<th> Tanggal </th>
								<th> Deskripsi </th>
								<th> Nominal </th>
								<th> Bukti Pembayaran </th>
								<th> Status Verifikasi </th>
								<th width="100"> Aksi </th>
							</tr>
						</thead>

						<tfoot>
							<tr>
								@if(auth()->user()->isOwner())
								<th> </th>
								@endif
								<th> Tanggal </th>
								<th> Deskripsi </th>
								<th> Nominal </th>
								<th> Bukti Pembayaran </th>
								<th> Status Verifikasi </th>
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


@section('modal')
<div class="modal fade" id="modalImport" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="formImport">

				<div class="modal-header">
					<h5 class="modal-title">
						<i class="fa fa-upload"></i> Import
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<div class="p-1">
						<p class="mb-2"> Catatan : </p>
						<ul class="pl-4">
							<li> Import wajib menggunakan template yg kita sediakan. </li>
							<li> Download template dengan <a href="{{ route('import_templates', 'Template_Transaksi.xlsx') }}" download> Klik Disini </a>. </li>
							<li> Kolom dengan background merah wajib diisi. </li>
						</ul>
					</div>


					{!! Template::requiredBanner() !!}

					<div class="form-group">
						<label> File {!! Template::required() !!} </label>
						<input type="file" name="file_excel" class="form-control">
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Grup Transaksi {!! Template::required() !!} </label>
						<select class="form-control" name="id_transaction_group" style="width: 100%;">
							@foreach(auth()->user()->getTransactionGroups() as $transactionGroup)
							<option value="{{ $transactionGroup->id }}"> {{ $transactionGroup->transaction_group_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fas fa-times mr-1"></i> Tutup
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-upload mr-1"></i> Import
					</button>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modalFilter" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="formFilter">

				<div class="modal-header">
					<h5 class="modal-title">
						<i class="fa fa-filter"></i> Filter
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<div class="form-group">
						<label> Grup Transaksi </label>
						<select class="form-control" name="id_transaction_group" style="width: 100%;">
							<option value="all"> - Semua Grup Transaksi - </option>
							@foreach(auth()->user()->getTransactionGroups() as $transactionGroup)
							<option value="{{ $transactionGroup->id }}"> {{ $transactionGroup->transaction_group_name }} </option>
							@endforeach
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Jenis </label>
						<select class="form-control" name="type" style="width: 100%;">
							<option value="all"> - Semua Jenis - </option>
							<option value="Income"> Pemasukan </option>
							<option value="Expense"> Pengeluaran </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Kategori </label>
						<div class="small mb-2"> * Pilih Jenis Terlebih Dahulu </div>
						<select class="form-control" name="id_category" style="width: 100%;">
							<option value="all"> - Semua Kategori - </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

					<div class="form-group">
						<label> Status Verifikasi </label>
						<select class="form-control" name="is_verified" style="width: 100%;">
							<option value="all"> - Semua - </option>
							<option value="yes"> Terverifikasi </option>
							<option value="no"> Belum Diverifikasi </option>
						</select>
						<span class="invalid-feedback"></span>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fas fa-times mr-1"></i> Tutup
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-filter mr-1"></i> Terapkan Filter
					</button>
				</div>

			</form>
		</div>
	</div>
</div>
@endsection


@section('scripts')
<script>

	$(function(){

		const $filterForm = $('#formFilter');

		$filterForm.find(`[name="is_verified"]`).select2({
			'placeholder': '- Pilih Status Verifikasi -',
		})
		$filterForm.find(`[name="is_verified"]`).val('all').trigger('change')

		$('#dataTable').DataTable({
			processing : true,
			serverSide : true,
			autoWidth : false,
			ajax : {
				url : "{{ route('transaction') }}"
			},
			columns : [
				@if(auth()->user()->isOwner())
				{
					data : "check",
					name : "check",
				},
				@endif
				{
					data : "date",
					name : "date",
				},
				{
					data : "description",
					name : "description",
				},
				{
					data : "nominal",
					name : "nominal",
				},
				{
					data : "file_transaction_proof",
					name : "file_transaction_proof",
				},
				{
					data : "is_verified",
					name : "is_verified",
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
			preDrawCallback : settings => {
				const query = $filterForm.serialize();
				settings.ajax.url = `{{ route('transaction') }}?${query}`
			},
			@if(auth()->user()->isOwner())
			order: [[ '1', 'desc' ]]
			@else
			order: [[ '0', 'desc' ]]
			@endif
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

			$.each($('.verify'), (i, verifyBtn) => {
				$(verifyBtn).off('click')
				$(verifyBtn).on('click', function(){
					let { verifyMessage, verifyHref } = $(this).data();
					confirmation(verifyMessage, function(){
						ajaxSetup()
						$.ajax({
							url: verifyHref,
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


		const $importForm = $('#formImport');
		const $importSubmitBtn = $importForm.find(`[type="submit"]`).ladda();

		$importForm.find(`[name="id_transaction_group"]`).select2({
			'placeholder': '- Pilih Grup Transaksi -',
		})
		$importForm.find(`[name="id_transaction_group"]`).val('all').trigger('change')


		$importForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			const formData = new FormData(this)
			$importSubmitBtn.ladda('start')

			ajaxSetup();
			$.ajax({
				url: `{{ route('transaction.import') }}`,
				method: 'post',
				data: formData,
				dataType: 'json',
				processData: false,
				contentType: false
			})
			.done(response => {
				$importSubmitBtn.ladda('stop')
				ajaxSuccessHandling(response)
				$importForm[0].reset();
				$importForm.find(`[name="id_transaction_group"]`).val('').trigger('change')
				reloadDT()
				$('#modalImport').modal('hide')
			})
			.fail(error => {
				$importSubmitBtn.ladda('stop')
				ajaxErrorHandling(error, $importForm)
			})
		})


		$filterForm.find(`[name="id_transaction_group"]`).select2({
			'placeholder': '- Pilih Grup Transaksi -',
		})

		$filterForm.find(`[name="type"]`).select2({
			'placeholder': '- Pilih Jenis -'
		})

		$filterForm.find(`[name="id_category"]`).select2({
			'placeholder': '- Pilih Kategori -',
			'allowClear': true
		})

		$filterForm.find(`[name="type"]`).on('change', function(){
			const type = $(this).val();

			if(type) {
				$.get({
					url: `{{ route('get_category') }}?type=${type}`,
					dataType: 'json'
				})
				.done(response => {
					let html = '';
					const { categories } = response

					html += `<option value="all"> - Semua Kategori - </option>`
					categories.forEach(category => {
						html += `<option value="${category.id}"> ${category.category_name} </option>`
					})
					$filterForm.find(`[name="id_category"]`).html(html)
					$filterForm.find(`[name="id_category"]`).val('all').trigger('change')
				})
			}
		})

		$filterForm.on('submit', function(e){
			e.preventDefault();
			reloadDT();
			$('#modalFilter').modal('hide');
		})


		$('.verify-selected').on('click', function(){
			let { verifyMessage, verifyHref } = $(this).data();
			confirmation(verifyMessage, function(){
				let checked = [];
				$.each($('.checkbox-transaction'), (i, checkbox) => {
					if($(checkbox).prop('checked')) {
						checked.push($(checkbox).val())
					}
				})

				if(checked.length == 0) {
					warningNotification('Peringatan', 'Wajib centang minimal 1 transaksi')
					return;
				}

				ajaxSetup()
				$.ajax({
					url: `{{ route('transaction.verify_selected') }}`,
					method: 'post',
					dataType: 'json',
					data: {
						ids: checked
					}
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


		$('.verify-all').on('click', function(){
			let { verifyMessage } = $(this).data();
			confirmation(verifyMessage, function(){
				ajaxSetup()
				$.ajax({
					url: `{{ route('transaction.verify_all') }}`,
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

</script>
@endsection
