<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustmentDetails extends Model
{
    protected $guarded=[''];

    public function stockAdjustment() {
        return $this->belongsTo(StockAdjustments::class, 'id_stock_adjustment');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    // Method Action
    public static function storeStockAdjustmentDetail(array $data)
    {
        $idOutgoingGood = $data['idStockAdjustment'];
        $idProduct = $data['idProduct'];
        $amount = $data['amount'];

        return self::create([
            'id_stock_adjustment' => $idOutgoingGood,
            'id_product' => $idProduct,
            'amount' => $amount,
        ]);
    }

    public function deleteStockAdjustment()
    {
        $this->removeStockProduct();
        $this->removeStockWarehouse();
        $this->delete();
        return $this;
    }

    // product
    public function removeStockProduct()
    {
        if($this){
            $amount = $this->amount;

            if($this->product){
                $stockProduct = $this->product->stock;
                $amountProduct = $amount;
                $totalStock = $stockProduct - $amountProduct; 
                $this->product->update([
                    'stock' => $totalStock
                ]);
            }
        }
    }

    public function removeStockWarehouse()
    {
        if($this){
            $amount = $this->amount;
            foreach($this->stockAdjustment->warehouse->warehouseStock as $key => $warehouseStock){
                $idProduct = $warehouseStock->id_product;

                if($this->id_product == $idProduct){
                    $stockProduct = $warehouseStock->stock;
                    $amountProduct = $amount;
                    $totalStock = $stockProduct - $amountProduct;
                    $warehouseStock->update([
                        'stock' => $totalStock
                    ]);
                }
            }
        }
    }
}
