<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IncomingGoodDetail extends Model
{
    use HasFactory;
    protected $guarded = [''];

    // Relationship
    public function incomingGoods(){
        return $this->belongsTo(IncomingGood::class, 'id_incoming_good');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'id_product');
    }

    // Method Action
    public static function storeIncomingGoodDetail(array $data){
        $idIncomingGood = $data['idIncomingGood'];
        $idProduct = $data['idProduct'];
        $amount = $data['amount'];

        return self::create([
            'id_incoming_good' => $idIncomingGood,
            'id_product' => $idProduct,
            'amount' => $amount,
        ]);
    }

    public function deleteIncomingGoodDetail(){
        $this->removeStockProduct();
        $this->removeStockWarehouse();
        $this->delete();
        return $this;
    }

    public function removeStockProduct(){
        if($this) {
                $amount = $this->amount;

                if($this->product) {
                    $stockProduct = $this->product->stock;
                    $amountProduct = $amount;
                    $totalStock = $stockProduct - $amountProduct;
                    $this->product->update([
                        'stock' => $totalStock
                    ]);
                }
        }
    }

    public function removeStockWarehouse(){
        if($this){
                $amount = $this->amount;
                
                foreach ($this->incomingGoods->warehouse->warehouseStock as $key => $warehouseStock) {
                    $idProduct = $warehouseStock->id_product;
                    
                    if($this->id_product == $idProduct) {
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

    // Data Table 
    public static function dataTable(){
        $data = self::select([ 'incoming_good_details.*', 'products.*' , 'product_types.*' ])
        ->with('product', 'incomingGoods', 'product.productType')
        ->leftJoin('products', 'incoming_good_details.id_product', '=', 'products.id')
        ->leftJoin('incoming_goods', 'incoming_good_details.id_incoming_good', '=', 'incoming_goods.id')
        ->leftJoin('product_types', 'products.id_product_type', '=', 'product_types.id');


        return DataTables::eloquent($data)
        
        ->editColumn('incomingGoods.date', function($data){
            return  $data->incomingGoods->date->format('d F y');
        })
        ->editColumn('incomingGoods.transaction_number', function($data){
            $detail =   '<a class="text-decoration-none" href="'.route('incoming-goods.detail', $data->id_incoming_good).'">
                            '.$data->incomingGoods->transaction_number.
                        '</a>';
            return $detail;
        })
        ->editColumn('product.product_name', function($data){
            return $data->product->product_name;
        })
        ->editColumn('product.productType.product_type_name', function($data){
            return $data->product->productType->product_type_name;
        })
        ->rawColumns(['incomingGoods.transaction_number'])
        ->make(true);
    }
}