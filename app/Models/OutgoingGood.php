<?php

namespace App\Models;

use App\MyClass\MyClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;


class OutgoingGood extends Model
{
    use HasFactory;

    protected $dates = ['created_at', 'updated_at', 'date'];
    protected $guarded = [''];


    // Relasi
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'id_warehouse');
    }

    public function getWarehouse()
    {
        return $this->warehouse ? $this->warehouse->warehouse_name : "-";
    }

    public function outGoingGoodDetail()
    {
        return $this->hasMany(OutgoingGoodDetail::class, 'id_outgoing_good');
    }

    public static function getOutgoingGoodDetailsById($idOutgoingGood)
    {
        return OutgoingGoodDetail::where('id_outgoing_good', $idOutgoingGood)->with('product')->get();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }


    // Crud
    public static function storeOutgoingGood($request)
    {
        return self::create($request);
    }

    public function updateOutgoinGood($request)
    {
        return $this->update($request);
    }

    public function deleteOutgoinGood()
    {
        $this->removeAmountWarehouse();
        $this->removeStockProduct();
        $this->removeOutgoingGoodDetails();
        return $this->delete();
    }


    public function removeAmountWarehouse() {
        if($this->outGoingGoodDetail){
            foreach ($this->outGoingGoodDetail as $key => $value) {
                $amount = $value->amount;
                
                // Mengurangi Amount warehouse stock
                if($value->product->WarehouseStock){
                    // dd($value->product->WarehouseStock);
                    foreach ($value->product->warehouseStock as $key => $warehouseStock) {
                        if($warehouseStock->id_warehouse == $value->outgoingGood->id_warehouse){
                            $stockProduct = $warehouseStock->stock;
                            $amountProduct = $amount;
                            $totalStockProduct = $stockProduct + $amountProduct;
                            $warehouseStock->update([
                                'stock' => $totalStockProduct
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function removeStockProduct()
    {
        if($this->outGoingGoodDetail){
            foreach ($this->outGoingGoodDetail as $key => $outGoingGoodDetail) {
                $amount = $outGoingGoodDetail->amount;

                // Mengurangi Stock Product
                if($outGoingGoodDetail->product){
                    $stockProduct = $outGoingGoodDetail->product->stock;
                    $amountProduct = $amount;
                    $totalStockProduct = $stockProduct + $amountProduct;
                    $outGoingGoodDetail->product->update([
                        'stock' => $totalStockProduct
                    ]);
                }
            }
        }
    }

    public function removeOutgoingGoodDetails()
    {
        OutgoingGoodDetail::where('id_outgoing_good', $this->id)->delete();
        foreach ($this->outGoingGoodDetail as $key => $outGoingGoodD) {
            $outGoingGoodD->removePhoto();
            $outGoingGoodD->delete();
        }
        return $this;
    }
    

   

    // Data Table
    public static function dataTable()
    {
        $data = self::select(['outgoing_goods.*'])
        ->with('warehouse')
        ->leftJoin('warehouses', 'outgoing_goods.id_warehouse', '=', 'warehouses.id');

        return DataTables::eloquent($data)
            ->addColumn('action', function ($data) {
                $outgoingGoods = $data->id;
                $action = '
                <div class="dropdown">
                    <button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Pilih Aksi
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="' . route('outgoing-goods.detail', $data->id) . '">
							<i class="fas fa-search mr-1"></i> Detail
						</a>
                        <a class="dropdown-item edit" href="' . route('outgoing-goods.edit', $data->id) . '">
                            <i class="fas fa-pencil-alt mr-1"></i> Edit
                        </a>
                        <a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong> Data Ini </strong>?" data-delete-href="' . route('outgoing-goods.destroy', $outgoingGoods) . '">
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
            ->editColumn('warehouse.warehouse_name', function ($data) {
                return $data->getWarehouse();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public static function createFormatTransaksi($idOutgoingGood = null)
    {
        // Ambil Tahun Saat Ini
        $tahunIni = date('Y');


        // Cari Transaksi Di Tahun Ini
        $transaksiTerbaru = self::whereBetween('date', [$tahunIni . '-01-01', $tahunIni . '-12-31']);
        if ($idOutgoingGood != null) {
            $transaksiTerbaru->whereNotIn('id', [$idOutgoingGood]);
        }
        $transaksiTerbaru->orderBy('created_at', 'DESC')->first();
        $transaksi = $transaksiTerbaru->first();

        if ($transaksi) {
            $noTransaksi = $transaksi->transaction_number;
            $explode = explode('/', $noTransaksi);
            $noUrut = (int) $explode[0];
            $noUrut++;
        } else {
            $noUrut = 1;
        }
        $urutan = str_pad($noUrut, 4, 0, STR_PAD_LEFT);

        $data = [
            'perusahaan' => 'ADIVA',
            'tanggal' => now(),
            'kodeBarang' => 'BRG-KLR',
            'urutan' => $urutan,
        ];

        return MyClass::formatNoTransaksi($data);
    }
}