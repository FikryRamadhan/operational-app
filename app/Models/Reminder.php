<?php

namespace App\Models;

use function Termwind\ask;
use Illuminate\Support\Facades\Log;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{
	use HasFactory;

	protected $fillable = ['reminder_name', 'time', 'status', 'date', 'notes', 'reminder_target'];


	/**
	 *  Relationship methods
	 * */
	public function reminderDetails()
	{
		return $this->hasMany('App\Models\ReminderDetail', 'id_reminder')
			->orderBy('reminder_minutes');
	}



	/**
	 *  CRUD methods
	 * */
	public static function createReminder($request)
	{
		$reminder = self::create($request->all());
		$reminder->createReminderDetails($request);
		return $reminder;
	}

	public function updateReminder($request)
	{
		$this->update($request->all());
		$this->removeReminderDetails();
		$this->createReminderDetails($request);
		return $this;
	}

	public function deleteReminder()
	{
		$this->removeReminderDetails();
		return $this->delete();
	}

	// public function belumDiSelesaikan()
	// {
	// 	return $this->status == 'Belum Di Selesaikan';
	// }

	// public function sudahDiselesaikan()
	// {
	// 	return $this->status == 'Sudah Di Selesaikan';
	// }

	public function isStatus()
	{
		return $this->status == 'Sudah Di Selesaikan';
	}

	public function statusTextHtml()
	{
		if ($this->isStatus()) {
			return '<span class="text-success"> <i class="fas fa-check-circle"></i> Sudah Di Selesaikan </span>';
		} else {
			return '<span class="text-danger"> <i class="fas fa-times-circle"></i> Belum Di Selesaikan </span>';
		}
	}

	public function status()
	{
		$this->update([
			'status'	=> 'Sudah Di Selesaikan',
		]);
		return $this;
	}

	public function noStatus(){
		$this->status == 'Belum Di Selesaikan';
	}



	/**
	 *  Helper methods
	 * */
	public function removeReminderDetails()
	{
		ReminderDetail::where('id_reminder', $this->id)->delete();
		return $this;
	}

	public function createReminderDetails($request)
	{
		$on = $request->reminder_detail['on'];
		$minutes = $request->reminder_detail['minutes'];
		$iter = 0;

		foreach ($on as $o) {
			$time = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->date . ' 00:00:00')->addMinutes(-$minutes[$iter]);
			ReminderDetail::create([
				'id_reminder'		=> $this->id,
				'reminder_on' 		=> $o,
				'reminder_minutes'	=> $minutes[$iter],
				'reminder_time'		=> $time->format('Y-m-d H:i:s'),
			]);
			$iter++;
		}

		return $this;
	}

	public function dateFormatted($format = 'd M Y')
	{
		return date($format, strtotime($this->date));
	}

	public function notesHtml()
	{
		if (empty($this->notes)) return '-';
		return str_replace("\n", '<br>', $this->notes);
	}

	public function reminderTarget()
	{
		$data = explode(',', $this->reminder_target);
		$result = [];
		foreach ($data as $d) {
			if (trim($d)) $result[] = trim($d);
		}

		return $result;
	}


	/**
	 *  Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select(['reminders.*']);

		return \Datatables::eloquent($data)
			->addColumn('action', function ($data) {
				$action = '
					<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">';
							if (!$data->isStatus()) {
								$action .= '
									<a class="dropdown-item check" href="javascript:void(0)" data-check-message="Sudah Di Selesaikan <strong></strong>?" data-check-href="' . route('reminder.check', $data->id) . '">
										<i class="fas fa-check mr-1"></i> Verifikasi
									</a>';
							}
							if (!$data->noStatus()){
								$action .= '
									<a class="dropdown-item" href="' . route('reminder.detail', $data->id) . '">
										<i class="fas fa-search mr-1"></i> Detail
										</a>
									<a class="dropdown-item" href="' . route('reminder.edit', $data->id) . '">
										<i class="fas fa-pencil-alt mr-1"></i> Edit
									</a>
									<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus?" data-delete-href="' . route('reminder.destroy', $data->id) . '">
										<i class="fas fa-trash mr-1"></i> Hapus
									</a>';
							}	
						'</div>
					</div>';

				return $action;
			})
			->editColumn('created_at', function ($data) {
				return $data->created_at->format('d M Y H:i');
			})
			->editColumn('notes', function ($data) {
				return $data->notesHtml();
			})
			->editColumn('status', function ($data) {
				return $data->statusTextHtml();
			})
			->rawColumns(['notes', 'action', 'status'])
			->make(true);
	}


	public static function sendNotification()
	{
		$details = ReminderDetail::has('reminder')
			->with(['reminder'])
            ->whereHas('reminder', function($q){
                $q->where('status', 'Belum Di Selesaikan');
            })
			->where('reminder_time', 'like', '%' . date('Y-m-d H:i:s') . '%')
			->get();


		foreach ($details as $detail) {
			$time = '';
			if ($detail->reminder_minutes == 10) $time = '10 Menit Lagi';
			if ($detail->reminder_minutes == 30) $time = '30 Menit Lagi';
			if ($detail->reminder_minutes == 60) $time = '1 Jam Lagi';
			if ($detail->reminder_minutes == 180) $time = '3 Jam Lagi';
			if ($detail->reminder_minutes == 360) $time = '6 Jam Lagi';
			if ($detail->reminder_minutes == 720) $time = '12 Jam Lagi';
			if ($detail->reminder_minutes == 1440) $time = '1 Hari Lagi';
			if ($detail->reminder_minutes == 10080) $time = '1 Minggu Lagi';
			if ($detail->reminder_minutes == 20160) $time = '2 Minggu Lagi';
			if ($detail->reminder_minutes == 43200) $time = '1 Bulan Lagi';
			if ($detail->reminder_minutes == 86400) $time = '2 Bulan Lagi';
			if ($detail->reminder_minutes == 129600) $time = '3 Bulan Lagi';
			if ($detail->reminder_minutes == 259200) $time = '6 Bulan Lagi';

			$reminder = $detail->reminder;
			$message = "Pesan Reminder";
			$message .= "\n\n*" . $reminder->reminder_name . "*";
			$message .= "\nTanggal Acara " . $reminder->dateFormatted('d F Y') . " (" . $time . ")";
			if (!empty($reminder->notes)) {
				$message .= "\n" . $reminder->notes;
			}

			foreach ($reminder->reminderTarget() as $target) {
				\App\MyClass\Whatsapp::sendChat([
					'to'	=> $target,
					'text'	=> $message
				]);
			}
		}
	}
}