<?php

namespace App\Http\Controllers;

use Exception;
use App\MyClass\Response;
use App\Models\IncomingGood;
use App\MyClass\Validations;
use Illuminate\Http\Request;
use App\Models\WarehouseStock;
use App\Models\IncomingGoodDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class incomingGoodDetailsController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            return IncomingGoodDetail::dataTable();
        }
        return view('item_incoming_good_detail.index', [
            'title' => 'Item Barang Masuk',
            'breadcrumbs'    => [
                [
                    'title'    => 'Item Barang Masuk',
                    'link'    => route('item-incoming-good-details')
                ]
            ]
        ]);
    }

    public function edit(IncomingGoodDetail $incomingGoodDetail) {
        try {
            return view('incoming_good_detail.edit', [
                'title' => 'Edit Item Barang Masuk',
                'breadcrumbs'    => [
                    [
                        'title'    => 'Edit Item Barang Masuk',
                        'link'    => route('incoming-good-detail.edit', $incomingGoodDetail->id)
                    ]
                    ],
                'product' => Product::all(),
                'incomingGoodDetail' => $incomingGoodDetail
            ]);
        } catch (Exception $e) {
            return Response::error($e);
        }
    }

    public function update(Request $request, IncomingGoodDetail $incomingGoodDetail) {
        Validations::updateIncomningDetail($request);
        DB::beginTransaction();

        try {
            $incomingGood = $incomingGoodDetail->incomingGoods;
            $warehouseStock = WarehouseStock::where('id_warehouse', $incomingGood->id_warehouse)->where('id_product', $incomingGoodDetail->id_product)->first();
            $warehouseStock->update([
                'stock' => $warehouseStock->stock - $incomingGoodDetail->amount
            ]);

            // For Total Amount In IncomingGood
            $incomingGood->update([
                'total_amount' => $incomingGood->total_amount - $incomingGoodDetail->amount,
            ]);
            $totalAmount = $incomingGood->total_amount + $request->amount;
            $incomingGood->update([
                'total_amount' => $totalAmount,
            ]);
            
            // For Product Dan Incoming Goods
            $idProduct = $request->id_product;
            $idWarehouse = $incomingGood->id_warehouse;
            $amount= $request->amount;

                $warehouseStock = WarehouseStock::where('id_warehouse', $idWarehouse)->where('id_product', $idProduct)->first();
                if($warehouseStock){
                    $warehouseStock->update([
                        'stock' => $warehouseStock->stock + $amount
                    ]);
                }else{
                    WarehouseStock::storeWarehouseStock([
                        'idWarehouse' => $idWarehouse,
                        'idProduct' => $idProduct,
                        'stock' => $amount,
                    ]);
                }

                $dataIncomingGoodDetail = [
                    'id_product' => $request->id_product,
                    'id_incoming_good' => $incomingGood->id,
                    'amount' => $request->amount,
                ];
                $filePhoto = $request->file_photo;

                $incomingGoodDetail->updateIncomingGoodDetail($dataIncomingGoodDetail);
                if($request->file('file_photo')){
                    if(isset($filePhoto)){    
                        $incomingGoodDetail->saveFile($filePhoto);
                    }    
                }
                $incomingGoodDetail->product->perhitunganUlangStockProduct();
                
                DB::commit();
                return  Response::success();
        }catch(Exception $e) {
            DB::rollBack();
            return  Response::error($e);
        }
    }
    
    public function destroy(IncomingGoodDetail $incomingGoodDetail){
        DB::beginTransaction();

        try{
            $incomingGoodDetail->removePhoto();
            $incomingGood = $incomingGoodDetail->incomingGoods;
            $amountAwal = $incomingGood->total_amount;
            $amountAkhir = $amountAwal - $incomingGoodDetail->amount;
            $incomingGood->update([
                'total_amount' => $amountAkhir,
            ]);
            $incomingGoodDetail->deleteIncomingGoodDetail();
            
            DB::commit();

            return Response::delete();
        } catch (Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
        
    }

    public function detail(IncomingGood $incomingGoods)
    {
        $idIncominGood = $incomingGoods->id;
        $incomingGoodDetails = IncomingGood::getIncomingGoodDetailsById($idIncominGood);
        return view('incoming_good_detail.index', [
            'title' => 'Detail Barang Masuk',
            'breadcrumbs'	=> [
                [
                    'title'	=> 'Detail Barang Masuk',
                    'link'	=> route('incoming-goods.detail', $incomingGoods)
                ]
                ],
                'incomingGood' => $incomingGoods,
                'incomingGoodDetails' => $incomingGoodDetails
        ]);
    }
}