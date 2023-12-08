<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = ['id_warehouse', 'id_product', 'stock'];

    // Relationship
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'id_warehouse');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    public function getWarehouseName()
    {
        return $this->warehouse ? $this->warehouse->warehouse_name : "-";
    }

    public function getProductType(){
        $product = $this->product;
        return $product->productType ? $product->productType->product_type_name : "-";
    }
    public function getProduct()
    {
        return $this->product ? $this->product->product_name : "-";
    }

    public static function getMinimalStock()
    {
        return self::whereHas('product', function ($q){
            $q->whereColumn('stock', '<', 'minimal_stock');
        })
        ->get();
    }

    public static function getStockToWhatsapp()
    {
        return self::whereHas('product', function ($q){
            $q->whereColumn('stock', '<', 'minimal_stock');
        })
        ->get()
        ->groupBy('warehouse.warehouse_name');
    }

    // CRUD
    public static function checkProduct(array $data)
    {
        $idWarehouse = $data['idWarehouse'];
        $idProduct = $data['idProduct'];
        return  self::where('id_warehouse', $idWarehouse)->where('id_product', $idProduct)->first();
    }

    public static function storeWarehouseStock(array $data)
    {
        $idWarehouse = $data['idWarehouse'];
        $idProduct = $data['idProduct'];
        $stock = $data['stock'];
        $stockOld = 0;

        $warehouseStock = self::checkProduct($data);

        if (!$warehouseStock) {
            $warehouseStock = self::create([
                'id_warehouse' => $idWarehouse,
                'id_product' => $idProduct,
            ]);
        }

        // Old Stock
        $stockOld = $warehouseStock->stock;
        // Total Stock
        $totalStock = $stockOld + $stock;

        $warehouseStock->update([
            'stock' => $totalStock
        ]);

        return $warehouseStock;
    }

    public static function storeOutGoingGoodWarehouseStock(array $data)
    {
        $idWarehouse = $data['idWarehouse'];
        $idProduct = $data['idProduct'];
        $stock = $data['stock'];
        $stockOld = 0;

        $warehouseStock = self::checkProduct($data);

        if (!$warehouseStock) {
            $warehouseStock = self::create([
                'id_warehouse' => $idWarehouse,
                'id_product' => $idProduct,
            ]);
        }

        // Old Stock
        $stockOld = $warehouseStock->stock;
        // Total Stock
        $totalStock = $stockOld - $stock;

        $warehouseStock->update([
            'stock' => $totalStock
        ]);

        return $warehouseStock;
    }

    // Data Tables Yajra
    public static function dataTable()
    {
        $data = Warehouse::select(['warehouses.*']);

        return \Datatables::eloquent($data)
            ->addColumn('action', function ($data) {
                $action = '
					<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
							<a class="dropdown-item" href="' . route('warehouse_stock.detail', $data->id) . '">
								<i class="fas fa-search mr-1"></i> Detail
							</a>
						</div>
					</div>';
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}