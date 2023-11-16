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

	<div class="title"> Laporan Kas </div>

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
				<td> Tanggal </td>
				<td> Keterangan </td>
				<td align="right"> Pemasukan </td>
				<td align="right"> Pengeluaran </td>
				<td align="right"> Saldo </td>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td></td>
				<td></td>
				<td><b> Saldo Awal </b></td>
				<td></td>
				<td></td>
				<td align="right"><b> Rp. {{ number_format($beginningBalance) }} </b></td>
			</tr>

			<?php $balance = $beginningBalance; ?>
			<?php $totalIncome = 0; ?>
			<?php $totalExpense = 0; ?>
			@forelse($transactions as $transaction)
			<?php $balance += $transaction->nominal; ?>
			<tr>
				<td> {{ $loop->iteration }} </td>
				<td> {{ $transaction->dateText('d/m/Y') }} </td>
				<td> {{ $transaction->description }} </td>

				@if($transaction->isTypeIncome())
				<td align="right"> {{ $transaction->nominalText() }} </td>
				<td></td>
				<?php $totalIncome += $transaction->nominal; ?>
				@else
				<td></td>
				<td align="right"> {{ $transaction->nominalText() }} </td>
				<?php $totalExpense += $transaction->nominal * - 1; ?>
				@endif

				@if($balance >= 0)
				<td align="right"> Rp. {{ number_format($balance) }} </td>
				@else
				<td align="right"> - Rp. {{ number_format($balance * -1) }} </td>
				@endif
			</tr>
			@empty
			<tr>
				<td colspan="6" align="center">
					Kosong
				</td>
			</tr>
			@endforelse

			<tr>
				<td></td>
				<td></td>
				<td><b> Total </b></td>
				<td align="right"><b> Rp. {{ number_format($totalIncome) }} </b></td>
				<td align="right"><b> Rp. {{ number_format($totalExpense) }} </b></td>
				<td></td>
			</tr>

			<tr>
				<td></td>
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