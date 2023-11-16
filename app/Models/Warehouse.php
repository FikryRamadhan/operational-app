<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Warehouse extends Model
{
    protected $fillable =
    [
        'warehouse_name',
        'description'
    ];

    use HasFactory;
    use SoftDeletes;

    // Method Action
    public static function createWarehouse(array $request)
    {
        return self::create($request);
    }
    public function updateWarehouse(array $request)
    {
        $this->update($request);
        return $this;
    }
    
    public function deleteWarehouse()
    {
        return $this->delete();
    }

    // Relationship
    public function warehouseStock(){
        return $this->hasMany(WarehouseStock::class, 'id_warehouse');
    }

    public static function getProductById($idWarehouse){
        return WarehouseStock::where('id_warehouse' ,$idWarehouse)->get();
    }

    // Data Table
    public static function dataTable()
    {
        $data = self::select(['warehouses.*']);

        return \DataTables::eloquent($data)
            ->addColumn('action', function ($data) {
                $action = '
                        <div class="dropdown">
                            <button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Pilih Aksi
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item edit" href="javascript:void(0);" data-edit-href="' . route('warehouse.update', $data->id) . '" data-get-href="' . route('warehouse.get', $data->id) . '">
                                    <i class="fas fa-pencil-alt mr-1"></i> Edit
                                </a>
                                <a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong>' . $data->warehouse_name . '</strong>?" data-delete-href="' . route('warehouse.destroy', $data->id) . '">
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

    public  static function importWarehouseFromExcel($request)
    {
        return self::importFromExcel($request);
    }

    public static function importFromExcel($request)
    {
        $amount = 0;

        if (!empty($request->file_excel)) {
            $file = $request->file('file_excel');
            $filename = date('YmdHis_') . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(storage_path('app/public/temp_files'), $filename);
            $path = storage_path('app/public/temp_files/' . $filename);
            $parseData = \App\MyClass\SimpleXLSX::parse($path);

            if ($parseData) {
                $iter = 0;
                foreach ($parseData->rows() as $row) {
                    $iter++;
                    if ($iter == 1) continue;

                    if (!empty($row[0])) {
                        $warehouse = self::where('warehouse_name', $row[0])
                            ->first();

                        if (!$warehouse) {
                            DB::beginTransaction();
                            try {
                                self::create([
                                    'warehouse_name' => $row[0],
                                    'description' => $row[1],
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
    }
}
