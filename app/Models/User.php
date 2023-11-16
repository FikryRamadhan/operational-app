<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	protected $table = 'users';
	protected $fillable = [ 'name', 'email', 'phone_number', 'password', 'role', 'avatar' ];

	protected $hidden = [ 'password', 'remember_token' ];
	protected $casts = [ 'email_verified_at' => 'datetime' ];

	const ROLE_OWNER    = 'Owner';
	const ROLE_STAFF    = 'Staff';


	/**
	 * 	Relationship
	 * */
	public function transactions()
	{
		return $this->hasMany('App\Models\Transaction', 'id_user');
	}

	public function transactionGroupAccesses()
	{
		return $this->hasMany('App\Models\TransactionGroupAccess', 'id_user')
					->whereHas('transactionGroup', function($query){
						$query->where('is_active', 'yes');
					})
					->with([ 'transactionGroup' ]);
	}

    public function incomingGood(){
		return $this->hasMany(IncomingGood::class);
	}


	/**
	 * 	CRUD methods
	 * */
	public static function createUser($request)
	{
		$user = self::create($request->except([ 'password' ]));
		$user->setPassword($request->password);
		return $user;
	}

	public function updateUser($request)
	{
		$this->update($request->except([ 'password' ]));

		if(!empty($request->password)) {
			$this->setPassword($request->password);
		}
		return $this;
	}

	public function deleteUser()
	{
		return $this->delete();
	}


	/**
	 *  Helper methods
	 * */
	public function isHasAvatar()
	{
		if(empty($this->avatar)) return false;
		return \File::exists($this->avatarPath());
	}

	public function avatarPath()
	{
		return storage_path('app/public/avatars/'.$this->avatar);
	}

	public function avatarLink()
	{
		if($this->isHasAvatar()) {
			return url('storage/avatars/'.$this->avatar);
		}

		return url('img/default-avatar.jpg');
	}

	public function setAvatar($request)
	{
		if(!empty($request->upload_avatar)) {
			$this->removeAvatar();
			$file = $request->file('upload_avatar');
			$filename = date('YmdHis_').$file->getClientOriginalName();
			$file->move(storage_path('app/public/avatars'), $filename);
			$this->update([
				'avatar' => $filename,
			]);
		}

		return $this;
	}

	public function removeAvatar()
	{
		if($this->isHasAvatar()) {
			\File::delete($this->avatarPath());
			$this->update([
				'avatar' => null,
			]);
		}

		return $this;
	}

	public function setPassword($password)
	{
		$this->update([
			'password'	=> \Hash::make($password)
		]);
		return $this;
	}

	public function comparePassword($password)
	{
		return \Hash::check($password, $this->password);
	}

	public function isOwner()
	{
		return $this->role == self::ROLE_OWNER;
	}

	public function isStaff()
	{
		return $this->role == self::ROLE_STAFF;
	}

	public function getTransactionGroups()
	{
		if($this->isOwner()) {
			return TransactionGroup::where('is_active', 'yes')->get();
		} else {
			$accesses = $this->transactionGroupAccesses;
			$groups = [];
			foreach($accesses as $access) {
				$groups[] = $access->transactionGroup;
			}
			return $groups;
		}
	}


	/**
	 * 	Static methods
	 * */
	public static function dataTable($request)
	{
		$data = self::select([ 'users.*' ]);

		return datatables()->eloquent($data)
			->addColumn('action', function ($data) {
				$action = '
					<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="'.route('user.edit', $data->id).'">
								<i class="fas fa-pencil-alt mr-1"></i> Edit
							</a>
							<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong>'.$data->name.'</strong>?" data-delete-href="'.route('user.destroy', $data->id).'">
								<i class="fas fa-trash mr-1"></i> Hapus
							</a>
						</div>
					</div>';
				return $action;
			})
			->rawColumns(['action'])
			->addIndexColumn()
			->make(true);
	}

}
