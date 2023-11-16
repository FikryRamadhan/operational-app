<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingGoodDetail extends Model
{
    use HasFactory;
    protected $guarded = [''];

    // Relationship
    public function outgoingGood()
    {
        return $this->belongsTo(OutgoingGood::class, 'id_outgoing_good');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    // Method Action
    public static function storeOutgoingGoodDetail(array $data)
    {
        $idOutgoingGood = $data['idOutgoingGood'];
        $idProduct = $data['idProduct'];
        $amount = $data['amount'];

        return self::create([
            'id_outgoing_good' => $idOutgoingGood,
            'id_product' => $idProduct,
            'amount' => $amount,
        ]);
    }

    public function deleteOutgoinGoodDetail()
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
                $totalStock = $stockProduct + $amountProduct; 
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
            foreach($this->outgoingGood->warehouse->warehouseStock as $key => $warehouseStock){
                $idProduct = $warehouseStock->id_product;

                if($this->id_product == $idProduct){
                    $stockProduct = $warehouseStock->stock;
                    $amountProduct = $amount;
                    $totalStock = $stockProduct + $amountProduct;
                    $warehouseStock->update([
                        'stock' => $totalStock
                    ]);
                }
            }
        }
    }


    public static function dataTable()
    {
        $data = self::select(['outgoing_good_details.*'])
        ->with('product', 'outgoingGood', 'product.productType')
        ->leftJoin('products', 'outgoing_good_details.id_product', '=', 'products.id')
        ->leftJoin('outgoing_goods', 'outgoing_good_details.id_outgoing_good', '=', 'outgoing_goods.id');

        return \Datatables::eloquent($data)
        ->editColumn('outgoingGood.date', function($data){
            return $data->outgoingGood->date->format('d F y');
        })
        ->editColumn('outgoingGood.transaction_number', function($data){
            $detail = '<a class="text-decoration-none" href="' . route('outgoing-goods.detail', $data->id_outgoing_good) . '">
                        '.$data->outgoingGood->transaction_number.
                     '</a>';
            return $detail;
        })
        ->editColumn('product.product_name', function($data){
            return $data->product->product_name;
        })
        ->editColumn('product.productType.product_type_name', function($data){
            return $data->product->productType->product_type_name;
        })
        ->rawColumns(['outgoingGood.transaction_number'])
        ->make(true);
          
    }
}
