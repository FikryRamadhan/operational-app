<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Supplier extends Model
{
    use HasFactory;

    protected  $guarded = [''];

	public function incomingGoods(){
		return $this->hasMany(Incoming_goods::class);
	}

    public static function dataTable($request){
        $data = self::select([ 'suppliers.*' ]);

        return \DataTables::eloquent($data)
            ->addColumn('action', function ($data) {
                $action = '
                <div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item edit" href="javascript:void(0);" data-edit-href="' . route('supplier.update', $data->id) . '" data-get-href="' . route('supplier.get', $data->id) . '">
								<i class="fas fa-pencil-alt mr-1"></i> Edit
							</a>
							<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong>' . $data->supplier_name . '</strong>?" data-delete-href="' . route('supplier.destroy', $data->id) . '">
								<i class="fas fa-trash mr-1"></i> Hapus
							</a>
						</div>
					</div>
                ';
                return $action;
                })
            ->rawColumns([
                'action'
            ])
            ->make(true);
    }

    public static function createSupplier(array $request){
        return self::create($request);
    }

    public function updateSupplier(array $request)
	{
		$this->update($request);
		return $this;
	}

    public function deleteSupplier()
	{
		return $this->delete();
	}

    public  static function importExpenseSupplierFromExcel($request){
		return self::importFromExcel($request);
	}

    public static function importFromExcel($request) {
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

					if(!empty($row[0])) {
						$supplier = self::where('supplier_name', $row[0])
										->first();

						if(!$supplier) {
							DB::beginTransaction();
							try {
								self::create([
									'supplier_name'=> $row[0],
                                    'address' => $row[1],
                                    'phone_number' => $row[2],
                                    'email' => $row[3],
                                    'description' => $row[4]
								]);
								$amount++;
								DB::commit();
							} catch (Exception $e) {
								DB::rollback();
							}
						}
					}
				}
			}

			\File::delete($path);
		}

		return $amount;
    }
}
