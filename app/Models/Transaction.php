<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	use HasFactory;

	protected $fillable = [ 'date', 'type', 'id_transaction_group', 'id_category', 'description', 'nominal', 'file_transaction_proof', 'is_verified', 'id_user', 'id_verificator_user' ];


	/**
	 * 	Relationship
	 * */
	public function transactionGroup()
	{
		return $this->belongsTo('App\Models\TransactionGroup', 'id_transaction_group')->withTrashed();
	}

	public function category()
	{
		return $this->belongsTo('App\Models\Category', 'id_category')->withTrashed();
	}

	public function verificatorUser()
	{
		return $this->belongsTo('App\Models\User', 'id_verificator_user');
	}


	/**
	 *  CRUD methods
	 * */
	public static function createTransaction($request)
	{
		$transaction = self::create(array_merge($request->all(), [
			'id_user' => auth()->user()->id
		]));
		$transaction->saveFile($request);
		$transaction->setValidNominal();
		return $transaction;
	}

	public function updateTransaction($request)
	{
		$this->update($request->all());
		$this->saveFile($request);
		$this->setValidNominal();
		return $this;
	}

	public function deleteTransaction()
	{
		$this->removeTransactionProof();
		return $this->delete();
	}



	/**
	 * 	Helper methods
	 * */
	public function saveFile($request)
	{
		if($request->hasFile('file_transaction_proof_upload')) {
			$this->removeTransactionProof();
			$file = $request->file('file_transaction_proof_upload');
			$filename = date('YmdHis_').$file->getClientOriginalName();
			$file->move(storage_path('app/public/transaction_proof'), $filename);
			$this->update([
				'file_transaction_proof' => $filename,
			]);
		}

		return $this;
	}

	public function transactionProofFilePath()
	{
		return storage_path('app/public/transaction_proof/'.$this->file_transaction_proof);
	}

	public function transactionProofFileLink()
	{
		return url('storage/transaction_proof/'.$this->file_transaction_proof);
	}

	public function isHasTransactionProof()
	{
		if(empty($this->file_transaction_proof)) return false;
		return \File::exists($this->transactionProofFilePath());
	}

	public function removeTransactionProof()
	{
		if($this->isHasTransactionProof()) {
			\File::delete($this->transactionProofFilePath());
			$this->update([
				'file_transaction_proof' => null
			]);
		}

		return $this;
	}

	public function dateText($format = 'd M Y')
	{
		return date($format, strtotime($this->date));
	}

	public function nominalText()
	{
		if($this->isTypeIncome()) {
			return 'Rp. '.number_format($this->nominal);
		} else {
			return '- Rp. '.number_format($this->nominal * -1);
		}
	}

	public function nominalHtml()
	{
		if($this->isTypeIncome()) {
			return '<span class="text-success"> Rp. '.number_format($this->nominal).'</span>';
		} else {
			return '<span class="text-danger"> - Rp. '.number_format($this->nominal * -1).'</span>';
		}
	}

	public function transactionProofFileLinkHtml()
	{
		if($this->isHasTransactionProof()) {
			return '<a href="'.$this->transactionProofFileLink().'" target="_blank"> Lihat Bukti Transaksi </a>';
		} else {
			return '<span class="text-danger"> Tidak ada bukti transaksi </span>';
		}
	}

	public function isTypeIncome()
	{
		return $this->type == 'Income';
	}

	public function isTypeExpense()
	{
		return $this->type == 'Expense';
	}

	public function isVerified()
	{
		return $this->is_verified == 'yes';
	}

	public function isVerifiedHtml()
	{
		if($this->isVerified()) {
			return '<span class="text-success"> <i class="fas fa-check-circle"></i> Diverifikasi </span> <span>['.$this->verificatorUserName().']</span>';
		} else {
			return '<span class="text-danger"> <i class="fas fa-times-circle"></i> Belum Diverifikasi </span>';
		}
	}

	public function verificatorUserName()
	{
		return $this->verificatorUser->name ?? '-';
	}

	public function transactionGroupName()
	{
		return $this->transactionGroup->transaction_group_name ?? '-';
	}

	public function categoryName()
	{
		return $this->category->category_name ?? '-';
	}

	public function setValidNominal()
	{
		$nominal = $this->nominal;
		if($this->isTypeIncome() && $nominal < 0) $nominal *= -1;
		if($this->isTypeExpense() && $nominal >= 0) $nominal *= -1;
		$this->update([
			'nominal' => $nominal
		]);

		return $this;
	}

	public function verify()
	{
		$this->update([
			'is_verified'			=> 'yes',
			'id_verificator_user'	=> auth()->user()->id,
		]);
		return $this;
	}



	/**
	 *  Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::with([ 'transactionGroup', 'category' ]);

		if(!empty($request->id_transaction_group)) {
			if($request->id_transaction_group != 'all') {
				$data = $data->where('id_transaction_group', $request->id_transaction_group);
			}
		}

		if(!empty($request->type)) {
			if($request->type != 'all') {
				$data = $data->where('type', $request->type);
			}
		}

		if(!empty($request->id_category)) {
			if($request->id_category != 'all') {
				$data = $data->where('id_category', $request->id_category);
			}
		}

		if(!empty($request->is_verified)) {
			if($request->is_verified != 'all') {
				$data = $data->where('is_verified', $request->is_verified);
			}
		}

		$transactionGroupIds = [];
		foreach(auth()->user()->getTransactionGroups() as $group) {
			$transactionGroupIds[] = $group->id;
		}

		$data = $data->whereIn('id_transaction_group', $transactionGroupIds);

		return \Datatables::eloquent($data)
			->addColumn('check', function($data){
				$html = '
				<div class="custom-control custom-checkbox">
					<input type="checkbox" class="custom-control-input checkbox-transaction" id="row-'.$data->id.'" value="'.$data->id.'">
					<label class="custom-control-label" for="row-'.$data->id.'">&nbsp;</label>
				</div>';

				return $html;
			})
			->editColumn('date', function($data){
				return $data->dateText();
			})
			->editColumn('description', function($data){
				$text = str_replace("\n", "<br>", $data->description);
				$meta = '';

				if($data->transactionGroup) {
					$meta .= '<br> <span class="text-primary"> ['.$data->transactionGroupName().']</span>';
				}
				if($data->category) {
					if($data->isTypeIncome()) {
						$meta .= '<br> <span class="text-success"> ['.$data->categoryName().']</span>';
					} else {
						$meta .= '<br> <span class="text-danger"> ['.$data->categoryName().']</span>';
					}
				}

				return $text.$meta;
			})
			->editColumn('nominal', function($data){
				return $data->nominalHtml();
			})
			->editColumn('is_verified', function($data){
				return $data->isVerifiedHtml();
			})
			->editColumn('file_transaction_proof', function($data){
				return $data->transactionProofFileLinkHtml();
			})
			->addColumn('action', function ($data) {
				$action = '
					<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
							<!--<a class="dropdown-item" href="'.route('transaction.detail', $data->id).'">
								<i class="fas fa-search mr-1"></i> Detail
							</a>-->
							';

				if(!$data->isVerified() && auth()->user()->isOwner()) {
					$action .= '
							<a class="dropdown-item verify" href="javascript:void(0)" data-verify-message="Yakin ingin memverifikasi <strong>'.$data->description.'</strong>?" data-verify-href="'.route('transaction.verify', $data->id).'">
								<i class="fas fa-check mr-1"></i> Verifikasi
							</a>';
				}

				if(!$data->isVerified() || auth()->user()->isOwner()) {
					$action .= '
							<a class="dropdown-item" href="'.route('transaction.edit', $data->id).'">
								<i class="fas fa-pencil-alt mr-1"></i> Edit
							</a>
							<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong>'.$data->description.'</strong>?" data-delete-href="'.route('transaction.destroy', $data->id).'">
								<i class="fas fa-trash mr-1"></i> Hapus
							</a>';
				}

				if($data->isVerified() && !auth()->user()->isOwner()) {
					$action .= '
							<a class="dropdown-item" href="javascript:void(0);">
								Tidak Ada
							</a>';
				}

				$action .= '
						</div>
					</div>';
				return $action;
			})
			->rawColumns([ 'check', 'description', 'is_active', 'is_verified', 'nominal', 'file_transaction_proof', 'action' ])
			->make(true);
	}

	public static function importFromExcel($request)
	{
		$amount = 0;

		if(!empty($request->file_excel))
		{
			$file = $request->file('file_excel');
			$filename = date('YmdHis_').rand(100,999).'.'.$file->getClientOriginalExtension();
			$file->move(storage_path('app/public/temp_files'), $filename);
			$path = storage_path('app/public/temp_files/'.$filename);
			$parseData = \App\MyClass\SimpleXLSX::parse($path);

			if($parseData)
			{
				$iter = 0;
				foreach($parseData->rows() as $row)
				{
					$iter++;
					if($iter == 1) continue;

					if(!empty($row[0]) & !empty($row[1]) && (!empty($row[2])) || !empty($row[3])) {
						$date = $row[0];
						try {
							$date = \Carbon\Carbon::createFromFormat('d/m/Y', $data)->format('Y-m-d');
						} catch (\Exception $e) {}
						$nominalDebet = preg_replace( '/[^0-9]/', '', $row[2]);
						$nominalKredit = preg_replace( '/[^0-9]/', '', $row[3]);
						$type = '';

						if(!empty($nominalDebet)) {
							$type = 'Income';
						} elseif(!empty($nominalKredit)) {
							$type = 'Expense';
						}

						if(!empty($type)) {
							$nominal = $type == 'Income' ? $nominalDebet : $nominalKredit * -1;

							try {
								\DB::beginTransaction();
								self::create([
									'date' => $date,
									'type' => $type,
									'id_transaction_group' => $request->id_transaction_group,
									'description' => $row[1],
									'nominal' => $nominal,
								]);
								$amount++;
								\DB::commit();
							} catch (\Exception $e) {
								\DB::rollback();								
							}
						}

					}

				}
			}

			\File::delete($path);
		}

		return $amount;
	}



	/**
	 * 	Total This Year
	 * */
	public static function totalIncomeThisYear()
	{
		$transactionGroupIds = [];
		foreach(auth()->user()->getTransactionGroups() as $group) {
			$transactionGroupIds[] = $group->id;
		}

		$incomes = self::where('type', 'Income')
					  ->whereIn('id_transaction_group', $transactionGroupIds)
					  ->whereBetween('date', [ date('Y-01-01'), date('Y-12-31') ])
					  ->get();
		$total = 0;
		foreach($incomes as $income) {
			$total += $income->nominal;
		}

		return $total;
	}

	public static function totalIncomeThisYearFormatted()
	{
		return 'Rp. '.number_format(self::totalIncomeThisYear());
	}

	public static function totalExpenseThisYear()
	{
		$transactionGroupIds = [];
		foreach(auth()->user()->getTransactionGroups() as $group) {
			$transactionGroupIds[] = $group->id;
		}

		$expenses = self::where('type', 'Expense')
					 	->whereIn('id_transaction_group', $transactionGroupIds)
						->whereBetween('date', [ date('Y-01-01'), date('Y-12-31') ])
						->get();
		$total = 0;
		foreach($expenses as $expense) {
			$total += $expense->nominal * -1;
		}

		return $total;
	}

	public static function totalExpenseThisYearFormatted()
	{
		return 'Rp. '.number_format(self::totalExpenseThisYear());
	}

	public static function totalBalanceThisYear()
	{
		return self::totalIncomeThisYear() - self::totalExpenseThisYear();
	}

	public static function totalBalanceThisYearFormatted()
	{
		$total = self::totalBalanceThisYear();
		if($total >= 0) {
			return 'Rp. '.number_format($total);
		} else {
			return '- Rp. '.number_format($total * -1);
		}
	}


	/**
	 * 	Total
	 * */
	public static function totalIncome()
	{
		$transactionGroupIds = [];
		foreach(auth()->user()->getTransactionGroups() as $group) {
			$transactionGroupIds[] = $group->id;
		}

		$incomes = self::where('type', 'Income')
					  ->whereIn('id_transaction_group', $transactionGroupIds)
					  ->get();
		$total = 0;
		foreach($incomes as $income) {
			$total += $income->nominal;
		}

		return $total;
	}

	public static function totalIncomeFormatted()
	{
		return 'Rp. '.number_format(self::totalIncome());
	}

	public static function totalExpense()
	{
		$transactionGroupIds = [];
		foreach(auth()->user()->getTransactionGroups() as $group) {
			$transactionGroupIds[] = $group->id;
		}

		$expenses = self::where('type', 'Expense')
					 	->whereIn('id_transaction_group', $transactionGroupIds)
						->get();
		$total = 0;
		foreach($expenses as $expense) {
			$total += $expense->nominal * -1;
		}

		return $total;
	}

	public static function totalExpenseFormatted()
	{
		return 'Rp. '.number_format(self::totalExpense());
	}

	public static function totalBalance()
	{
		return self::totalIncome() - self::totalExpense();
	}

	public static function totalBalanceFormatted()
	{
		$total = self::totalBalance();
		if($total >= 0) {
			return 'Rp. '.number_format($total);
		} else {
			return '- Rp. '.number_format($total * -1);
		}
	}

	public static function totalBalanceFormattedHtml()
	{
		$total = self::totalBalance();
		if($total >= 0) {
			return 'Rp. '.number_format($total);
		} else {
			return '<span class="text-danger"> - Rp. '.number_format($total * -1).'</span>';
		}
	}


	/**
	 * 
	 * 	Report
	 * 
	 * */
	public static function generateDataForReport($request, $filename = null, $user = null)
	{
		$transactions = new self();
		if(empty($filename)) $filename = 'Laporan_Kas';
		$startDate = null;
		$endDate = null;
		$transactionGroup = null;
		if(empty($user)) $user = auth()->user();

		$beginningBalance = 0;
		$transactionsForCountingBalance = null;

		if(!empty($request->start_date)) {
			$transactions = $transactions->where('date', '>=', $request->start_date);
			$startDate = $request->start_date;
			$transactionsForCountingBalance = self::where('date', '<', $request->start_date);
		}

		if(!empty($request->end_date)) {
			$transactions = $transactions->where('date', '<=', $request->end_date);
			$endDate = $request->end_date;
		}

		if($request->id_transaction_group != 'all') {
			$transactions = $transactions->where('id_transaction_group', $request->id_transaction_group);
			$transactionGroup = TransactionGroup::find($request->id_transaction_group);

			if($transactionsForCountingBalance) {
				$transactionsForCountingBalance = $transactionsForCountingBalance->where('id_transaction_group', $request->id_transaction_group);
			}
		} else {
			$transactionGroupIds = [];
			foreach($user->getTransactionGroups() as $group) {
				$transactionGroupIds[] = $group->id;
			}
			$transactions = $transactions->whereIn('id_transaction_group', $transactionGroupIds);

			if($transactionsForCountingBalance) {
				$transactionsForCountingBalance = $transactionsForCountingBalance->whereIn('id_transaction_group', $transactionGroupIds);
			}
		}

		$transactions = $transactions->orderBy('date', 'asc')
									 ->orderBy('type', 'desc')
									 ->get();
		
		if($transactionsForCountingBalance) {
			$transactionsForCountingBalance = $transactionsForCountingBalance->get();
			foreach($transactionsForCountingBalance as $t) {
				$beginningBalance += $t->nominal;
			}
		}

		if(empty($startDate)) {
			$transaction = self::orderBy('date', 'asc');
			if(!empty($request->id_transaction_group)) {
				if($request->id_transaction_group != 'all') {
					$transaction = $transaction->where('id_transaction_group', $request->id_transaction_group);
				}
			}
			$transaction = $transaction->first();
			if($transaction) $startDate = $transaction->date;
		}

		if(empty($endDate)) {
			$transaction = self::orderBy('date', 'desc');
			if(!empty($request->id_transaction_group)) {
				if($request->id_transaction_group != 'all') {
					$transaction = $transaction->where('id_transaction_group', $request->id_transaction_group);
				}
			}
			$transaction = $transaction->first();
			if($transaction) $endDate = $transaction->date;
		}

		return [
			'data'		=> $transactions,
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
			'transactionGroup' => $transactionGroup,
			'beginningBalance' => $beginningBalance,
			'filename'	=> $filename,
		];
	}


	public static function generatePdfReport($request, $filename = null, $user = null)
	{
		$data = self::generateDataForReport($request, $filename, $user);
		$transactions = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$transactionGroup = $data['transactionGroup'];
		$beginningBalance = $data['beginningBalance'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('report.transaction_pdf', [
			'transactions'	=> $transactions,
			'startDate'		=> $startDate,
			'endDate'		=> $endDate,
			'transactionGroup' => $transactionGroup,
			'beginningBalance' => $beginningBalance,
		])->setPaper('A4', 'portrait');
		$filename .= '.pdf';

		return (object) [
			'pdf'		=> $pdf,
			'filename'	=> $filename,
		];
	}


	public static function streamPdfReport($request)
	{
		$result = self::generatePdfReport($request);

		return $result->pdf->stream($result->filename);
	}


	public static function downloadPdfReport($request)
	{
		$result = self::generatePdfReport($request);

		return $result->pdf->download($result->filename);
	}


	public static function downloadExcelReport($request)
	{
		$data = self::generateDataForReport($request);
		$transactions = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$transactionGroup = $data['transactionGroup'];
		$beginningBalance = $data['beginningBalance'];
		$filename = $data['filename'].'.xlsx';

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin', 'widths'=> [ 300, 300, 300, 300 ] ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();

		$totalRow = 0;
		$totalColumn = 5;

		$writer->writeSheetHeader('Sheet1', [
			'Laporan Kas'	=> 'string',
		], [
			'widths'=> [7,15,30,20,20,20],
			'font-style'=>'bold', 'halign'=>'center', 'valign' => 'center', 'height'=> 5, 'wrap_text' => true
		]);
		$writer->markMergedCell('Sheet1', $start_row=0, $start_col=0, $end_row=0, $end_col=$totalColumn);
		$totalRow++;

		if(!empty($startDate) && !empty($endDate)) {
			$writer->writeSheetRow('Sheet1', []);
			$totalRow++;

			if($startDate == $endDate) {
				$periode = date('d-m-Y', strtotime($startDate));
			} else {
				$periode = date('d-m-Y', strtotime($startDate)).' s/d '.date('d-m-Y', strtotime($endDate));
			}

			$writer->writeSheetRow('Sheet1', [ 'Periode : '.$periode ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		if($transactionGroup) {
			if(!(!empty($startDate) && !empty($endDate))) {
				$writer->writeSheetRow('Sheet1', []);
				$totalRow++;
			}

			$writer->writeSheetRow('Sheet1', [ 'Grup Transaksi : '.$transactionGroup->transaction_group_name ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		$writer->writeSheetRow('Sheet1', []);

		$writer->writeSheetRow('Sheet1', [
			'No',
			'Tanggal',
			'Keterangan',
			'Pemasukan',
			'Pengeluaran',
			'Saldo',
		], $headerStyle);

		$balance = 0;

		$balance += $beginningBalance;
		$writer->writeSheetRow('Sheet1', [
			'',
			'',
			'Saldo Awal',
			'',
			'',
			$beginningBalance,
		], $bodyStyle);

		$iter = 1;
		foreach($transactions as $transaction) {
			$balance += $transaction->nominal;
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				$transaction->dateText('d/m/Y'),
				$transaction->description,
				$transaction->isTypeIncome() ? $transaction->nominal : 0,
				$transaction->isTypeIncome() ? 0 : $transaction->nominal,
				$balance,
			], $bodyStyle);
			$iter++;
		}

		$writer->writeSheetRow('Sheet1', [
			'',
			'',
			'Saldo Akhir',
			'',
			'',
			$balance,
		], $bodyStyle);

		$path = \Helper::tempPath($filename);
		$writer->writeToFile($path);

		return $path;
	}



	/**
	 * 
	 * 	Report Transaction Per Category
	 * 
	 * */
	public static function transactionPerCategoryGenerateDataForReport($request, $filename = null, $user = null)
	{
		$transactions = new self();
		if(empty($filename)) $filename = 'Laporan_Kas_Per_Kategori';
		$startDate = null;
		$endDate = null;
		$transactionGroup = null;
		if(empty($user)) $user = auth()->user();

		$beginningBalance = 0;
		$transactionsForCountingBalance = null;

		if(!empty($request->start_date)) {
			$transactions = $transactions->where('date', '>=', $request->start_date);
			$startDate = $request->start_date;
			$transactionsForCountingBalance = self::where('date', '<', $request->start_date);
		}

		if(!empty($request->end_date)) {
			$transactions = $transactions->where('date', '<=', $request->end_date);
			$endDate = $request->end_date;
		}

		if($request->id_transaction_group != 'all') {
			$transactions = $transactions->where('id_transaction_group', $request->id_transaction_group);
			$transactionGroup = TransactionGroup::find($request->id_transaction_group);

			if($transactionsForCountingBalance) {
				$transactionsForCountingBalance = $transactionsForCountingBalance->where('id_transaction_group', $request->id_transaction_group);
			}
		} else {
			$transactionGroupIds = [];
			foreach($user->getTransactionGroups() as $group) {
				$transactionGroupIds[] = $group->id;
			}
			$transactions = $transactions->whereIn('id_transaction_group', $transactionGroupIds);

			if($transactionsForCountingBalance) {
				$transactionsForCountingBalance = $transactionsForCountingBalance->whereIn('id_transaction_group', $transactionGroupIds);
			}
		}

		if($transactionsForCountingBalance) {
			$transactionsForCountingBalance = $transactionsForCountingBalance->get();
			foreach($transactionsForCountingBalance as $t) {
				$beginningBalance += $t->nominal;
			}
		}

		$transactions = $transactions->orderBy('date', 'asc')
									 ->orderBy('type', 'desc')
									 ->with([ 'transactionGroup', 'category' ])
									 ->get();

		$data = [];
		$otherData = (object) [
			'Income' => 0,
			'Expense' => 0,
		];

		foreach($transactions as $transaction)
		{
			if(!empty($transaction->category)) {
				if(array_key_exists($transaction->id_category, $data))
				{
					$data[$transaction->id_category]->total += $transaction->nominal;
				}
				else
				{
					$data[$transaction->id_category] = (object) [
						'category' => $transaction->category,
						'total' => $transaction->nominal,
					];
				}
			} else {
				if($transaction->isTypeIncome()) {
					$otherData->Income += $transaction->nominal;
				} else {
					$otherData->Expense += $transaction->nominal;
				}
			}
		}

		if(empty($startDate)) {
			$transaction = self::orderBy('date', 'asc');
			if(!empty($request->id_transaction_group)) {
				if($request->id_transaction_group != 'all') {
					$transaction = $transaction->where('id_transaction_group', $request->id_transaction_group);
				}
			}
			$transaction = $transaction->first();
			if($transaction) $startDate = $transaction->date;
		}

		if(empty($endDate)) {
			$transaction = self::orderBy('date', 'desc');
			if(!empty($request->id_transaction_group)) {
				if($request->id_transaction_group != 'all') {
					$transaction = $transaction->where('id_transaction_group', $request->id_transaction_group);
				}
			}
			$transaction = $transaction->first();
			if($transaction) $endDate = $transaction->date;
		}

		return [
			'data'		=> $data,
			'otherData'	=> $otherData,
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
			'transactionGroup' => $transactionGroup,
			'beginningBalance' => $beginningBalance,
			'filename'	=> $filename,
		];
	}


	public static function transactionPerCategoryGeneratePdfReport($request, $filename = null, $user = null)
	{
		$data = self::transactionPerCategoryGenerateDataForReport($request, $filename, $user);
		$dataCategories = $data['data'];
		$dataOthers = $data['otherData'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$transactionGroup = $data['transactionGroup'];
		$beginningBalance = $data['beginningBalance'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('report.transaction_per_category_pdf', [
			'dataCategories' => $dataCategories,
			'dataOthers' 	=> $dataOthers,
			'startDate'		=> $startDate,
			'endDate'		=> $endDate,
			'transactionGroup' => $transactionGroup,
			'beginningBalance' => $beginningBalance,
		])->setPaper('A4', 'portrait');
		$filename .= '.pdf';

		return (object) [
			'pdf'		=> $pdf,
			'filename'	=> $filename,
		];
	}


	public static function transactionPerCategoryStreamPdfReport($request)
	{
		$result = self::transactionPerCategoryGeneratePdfReport($request);

		return $result->pdf->stream($result->filename);
	}


	public static function transactionPerCategoryDownloadPdfReport($request)
	{
		$result = self::transactionPerCategoryGeneratePdfReport($request);

		return $result->pdf->download($result->filename);
	}


	public static function transactionPerCategoryDownloadExcelReport($request)
	{
		$data = self::transactionPerCategoryGenerateDataForReport($request);
		$dataCategories = $data['data'];
		$dataOthers = $data['otherData'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$transactionGroup = $data['transactionGroup'];
		$beginningBalance = $data['beginningBalance'];
		$filename = $data['filename'].'.xlsx';

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin', 'widths'=> [ 300, 300, 300, 300 ] ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();

		$totalRow = 0;
		$totalColumn = 4;

		$writer->writeSheetHeader('Sheet1', [
			'Laporan Kas Per Kategori'	=> 'string',
		], [
			'widths'=> [7,30,20,20,20],
			'font-style'=>'bold', 'halign'=>'center', 'valign' => 'center', 'height'=> 5, 'wrap_text' => true
		]);
		$writer->markMergedCell('Sheet1', $start_row=0, $start_col=0, $end_row=0, $end_col=$totalColumn);
		$totalRow++;

		if(!empty($startDate) && !empty($endDate)) {
			$writer->writeSheetRow('Sheet1', []);
			$totalRow++;

			if($startDate == $endDate) {
				$periode = date('d-m-Y', strtotime($startDate));
			} else {
				$periode = date('d-m-Y', strtotime($startDate)).' s/d '.date('d-m-Y', strtotime($endDate));
			}

			$writer->writeSheetRow('Sheet1', [ 'Periode : '.$periode ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		if($transactionGroup) {
			if(!(!empty($startDate) && !empty($endDate))) {
				$writer->writeSheetRow('Sheet1', []);
				$totalRow++;
			}

			$writer->writeSheetRow('Sheet1', [ 'Grup Transaksi : '.$transactionGroup->transaction_group_name ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		$writer->writeSheetRow('Sheet1', []);

		$writer->writeSheetRow('Sheet1', [
			'No',
			'Kategori',
			'Pemasukan',
			'Pengeluaran',
			'Saldo',
		], $headerStyle);

		$balance = 0;

		if($beginningBalance > 0) {
			$balance += $beginningBalance;
			$writer->writeSheetRow('Sheet1', [
				'',
				'Saldo Awal',
				'',
				'',
				$beginningBalance,
			], $bodyStyle);
			
			$writer->writeSheetRow('Sheet1', [
				'', '', '', '', ''
			], $bodyStyle);
		}

		$writer->writeSheetRow('Sheet1', [
			'', 'Pemasukkan', '', '', ''
		], $headerStyle);

		$iter = 1;
		foreach($dataCategories as $dataCategory) {
			if($dataCategory->category->isTypeIncome()) {
				$balance += $dataCategory->total;
				$writer->writeSheetRow('Sheet1', [
					" $iter",
					$dataCategory->category->category_name,
					$dataCategory->category->isTypeIncome() ? $dataCategory->total : 0,
					$dataCategory->category->isTypeIncome() ? 0 : $dataCategory->total,
					$balance,
				], $bodyStyle);
				$iter++;
			}
		}

		if($dataOthers->Income > 0) {
			$balance += $dataOthers->Income;
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				"Lainnya",
				$dataOthers->Income,
				0,
				$balance,
			], $bodyStyle);
			$iter++;
		}

		$writer->writeSheetRow('Sheet1', [
			'', '', '', '', ''
		], $bodyStyle);

		$writer->writeSheetRow('Sheet1', [
			'', 'Pengeluaran', '', '', ''
		], $headerStyle);

		foreach($dataCategories as $dataCategory) {
			if($dataCategory->category->isTypeExpense()) {
				$balance += $dataCategory->total;
				$writer->writeSheetRow('Sheet1', [
					" $iter",
					$dataCategory->category->category_name,
					$dataCategory->category->isTypeIncome() ? $dataCategory->total : 0,
					$dataCategory->category->isTypeIncome() ? 0 : $dataCategory->total,
					$balance,
				], $bodyStyle);
				$iter++;
			}
		}

		if($dataOthers->Expense < 0) {
			$balance += $dataOthers->Expense;
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				"Lainnya",
				0,
				$dataOthers->Expense,
				$balance,
			], $bodyStyle);
			$iter++;
		}

		$writer->writeSheetRow('Sheet1', [
			'', '', '', '', ''
		], $bodyStyle);

		$writer->writeSheetRow('Sheet1', [
			'',
			'Saldo Akhir',
			'',
			'',
			$balance,
		], $bodyStyle);

		$path = \Helper::tempPath($filename);
		$writer->writeToFile($path);

		return $path;
	}



	/**
	 * 
	 * 	Income
	 * 
	 * */
	public static function incomeGenerateDataForReport($request, $filename = null)
	{
		$transactions = self::where('type', 'Income');
		if(empty($filename)) $filename = 'Laporan_Pemasukan';
		$startDate = null;
		$endDate = null;
		$transactionGroup = null;

		if($request->id_transaction_group != 'all') {
			$transactions = $transactions->where('id_transaction_group', $request->id_transaction_group);
			$transactionGroup = TransactionGroup::find($request->id_transaction_group);
		} else {
			$transactionGroupIds = [];
			foreach(auth()->user()->getTransactionGroups() as $group) {
				$transactionGroupIds[] = $group->id;
			}
			$transactions = $transactions->whereIn('id_transaction_group', $transactionGroupIds);
		}

		if(!empty($request->start_date)) {
			$transactions = $transactions->where('date', '>=', $request->start_date);
			$startDate = $request->start_date;
		}

		if(!empty($request->end_date)) {
			$transactions = $transactions->where('date', '<=', $request->end_date);
			$endDate = $request->end_date;
		}

		$transactions = $transactions->orderBy('date', 'asc')
									 ->orderBy('type', 'asc')
									 ->get();

		if(empty($startDate)) {
			$transaction = self::orderBy('date', 'asc');
			if(!empty($request->id_transaction_group)) {
				if($request->id_transaction_group != 'all') {
					$transaction = $transaction->where('id_transaction_group', $request->id_transaction_group);
				}
			}
			$transaction = $transaction->first();
			if($transaction) $startDate = $transaction->date;
		}

		if(empty($endDate)) {
			$transaction = self::orderBy('date', 'desc');
			if(!empty($request->id_transaction_group)) {
				if($request->id_transaction_group != 'all') {
					$transaction = $transaction->where('id_transaction_group', $request->id_transaction_group);
				}
			}
			$transaction = $transaction->first();
			if($transaction) $endDate = $transaction->date;
		}

		return [
			'data'		=> $transactions,
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
			'transactionGroup' => $transactionGroup,
			'filename'	=> $filename,
		];
	}


	public static function incomeGeneratePdfReport($request)
	{
		$data = self::incomeGenerateDataForReport($request);
		$transactions = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$transactionGroup = $data['transactionGroup'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('report.income_pdf', [
			'transactions'	=> $transactions,
			'startDate'		=> $startDate,
			'endDate'		=> $endDate,
			'transactionGroup' => $transactionGroup,
		])->setPaper('A4', 'portrait');
		$filename .= '.pdf';

		return (object) [
			'pdf'		=> $pdf,
			'filename'	=> $filename,
		];
	}


	public static function incomeStreamPdfReport($request)
	{
		$result = self::incomeGeneratePdfReport($request);

		return $result->pdf->stream($result->filename);
	}


	public static function incomeDownloadPdfReport($request)
	{
		$result = self::incomeGeneratePdfReport($request);

		return $result->pdf->download($result->filename);
	}


	public static function incomeDownloadExcelReport($request)
	{
		$data = self::incomeGenerateDataForReport($request);
		$transactions = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$transactionGroup = $data['transactionGroup'];
		$filename = $data['filename'].'.xlsx';

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin', 'widths'=> [ 300, 300, 300, 300 ] ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();

		$totalRow = 0;
		$totalColumn = 3;

		$writer->writeSheetHeader('Sheet1', [
			'Laporan Pemasukan'	=> 'string',
		], [
			'widths'=> [7,15,30,20],
			'font-style'=>'bold', 'halign'=>'center', 'valign' => 'center', 'height'=> 5, 'wrap_text' => true
		]);
		$writer->markMergedCell('Sheet1', $start_row=0, $start_col=0, $end_row=0, $end_col=$totalColumn);
		$totalRow++;

		if(!empty($startDate) && !empty($endDate)) {
			$writer->writeSheetRow('Sheet1', []);
			$totalRow++;

			if($startDate == $endDate) {
				$periode = date('d-m-Y', strtotime($startDate));
			} else {
				$periode = date('d-m-Y', strtotime($startDate)).' s/d '.date('d-m-Y', strtotime($endDate));
			}

			$writer->writeSheetRow('Sheet1', [ 'Periode : '.$periode ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		if($transactionGroup) {
			if(!(!empty($startDate) && !empty($endDate))) {
				$writer->writeSheetRow('Sheet1', []);
				$totalRow++;
			}

			$writer->writeSheetRow('Sheet1', [ 'Grup Transaksi : '.$transactionGroup->transaction_group_name ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		$writer->writeSheetRow('Sheet1', []);

		$writer->writeSheetRow('Sheet1', [
			'No',
			'Tanggal',
			'Keterangan',
			'Nominal',
		], $headerStyle);

		$iter = 1;
		$balance = 0;

		foreach($transactions as $transaction) {
			$balance += $transaction->nominal;
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				$transaction->dateText('d/m/Y'),
				$transaction->description,
				$transaction->nominal,
			], $bodyStyle);
			$iter++;
		}

		$writer->writeSheetRow('Sheet1', [
			'',
			'',
			'Total Pemasukan',
			$balance
		], $bodyStyle);

		$path = \Helper::tempPath($filename);
		$writer->writeToFile($path);

		return $path;
	}









	/**
	 * 
	 * 	Expense
	 * 
	 * */
	public static function expenseGenerateDataForReport($request, $filename = null)
	{
		$transactions = self::where('type', 'Expense');
		if(empty($filename)) $filename = 'Laporan_Pengeluaran';
		$startDate = null;
		$endDate = null;
		$transactionGroup = null;

		if($request->id_transaction_group != 'all') {
			$transactions = $transactions->where('id_transaction_group', $request->id_transaction_group);
			$transactionGroup = TransactionGroup::find($request->id_transaction_group);
		} else {
			$transactionGroupIds = [];
			foreach(auth()->user()->getTransactionGroups() as $group) {
				$transactionGroupIds[] = $group->id;
			}
			$transactions = $transactions->whereIn('id_transaction_group', $transactionGroupIds);
		}

		if(!empty($request->start_date)) {
			$transactions = $transactions->where('date', '>=', $request->start_date);
			$startDate = $request->start_date;
		}

		if(!empty($request->end_date)) {
			$transactions = $transactions->where('date', '<=', $request->end_date);
			$endDate = $request->end_date;
		}

		$transactions = $transactions->orderBy('date', 'asc')
									 ->orderBy('type', 'asc')
									 ->get();

		if(empty($startDate)) {
			$transaction = self::orderBy('date', 'asc');
			if(!empty($request->id_transaction_group)) {
				if($request->id_transaction_group != 'all') {
					$transaction = $transaction->where('id_transaction_group', $request->id_transaction_group);
				}
			}
			$transaction = $transaction->first();
			if($transaction) $startDate = $transaction->date;
		}

		if(empty($endDate)) {
			$transaction = self::orderBy('date', 'desc');
			if(!empty($request->id_transaction_group)) {
				if($request->id_transaction_group != 'all') {
					$transaction = $transaction->where('id_transaction_group', $request->id_transaction_group);
				}
			}
			$transaction = $transaction->first();
			if($transaction) $endDate = $transaction->date;
		}

		return [
			'data'		=> $transactions,
			'startDate'	=> $startDate,
			'endDate'	=> $endDate,
			'transactionGroup' => $transactionGroup,
			'filename'	=> $filename,
		];
	}


	public static function expenseGeneratePdfReport($request)
	{
		$data = self::expenseGenerateDataForReport($request);
		$transactions = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$transactionGroup = $data['transactionGroup'];
		$filename = $data['filename'];

		$pdf = \PDF::loadView('report.expense_pdf', [
			'transactions'	=> $transactions,
			'startDate'		=> $startDate,
			'endDate'		=> $endDate,
			'transactionGroup' => $transactionGroup,
		])->setPaper('A4', 'portrait');
		$filename .= '.pdf';

		return (object) [
			'pdf'		=> $pdf,
			'filename'	=> $filename,
		];
	}


	public static function expenseStreamPdfReport($request)
	{
		$result = self::expenseGeneratePdfReport($request);

		return $result->pdf->stream($result->filename);
	}


	public static function expenseDownloadPdfReport($request)
	{
		$result = self::expenseGeneratePdfReport($request);

		return $result->pdf->download($result->filename);
	}


	public static function expenseDownloadExcelReport($request)
	{
		$data = self::expenseGenerateDataForReport($request);
		$transactions = $data['data'];
		$startDate = $data['startDate'];
		$endDate = $data['endDate'];
		$transactionGroup = $data['transactionGroup'];
		$filename = $data['filename'].'.xlsx';

		$headerStyle = [ 'font-style'=>'bold', 'halign'=>'center', 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin', 'widths'=> [ 300, 300, 300, 300 ] ];
		$bodyStyle = [ 'border'=>'left,right,top,bottom', 'border-color' => '#000', 'border-style' => 'thin' ];

		$writer = new \App\MyClass\XLSXWriter();

		$totalRow = 0;
		$totalColumn = 3;

		$writer->writeSheetHeader('Sheet1', [
			'Laporan Pengeluaran'	=> 'string',
		], [
			'widths'=> [7,15,30,20],
			'font-style'=>'bold', 'halign'=>'center', 'valign' => 'center', 'height'=> 5, 'wrap_text' => true
		]);
		$writer->markMergedCell('Sheet1', $start_row=0, $start_col=0, $end_row=0, $end_col=$totalColumn);
		$totalRow++;

		if(!empty($startDate) && !empty($endDate)) {
			$writer->writeSheetRow('Sheet1', []);
			$totalRow++;

			if($startDate == $endDate) {
				$periode = date('d-m-Y', strtotime($startDate));
			} else {
				$periode = date('d-m-Y', strtotime($startDate)).' s/d '.date('d-m-Y', strtotime($endDate));
			}

			$writer->writeSheetRow('Sheet1', [ 'Periode : '.$periode ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		if($transactionGroup) {
			if(!(!empty($startDate) && !empty($endDate))) {
				$writer->writeSheetRow('Sheet1', []);
				$totalRow++;
			}

			$writer->writeSheetRow('Sheet1', [ 'Grup Transaksi : '.$transactionGroup->transaction_group_name ], [
				'halign'=>'center', 'valign' => 'center',
			]);
			$writer->markMergedCell('Sheet1', $start_row=$totalRow, $start_col=0, $end_row=$totalRow, $end_col=$totalColumn);
			$totalRow++;
		}

		$writer->writeSheetRow('Sheet1', []);

		$writer->writeSheetRow('Sheet1', [
			'No',
			'Tanggal',
			'Keterangan',
			'Nominal',
		], $headerStyle);

		$iter = 1;
		$balance = 0;

		foreach($transactions as $transaction) {
			$balance += $transaction->nominal;
			$writer->writeSheetRow('Sheet1', [
				" $iter",
				$transaction->dateText('d/m/Y'),
				$transaction->description,
				$transaction->nominal * -1,
			], $bodyStyle);
			$iter++;
		}

		$writer->writeSheetRow('Sheet1', [
			'',
			'',
			'Total Pengeluaran',
			$balance * -1
		], $bodyStyle);

		$path = \Helper::tempPath($filename);
		$writer->writeToFile($path);

		return $path;
	}



	public static function sendWeeklyReports()
	{
		ini_set("memory_limit", -1);
		$endOfWeek = now();
		$startOfWeek = now()->addDays(-6);
		$users = [];
		foreach(User::all() as $user) {
			$users[] = $user;
		}

		foreach(ReportReceiver::all() as $reportReceiver) {
			$users[] = $reportReceiver;
		}

		foreach($users as $user)
		{
			if(empty($user->phone_number)) continue;

			$transactionGroups = $user->getTransactionGroups();
			if(count($transactionGroups) > 0)
			{
				$totalBeginBalance = 0;
				$totalIncome = 0;
				$totalExpense = 0;
				$text = "*Laporan Mingguan*";
				$text .= "\n".$startOfWeek->format('d M Y')." - ".$endOfWeek->format('d M Y');
				$filePaths = [];
				foreach($transactionGroups as $transactionGroup)
				{
					$balance = 0;
					$income = 0;
					$expense = 0;
					$transactions = Transaction::where('date', '<', $startOfWeek->format('Y-m-d'))
										->where('id_transaction_group', $transactionGroup->id)
										->get();
					foreach($transactions as $transaction) {
						$balance += $transaction->nominal;
					}

					$transactions = Transaction::where('date', '>=', $startOfWeek->format('Y-m-d'))
										->where('date', '<=', $endOfWeek->format('Y-m-d'))
										->where('id_transaction_group', $transactionGroup->id)
										->get();
					foreach($transactions as $transaction) {
						if($transaction->type == 'Income') {
							$income += $transaction->nominal;
						} else {
							$expense += $transaction->nominal * -1;
						}
					}

					$text .= "\n\n*# {$transactionGroup->transaction_group_name}*";
					$text .= "\nSaldo Awal : Rp. ".number_format($balance);
					$text .= "\nPemasukan : Rp. ".number_format($income);
					$text .= "\nPengeluaran : Rp. ".number_format($expense);
					$text .= "\nSaldo Akhir : Rp. ".number_format($balance + $income - $expense);

					$totalBeginBalance += $balance;
					$totalIncome += $income;
					$totalExpense += $expense;

					$path = \Helper::tempPath(str_replace(' ', '_', $transactionGroup->transaction_group_name)."_{$startOfWeek->format('dMY')}_{$endOfWeek->format('dMY')}".'.pdf');
					$res = self::generatePdfReport((object) [
						'start_date'	=> $startOfWeek->format('Y-m-d'),
						'end_date'		=> $endOfWeek->format('Y-m-d'),
						'id_transaction_group' => $transactionGroup->id,
					]);
					$res->pdf->save($path);
					$filePaths[] = $path;
				}

				$text .= "\n\n*REKAP AKHIR*";
				$text .= "\nTotal Saldo Awal : Rp. ".number_format($totalBeginBalance);
				$text .= "\nTotal Pemasukan : Rp. ".number_format($totalIncome);
				$text .= "\nTotal Pengeluaran : Rp. ".number_format($totalExpense);
				$text .= "\nTotal Saldo Akhir : Rp. ".number_format($totalBeginBalance + $totalIncome - $totalExpense);

				\App\MyClass\Whatsapp::sendChat([
					'to'	=> $user->phone_number,
					'text'	=> $text,
				]);

				$path = \Helper::tempPath("Semua_Transaksi_{$startOfWeek->format('dMY')}_{$endOfWeek->format('dMY')}".'.pdf');
				$res = self::generatePdfReport((object) [
					'start_date'	=> $startOfWeek->format('Y-m-d'),
					'end_date'		=> $endOfWeek->format('Y-m-d'),
					'id_transaction_group' => 'all',
				], null, $user);
				$res->pdf->save($path);
				$filePaths[] = $path;

				foreach($filePaths as $filePath)
				{
					\App\MyClass\Whatsapp::sendMedia([
						'to'	=> $user->phone_number,
						'text'	=> '',
						'path'	=> $filePath,
					]);
					\File::delete($filePath);
				}
			}
		}
	}


	public static function sendMonthlyReports()
	{
		ini_set("memory_limit", -1);
		$endOfMonth = now()->setDay(now()->format('t'));
		$startOfMonth = now()->setDay(1);
		$users = [];
		foreach(User::all() as $user) {
			$users[] = $user;
		}

		foreach(ReportReceiver::all() as $reportReceiver) {
			$users[] = $reportReceiver;
		}

		foreach($users as $user)
		{
			if(empty($user->phone_number)) continue;

			$transactionGroups = $user->getTransactionGroups();
			if(count($transactionGroups) > 0)
			{
				$totalBeginBalance = 0;
				$totalIncome = 0;
				$totalExpense = 0;
				$text = "*Laporan Bulanan*";
				$text .= "\n".$startOfMonth->format('d M Y')." - ".$endOfMonth->format('d M Y');
				$filePaths = [];
				foreach($transactionGroups as $transactionGroup)
				{
					$balance = 0;
					$income = 0;
					$expense = 0;
					$transactions = Transaction::where('date', '<', $startOfMonth->format('Y-m-d'))
										->where('id_transaction_group', $transactionGroup->id)
										->get();
					foreach($transactions as $transaction) {
						$balance += $transaction->nominal;
					}

					$transactions = Transaction::where('date', '>=', $startOfMonth->format('Y-m-d'))
										->where('date', '<=', $endOfMonth->format('Y-m-d'))
										->where('id_transaction_group', $transactionGroup->id)
										->get();
					foreach($transactions as $transaction) {
						if($transaction->type == 'Income') {
							$income += $transaction->nominal;
						} else {
							$expense += $transaction->nominal * -1;
						}
					}

					$text .= "\n\n*# {$transactionGroup->transaction_group_name}*";
					$text .= "\nSaldo Awal : Rp. ".number_format($balance);
					$text .= "\nPemasukan : Rp. ".number_format($income);
					$text .= "\nPengeluaran : Rp. ".number_format($expense);
					$text .= "\nSaldo Akhir : Rp. ".number_format($balance + $income - $expense);

					$totalBeginBalance += $balance;
					$totalIncome += $income;
					$totalExpense += $expense;

					$path = \Helper::tempPath(str_replace(' ', '_', $transactionGroup->transaction_group_name)."_{$startOfMonth->format('dMY')}_{$endOfMonth->format('dMY')}".'.pdf');
					$res = self::generatePdfReport((object) [
						'start_date'	=> $startOfMonth->format('Y-m-d'),
						'end_date'		=> $endOfMonth->format('Y-m-d'),
						'id_transaction_group' => $transactionGroup->id,
					]);
					$res->pdf->save($path);
					$filePaths[] = $path;
				}

				$text .= "\n\n*REKAP AKHIR*";
				$text .= "\nTotal Saldo Awal : Rp. ".number_format($totalBeginBalance);
				$text .= "\nTotal Pemasukan : Rp. ".number_format($totalIncome);
				$text .= "\nTotal Pengeluaran : Rp. ".number_format($totalExpense);
				$text .= "\nTotal Saldo Akhir : Rp. ".number_format($totalBeginBalance + $totalIncome - $totalExpense);

				\App\MyClass\Whatsapp::sendChat([
					'to'	=> $user->phone_number,
					'text'	=> $text,
				]);

				$path = \Helper::tempPath("Semua_Transaksi_{$startOfMonth->format('dMY')}_{$endOfMonth->format('dMY')}".'.pdf');
				$res = self::generatePdfReport((object) [
					'start_date'	=> $startOfMonth->format('Y-m-d'),
					'end_date'		=> $endOfMonth->format('Y-m-d'),
					'id_transaction_group' => 'all',
				], null, $user);
				$res->pdf->save($path);
				$filePaths[] = $path;

				foreach($filePaths as $filePath)
				{
					\App\MyClass\Whatsapp::sendMedia([
						'to'	=> $user->phone_number,
						'text'	=> '',
						'path'	=> $filePath,
					]);
					\File::delete($filePath);
				}
			}
		}
	}
}
