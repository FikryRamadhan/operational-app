<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportReceiver extends Model
{
	use HasFactory;

	protected $fillable = [ 'name', 'phone_number', 'notes' ];


	/**
	 * 	Relationship methods
	 * */
	public function reportReceiverDetails()
	{
		return $this->hasMany('App\Models\ReportReceiverDetail', 'id_report_receiver')
					->has('transactionGroup');
	}



	/**
	 *  CRUD methods
	 * */
	public static function createReportReceiver($request)
	{
		$receiver = self::create($request->all());
		$receiver->createReportReceiverDetails($request);
		return $receiver;
	}

	public function updateReportReceiver($request)
	{
		$this->update($request->all());
		$this->removeReportReceiverDetails();
		$this->createReportReceiverDetails($request);
		return $this;
	}

	public function deleteReportReceiver()
	{
		$this->removeReportReceiverDetails();
		return $this->delete();
	}



	/**
	 * 	Helper methods
	 * */
	public function removeReportReceiverDetails()
	{
		ReportReceiverDetail::where('id_report_receiver', $this->id)->delete();
		return $this;
	}

	public function createReportReceiverDetails($request)
	{
		$groupTransactionId = [];
		foreach($request->id_transaction_group as $id) {
			if(!in_array($id, $groupTransactionId)) {
				$groupTransactionId[] = $id;
			}
		}

		foreach($groupTransactionId as $id) {
			ReportReceiverDetail::create([
				'id_report_receiver'	=> $this->id,
				'id_transaction_group'	=> $id,
			]);
		}

		return $this;
	}

	public function getTransactionGroups()
	{
		$details = $this->reportReceiverDetails;
		$groups = [];
		foreach($details as $detail) {
			$groups[] = $detail->transactionGroup;
		}

		return $groups;
	}


	/**
	 *  Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'report_receivers.*' ])
					->with([ 'reportReceiverDetails.transactionGroup' ]);

		return \Datatables::eloquent($data)
			->addColumn('transaction_group', function($data){
				$html = '';
				$i = 0;
				foreach($data->reportReceiverDetails as $detail) {
					if($transactionGroup = $detail->transactionGroup) {
						if($i > 0) $html .= '<br>';
						$html .= $transactionGroup->transaction_group_name;
					}
					$i++;
				}
				return $html;
			})
			->addColumn('action', function ($data) {
				$action = '
					<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('report_receiver.edit', $data->id).'">
								<i class="fas fa-pencil-alt mr-1"></i> Edit
							</a>
							<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus?" data-delete-href="'.route('report_receiver.destroy', $data->id).'">
								<i class="fas fa-trash mr-1"></i> Hapus
							</a>
						</div>
					</div>';
				return $action;
			})
			->rawColumns([ 'transaction_group', 'action' ])
			->make(true);
	}
}
