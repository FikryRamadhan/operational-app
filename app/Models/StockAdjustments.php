<?php

namespace App\Models;

use App\MyClass\MyClass;
use Yajra\DataTables\DataTables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockAdjustments extends Model
{
    protected $dates =['created_at', 'updated_at', 'date'];
    protected $guarded = [''];

    // Method Action
    public static function storeStockAdjustment($request){
        return self::create($request);
    }

    public function  updateStockAdjustment($request){
        $this->update($request);
        return $this;
    }

    public function deleteStockAdjustment(){
        $this->removeAmountWarehouse();
        $this->removeStockProduct();
        $this->removeStockAdjustmentDetail();
        return $this->delete();
    }

    public function removeAmountWarehouse(){
        if($this->stockAdjustmentDetail){
            foreach ($this->stockAdjustmentDetail as $key => $stockAdjustmentDetail) {
                $amount = $stockAdjustmentDetail->amount;

                // Mengurangi Amount warehouse stock
                if($stockAdjustmentDetail->product->warehouseStock) {
                    foreach ($stockAdjustmentDetail->product->warehouseStock as $key => $warehouseStock) {
                        if($warehouseStock->id_warehouse == $stockAdjustmentDetail->stockAdjustment->id_warehouse){
                            $stockProduct = $warehouseStock->stock;
                            $amountProduct = $amount;
                            $totalStockProduct = $stockProduct - $amountProduct;
                                $warehouseStock->update([
                                    'stock' => $totalStockProduct
                                ]);
                        }
                    }
                };
            }
        }
    }

    public function removeStockProduct(){
        if($this->stockAdjustmentDetail){
            foreach ($this->stockAdjustmentDetail as $key => $stockAdjustmentDetail) {
                $amount = $stockAdjustmentDetail->amount;

                // Mengurangi Stock Product
                if($stockAdjustmentDetail->product){
                    $stockProduct = $stockAdjustmentDetail->product->stock;
                    $amountProduct = $amount;
                    $totalStockProduct = $stockProduct - $amountProduct;
                    $stockAdjustmentDetail->product->update([
                    'stock' => $totalStockProduct
                    ]);
                }
            }
        }
    }

    public function removeStockAdjustmentDetail(){
        StockAdjustmentDetails::where('id_stock_adjustment', $this->id)->delete();
        return $this;
    }


    // Relasi
    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'id_warehouse');
    }

    public function stockAdjustmentDetail(){
        return $this->hasMany(StockAdjustmentDetails::class, 'id_stock_adjustment');
    }

    public static function getStockAdjustmentDetailById($idStockAdjustment){
        return StockAdjustmentDetails::where('id_stock_adjustment', $idStockAdjustment)->with('product')->get();
    }

    public function getWarehouse(){
        return $this->warehouse?$this->warehouse->warehouse_name:"-";
    }

    // Data Table
    public static function dataTable(){
        $data = self::select([ 'stock_adjustments.*' ])
            ->with('warehouse')
            ->leftJoin('warehouses', 'stock_adjustments.id_warehouse', '=', 'warehouses.id');

        return \DataTables::eloquent($data)
        ->addColumn('action', function ($data) {
            $stockAdjustment = $data->id;
            $action = '
                <div class="dropdown">
                    <button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Pilih Aksi
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="'.route('stock-adjustment.detail', $data->id).'">
							<i class="fas fa-search mr-1"></i> Detail
						</a>
                        <a class="dropdown-item edit" href="'.route('stock-adjustment.edit', $data->id).'">
                            <i class="fas fa-pencil-alt mr-1"></i> Edit
                        </a>
                        <a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong> Data Ini </strong>?" data-delete-href="' . route('stock-adjustment.destroy', $stockAdjustment) . '">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </a>
                    </div>
                </div>
            ';
            return $action;
        })
        ->editColumn('date', function($data){
            return $data->date->format('d F y');
        })
        ->editColumn('warehouse.warehouse_name', function($data){
            return $data->getWarehouse();
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public static function createFormatTransaksi($idIncomingGood = null){
        // Ambil Tahun Saat Ini
        $tahunIni = date('Y');
        

        // Cari Transaksi Di Tahun Ini
        $transaksiTerbaru = self::whereBetween('date', [$tahunIni.'-01-01', $tahunIni.'-12-31']);
        if($idIncomingGood != null) {
            $transaksiTerbaru->whereNotIn('id', [$idIncomingGood]);
        }
        $transaksiTerbaru->orderBy('created_at', 'DESC')->first();
        $transaksi = $transaksiTerbaru->first();
        
        if($transaksi){
            $noTransaksi = $transaksi->transaction_number;
            $explode = explode('/', $noTransaksi);
            $noUrut = (int) $explode[0];
            $noUrut++;
        }else {
            $noUrut = 1;
        }
        $urutan = str_pad($noUrut,4,0,STR_PAD_LEFT);

        $data = [
            'perusahaan' => 'ADIVA',
            'tanggal' => now(),
            'kodeBarang' => 'TBH-BRG',
            'urutan' => $urutan,
        ];

        return MyClass::formatNoTransaksi($data);
    }
}

