<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportReceiverDetail extends Model
{
	use HasFactory;

	protected $fillable = [ 'id_report_receiver', 'id_transaction_group' ];


	/**
	 * 	Relationship methods
	 * */
	public function reportReceiver()
	{
		return $this->belongsTo('App\Models\ReportReceiver', 'id_report_receiver');
	}

	public function transactionGroup()
	{
		return $this->belongsTo('App\Models\TransactionGroup', 'id_transaction_group');
	}
}
