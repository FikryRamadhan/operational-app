<?php

namespace App\Models;

use App\Models\Reminder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReminderDetail extends Model
{
	use HasFactory;

	protected $fillable = [ 'id_reminder', 'reminder_on', 'reminder_minutes', 'reminder_time' ];


	/**
	 * 	Relationship methods
	 * */
	public function reminder()
	{
		return $this->belongsTo(Reminder::class, 'id_reminder');
	}



	/**
	 * 	Helper methods
	 * */
	public function reminderTimeFormatted($format = 'd M Y H:i')
	{
		return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->reminder_time)->format($format);
	}

	public function isPassed()
	{
		return strtotime($this->reminder_time) <= strtotime(date('Y-m-d H:i:s'));
	}
}
