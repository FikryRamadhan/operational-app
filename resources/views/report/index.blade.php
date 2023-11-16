@extends('layouts.template')


@section('styles')
<style type="text/css">
	.hand {
		cursor: pointer;
	}
</style>
@endsection


@section('content')
<div class="row">

	<div class="col-lg-12 mb-3">
		<ul class="nav nav-pills nav-secondary" id="nav-menu" role="tablist">

			<li class="nav-item">
				<a class="nav-link" id="report-tab" data-toggle="pill" href="#report" role="tab" aria-controls="report" aria-selected="true">
					Laporan
				</a>
			</li>

			<!-- <li class="nav-item">
				<a class="nav-link" id="inventory-tab" data-toggle="pill" href="#inventory" role="tab" aria-controls="inventory" aria-selected="true">
					...
				</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" id="manufacturing-tab" data-toggle="pill" href="#manufacturing" role="tab" aria-controls="manufacturing" aria-selected="true">
					...
				</a>
			</li>

			<li class="nav-item">
				<a class="nav-link" id="report-tab" data-toggle="pill" href="#report" role="tab" aria-controls="report" aria-selected="true">
					Laporan
				</a>
			</li> -->
		</ul>
	</div>

	<div class="col-lg-12">
		<div class="tab-content" id="tab-menu">

			<div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
				<div class="row">
					<div class="col-lg-4">
						<div class="card transaction-btn hand">
							<div class="card-body">
								<h2> Laporan Kas </h2>
								<p> Laporan Transaksi Pemasukan & Pengeluaran </p>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="card transaction-per-category-btn hand">
							<div class="card-body">
								<h2> Laporan Kas Per Kategori </h2>
								<p> Laporan Transaksi Pemasukan & Pengeluaran Per Kategori </p>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="card income-btn hand">
							<div class="card-body">
								<h2> Laporan Pemasukan </h2>
								<p> Laporan Transaksi Pemasukan </p>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<div class="card expense-btn hand">
							<div class="card-body">
								<h2> Laporan Pengeluaran </h2>
								<p> Laporan Transaksi Pengeluaran </p>
							</div>
						</div>
					</div>

					<!-- <div class="col-lg-3">
						<div class="card supply-btn hand">
							<div class="card-body">
								<h2> ... </h2>
								<p> .. </p>
							</div>
						</div>
					</div> -->
				</div>
			</div>


			<div class="tab-pane fade" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
				<div class="row">
					<div class="col-lg-3">
						<div class="card supply-btn hand">
							<div class="card-body">
								<h2> ... </h2>
								<p> .. </p>
							</div>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="card supply-btn hand">
							<div class="card-body">
								<h2> ... </h2>
								<p> .. </p>
							</div>
						</div>
					</div>
				</div>
			</div>


		</div>
	</div>

</div>
@endsection


@section('modal')
<div class="modal fade" id="transaction-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="transaction-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Laporan Kas
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					{!! Template::requiredBanner() !!}

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Awal </label>
								<input type="date" name="start_date" class="form-control">
								<span class="invalid-feedback"></span>
							</div>
						</div>
							
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Akhir </label>
								<input type="date" name="end_date" class="form-control">
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Grup Transaksi </label>
						<select name="id_transaction_group" style="width: 100%;">
							<option value="all"> - Semua Grup Transaksi - </option>
							@foreach(auth()->user()->getTransactionGroups() as $group)
							<option value="{{ $group->id }}"> {{ $group->transaction_group_name }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label> Aksi </label>
						<select class="form-control" name="action">
							<option value="pdf_stream"> Preview Report PDF </option>
							<option value="pdf_download"> Download Report PDF </option>
							<option value="xlsx_download"> Download Report Excel </option>
						</select>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fas fa-times mr-1"></i> Tutup
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-check mr-1"></i> Buat Report
					</button>
				</div>

			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="transaction-per-category-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="transaction-per-category-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Laporan Kas Per Kategori
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					{!! Template::requiredBanner() !!}

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Awal </label>
								<input type="date" name="start_date" class="form-control">
								<span class="invalid-feedback"></span>
							</div>
						</div>
							
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Akhir </label>
								<input type="date" name="end_date" class="form-control">
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Grup Transaksi </label>
						<select name="id_transaction_group" style="width: 100%;">
							<option value="all"> - Semua Grup Transaksi - </option>
							@foreach(auth()->user()->getTransactionGroups() as $group)
							<option value="{{ $group->id }}"> {{ $group->transaction_group_name }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label> Aksi </label>
						<select class="form-control" name="action">
							<option value="pdf_stream"> Preview Report PDF </option>
							<option value="pdf_download"> Download Report PDF </option>
							<option value="xlsx_download"> Download Report Excel </option>
						</select>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fas fa-times mr-1"></i> Tutup
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-check mr-1"></i> Buat Report
					</button>
				</div>
			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="income-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="income-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Laporan Pemasukan
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					{!! Template::requiredBanner() !!}

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Awal </label>
								<input type="date" name="start_date" class="form-control">
								<span class="invalid-feedback"></span>
							</div>
						</div>
							
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Akhir </label>
								<input type="date" name="end_date" class="form-control">
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Grup Transaksi </label>
						<select name="id_transaction_group" style="width: 100%;">
							<option value="all"> - Semua Grup Transaksi - </option>
							@foreach(auth()->user()->getTransactionGroups() as $group)
							<option value="{{ $group->id }}"> {{ $group->transaction_group_name }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label> Aksi </label>
						<select class="form-control" name="action">
							<option value="pdf_stream"> Preview Report PDF </option>
							<option value="pdf_download"> Download Report PDF </option>
							<option value="xlsx_download"> Download Report Excel </option>
						</select>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fas fa-times mr-1"></i> Tutup
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-check mr-1"></i> Buat Report
					</button>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="expense-modal" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="expense-form">

				<div class="modal-header">
					<h5 class="modal-title">
						Laporan Pengeluaran
					</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">

					{!! Template::requiredBanner() !!}

					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Awal </label>
								<input type="date" name="start_date" class="form-control">
								<span class="invalid-feedback"></span>
							</div>
						</div>
							
						<div class="col-lg-6">
							<div class="form-group">
								<label> Tanggal Akhir </label>
								<input type="date" name="end_date" class="form-control">
								<span class="invalid-feedback"></span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label> Grup Transaksi </label>
						<select name="id_transaction_group" style="width: 100%;">
							<option value="all"> - Semua Grup Transaksi - </option>
							@foreach(auth()->user()->getTransactionGroups() as $group)
							<option value="{{ $group->id }}"> {{ $group->transaction_group_name }} </option>
							@endforeach
						</select>
					</div>

					<div class="form-group">
						<label> Aksi </label>
						<select class="form-control" name="action">
							<option value="pdf_stream"> Preview Report PDF </option>
							<option value="pdf_download"> Download Report PDF </option>
							<option value="xlsx_download"> Download Report Excel </option>
						</select>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fas fa-times mr-1"></i> Tutup
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fas fa-check mr-1"></i> Buat Report
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

		/*
		*		# Transaction
		*/
		$transactionModal = $('#transaction-modal');
		$transactionForm = $('#transaction-form');
		$transactionSubmitBtn = $transactionForm.find(`[type="submit"]`).ladda();

		$('.transaction-btn').on('click', function(){
			$transactionModal.modal('show');
		})

		$transactionForm.find(`[name="id_transaction_group"]`).select2({
			'placeholder': '- Pilih Grup Transaksi -'
		})

		$transactionForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			window.open(`{{ route('report.transaction_generate') }}?${formData}`);
		})



		/*
		*		# Transaction Per Category
		*/
		$transactionPerCategoryModal = $('#transaction-per-category-modal');
		$transactionPerCategoryForm = $('#transaction-per-category-form');
		$transactionPerCategorySubmitBtn = $transactionPerCategoryForm.find(`[type="submit"]`).ladda();

		$('.transaction-per-category-btn').on('click', function(){
			$transactionPerCategoryModal.modal('show');
		})

		$transactionPerCategoryForm.find(`[name="id_transaction_group"]`).select2({
			'placeholder': '- Pilih Grup Transaksi -'
		})

		$transactionPerCategoryForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			window.open(`{{ route('report.transaction_per_category_generate') }}?${formData}`);
		})



		/*
		*		# INCOME
		*/
		$incomeModal = $('#income-modal');
		$incomeForm = $('#income-form');
		$incomeSubmitBtn = $incomeForm.find(`[type="submit"]`).ladda();

		$('.income-btn').on('click', function(){
			$incomeModal.modal('show');
		})

		$incomeForm.find(`[name="id_transaction_group"]`).select2({
			'placeholder': '- Pilih Grup Transaksi -'
		})

		$incomeForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			window.open(`{{ route('report.income_generate') }}?${formData}`);
		})



		/*
		*		# EXPENSE
		*/
		$expenseModal = $('#expense-modal');
		$expenseForm = $('#expense-form');
		$expenseSubmitBtn = $expenseForm.find(`[type="submit"]`).ladda();

		$('.expense-btn').on('click', function(){
			$expenseModal.modal('show');
		})

		$expenseForm.find(`[name="id_transaction_group"]`).select2({
			'placeholder': '- Pilih Grup Transaksi -'
		})

		$expenseForm.on('submit', function(e){
			e.preventDefault();
			clearInvalid();

			let formData = $(this).serialize();

			window.open(`{{ route('report.expense_generate') }}?${formData}`);
		})



		$('#nav-menu').find('.nav-link:first').addClass('active');
		$('#tab-menu').find('.tab-pane:first').addClass('show active');


	})

</script>
@endsection
