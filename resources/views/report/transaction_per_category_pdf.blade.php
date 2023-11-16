<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		* {
			font-family: Calibri, Arial, sans-serif;
		}

		.title {
			text-align: center;
			font-size: 20pt;
			margin-bottom: 10px;
		}

		.date {
			text-align: center;
			font-size: 10pt;
		}

		.table {
			width: 100%;
			border-collapse: collapse;
		}

		.table td,
		.table th {
			font-size: 10pt;
			padding: 3px;
			border: 1px solid black;
		}

		.bg-grey {
			background: rgb(199 199 199);
		}

		.red {
			color: red;
		}
	</style>
</head>
<body>

	<div class="title"> Laporan Kas Per Kategori </div>

	@if(!empty($startDate) && !empty($endDate))
	<div class="date">
		Periode : 
		@if($startDate == $endDate)
		{{ date('d-m-Y', strtotime($startDate)) }}
		@else
		{{ date('d-m-Y', strtotime($startDate)) }} s/d {{ date('d-m-Y', strtotime($endDate)) }}
		@endif	
	</div>
	@endif

	@if($transactionGroup)
	<div class="date">
		Grup Transaksi : {{ $transactionGroup->transaction_group_name }}
	</div>
	@endif

	<br>


	<table class="table">
		<thead>
			<tr>
				<td> No </td>
				<td> Kategori </td>
				<td align="right"> Pemasukan </td>
				<td align="right"> Pengeluaran </td>
				<td align="right"> Saldo </td>
			</tr>
		</thead>

		<tbody>
			@if($beginningBalance > 0)
			<tr>
				<td></td>
				<td><b> Saldo Awal </b></td>
				<td></td>
				<td></td>
				<td align="right"><b> Rp. {{ number_format($beginningBalance) }} </b></td>
			</tr>

			<tr>
				<td style="height: 15px;"></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			@endif

			<tr>
				<td></td>
				<td><b> Pemasukkan </b></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>

			<?php $balance = $beginningBalance; ?>
			<?php $iteration = 1; ?>
			@foreach($dataCategories as $id => $dataCategory)
				@if($dataCategory->category->isTypeIncome())
				<?php $balance += $dataCategory->total; ?>
				<tr>
					<td> {{ $iteration++ }} </td>
					<td> {{ $dataCategory->category->category_name }} </td>
					<td align="right"> Rp. {{ number_format($dataCategory->total) }} </td>
					<td></td>
					@if($balance >= 0)
					<td align="right"> Rp. {{ number_format($balance) }} </td>
					@else
					<td align="right"> - Rp. {{ number_format($balance * -1) }} </td>
					@endif
				</tr>
				@endif
			@endforeach

			@if($dataOthers->Income > 0)
			<?php $balance += $dataOthers->Income; ?>
			<tr>
				<td> {{ $iteration++ }} </td>
				<td> Lainnya </td>
				<td align="right"> Rp. {{ number_format($dataOthers->Income) }} </td>
				<td></td>
				@if($balance >= 0)
				<td align="right"> Rp. {{ number_format($balance) }} </td>
				@else
				<td align="right"> - Rp. {{ number_format($balance * -1) }} </td>
				@endif
			</tr>
			@endif

			<tr>
				<td style="height: 15px;"></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>

			<tr>
				<td></td>
				<td><b> Pengeluaran </b></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>

			@foreach($dataCategories as $id => $dataCategory)
				@if($dataCategory->category->isTypeExpense())
				<?php $balance += $dataCategory->total; ?>
				<tr>
					<td> {{ $iteration++ }} </td>
					<td> {{ $dataCategory->category->category_name }} </td>
					<td></td>
					<td align="right"> - Rp. {{ number_format($dataCategory->total * -1) }} </td>
					@if($balance >= 0)
					<td align="right"> Rp. {{ number_format($balance) }} </td>
					@else
					<td align="right"> - Rp. {{ number_format($balance * -1) }} </td>
					@endif
				</tr>
				@endif
			@endforeach

			@if($dataOthers->Expense < 0)
			<?php $balance += $dataOthers->Expense; ?>
			<tr>
				<td> {{ $iteration++ }} </td>
				<td> Lainnya </td>
				<td></td>
				<td align="right"> - Rp. {{ number_format($dataOthers->Expense * -1) }} </td>
				@if($balance >= 0)
				<td align="right"> Rp. {{ number_format($balance) }} </td>
				@else
				<td align="right"> - Rp. {{ number_format($balance * -1) }} </td>
				@endif
			</tr>
			@endif

			<tr>
				<td style="height: 15px;"></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>

			<tr>
				<td></td>
				<td><b> Saldo Akhir </b></td>
				<td></td>
				<td></td>
				<td align="right"><b> Rp. {{ number_format($balance) }} </b></td>
			</tr>
		</tbody>
	</table>

</body>
</html>