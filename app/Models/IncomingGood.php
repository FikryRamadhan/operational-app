<?php

namespace App\Models;

use App\MyClass\MyClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class IncomingGood extends Model
{
    use HasFactory;


    protected $dates =['created_at', 'updated_at', 'date'];
    protected $guarded = [''];

    // Method Action
    public static function storeIncomingGoods($request){
        return self::create($request);
    }

    public function  updateIncomingGoods($request){
        $this->update($request);
        return $this;
    }

    public function deleteIncomingGoods(){
        $this->removeAmountWarehouse();
        $this->removeStockProduct();
        $this->removeIncomingGoodDetails();
        return $this->delete();
    }

    public function removeAmountWarehouse(){
        if($this->incomingGoodDetail){
            foreach ($this->incomingGoodDetail as $key => $incomingGoodDetail) {
                $amount = $incomingGoodDetail->amount;

                // Mengurangi Amount warehouse stock
                if($incomingGoodDetail->product->warehouseStock) {
                    foreach ($incomingGoodDetail->product->warehouseStock as $key => $warehouseStock) {
                        if($warehouseStock->id_warehouse == $incomingGoodDetail->incomingGoods->id_warehouse){
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
        if($this->incomingGoodDetail){
            foreach ($this->incomingGoodDetail as $key => $incomingGoodDetail) {
                $amount = $incomingGoodDetail->amount;

                // Mengurangi Stock Product
                if($incomingGoodDetail->product){
                    $stockProduct = $incomingGoodDetail->product->stock;
                    $amountProduct = $amount;
                    $totalStockProduct = $stockProduct - $amountProduct;
                    $incomingGoodDetail->product->update([
                    'stock' => $totalStockProduct
                    ]);
                }
            }
        }
    }

    public function removeIncomingGoodDetails(){
        IncomingGoodDetail::where('id_incoming_good', $this->id)->delete();
        return $this;
    }

    // Relasi
    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function getSupplier(){
        return $this->supplier?$this->supplier->supplier_name:"-";
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class, 'id_warehouse');
    }

    public function incomingGoodDetail(){
        return $this->hasMany(incomingGoodDetail::class, 'id_incoming_good');
    }

    public static function getIncomingGoodDetailsById($idIncomingGood){
        return incomingGoodDetail::where('id_incoming_good', $idIncomingGood)->with('product')->get();
    }

    public function getWarehouse(){
        return $this->warehouse?$this->warehouse->warehouse_name:"-";
    }

    // public function getDateFormat()
    // {
    //     $date = $this->date;
    //     return Carbon::getDateFormat('Y-m-d', $date)
    //         ->date('d-m-Y');
    // }

    // Data Table
    public static function dataTable(){
        $data = self::select([ 'incoming_goods.*' ])
            ->with('supplier', 'warehouse')
            ->leftJoin('suppliers', 'incoming_goods.id_supplier', '=', 'suppliers.id')
            ->leftJoin('warehouses', 'incoming_goods.id_warehouse', '=', 'warehouses.id');

        return DataTables::eloquent($data)
        ->addColumn('action', function ($data) {
            $incomingGoods = $data->id;
            $action = '
                <div class="dropdown">
                    <button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Pilih Aksi
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="'.route('incoming-goods.detail', $data->id).'">
							<i class="fas fa-search mr-1"></i> Detail
						</a>
                        <a class="dropdown-item edit" href="'.route('incoming-goods.edit', $data->id).'">
                            <i class="fas fa-pencil-alt mr-1"></i> Edit
                        </a>
                        <a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong> Data Ini </strong>?" data-delete-href="' . route('incoming-goods.destroy', $incomingGoods) . '">
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
        ->editColumn('supplier.supplier_name', function($data){
            return $data->getSupplier();
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
            'kodeBarang' => 'BRG-MSK',
            'urutan' => $urutan,
        ];

        return MyClass::formatNoTransaksi($data);
    }
}