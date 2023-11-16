<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Exception;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

	public function product(){
		return $this->hasMany(Product::class);
	}

    public static function dataTable($request)
    {
        $data = self::select([ 'brands.*' ]);

        return \DataTables::eloquent($data)
            ->addColumn('action', function ($data) {
                $action = '
                	<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item edit" href="javascript:void(0);" data-edit-href="' . route('brand.update', $data->id) . '" data-get-href="' . route('brand.get', $data->id) . '">
								<i class="fas fa-pencil-alt mr-1"></i> Edit
							</a>
							<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong>' . $data->brand_name . '</strong>?" data-delete-href="' . route('brand.destroy', $data->id) . '">
								<i class="fas fa-trash mr-1"></i> Hapus
							</a>
						</div>
					</div>
                ';
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public static function createBrand(array $request){
        return self::create($request);
    }

    public function updateBrand(array $request)
	{
		$this->update($request);
		return $this;
	}

    public function deleteBrand()
	{
		return $this->delete();
	}

    public  static function importExpenseBrandFromExcel($request){
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
						$brand = self::where('brand_name', $row[0])
										->first();

						if(!$brand) {
							DB::beginTransaction();
							try {
								self::create([
									'brand_name'=> $row[0],
                                    'notes'=> $row[1],
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

	public static function checkBrand($brandName){
		return self::where('brand_name', $brandName)->first();
	}

	public static function buatBrand($brandName) {
		return self::create([
			'brand_name' => $brandName,
		]);
	}

}
