<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use App\Models\OutgoingGood;
use App\MyClass\Response;
use App\Models\OutgoingGoodDetail;
use Illuminate\Http\Request;
use App\MyClass\Validations;
use App\Models\WarehouseStock;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OutgoingGoodsDetailController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()){
            return OutgoingGoodDetail::dataTable();
        }
        return view('item_outgoing_good_detail.index', [
            'title' => 'Item Barang Keluar',
            'breadcrumbs' => [
                [
                    'title' => 'Item Barang Keluar',
                    'link' => route('item-outgoing-good-detail')
                ]
            ]
        ]);

    }

    public function edit(OutgoingGoodDetail $outgoingGoodDetail) {
        try {
            return view('outgoing-good-detail.edit', [
                'title' => 'Edit Item Barang Keluar',
                'breadcrumbs'    => [
                    [
                        'title'    => 'Edit Item Barang Keluar',
                        'link'    => route('outgoing-good-detail.edit', $outgoingGoodDetail->id)
                    ]
                    ],
                'product' => Product::all(),
                'outgoingGoodDetail' => $outgoingGoodDetail
            ]);
        } catch (Exception $e) {
            return Response::error($e);
        }
    }
    
    public function detail(OutgoingGood $outgoingGoods)
    {
        $idOutgoingGood = $outgoingGoods->id;
        $outgoingGoodDetails = OutgoingGood::getOutgoingGoodDetailsById($idOutgoingGood);

        return view('outgoing-good-detail.index', [
            'title' => 'Detail Barang Keluar',
            'breadcrumbs' => [
                [
                    'title' => 'Detail Barang Keluar',
                    'link' => route('outgoing-goods.detail', $idOutgoingGood)
                ]
                ],
                'outgoingGood' => $outgoingGoods,
                'outgoingGoodDetails' => $outgoingGoodDetails
        ]);
    }

    public function update(Request $request, OutgoingGoodDetail $outgoingGoodDetail)
    {
        Validations::updateOutgoingGoodDetail($request);
        DB::beginTransaction();

        try{
            $outGoingGood = $outgoingGoodDetail->outgoingGood;
            $warehouseStock = WarehouseStock::where('id_warehouse', $outGoingGood->id_warehouse)->where('id_product', $outgoingGoodDetail->id_product)->first();
            $warehouseStock->update([
                'stock' => $warehouseStock->stock + $outgoingGoodDetail->amount
            ]);

            $outGoingGood->update([
                'total_amount' => $outGoingGood->total_amount - $outgoingGoodDetail->amount,
            ]);
            $totalAmount = $outGoingGood->total_amount + $request->amount;
            $outGoingGood->update([
                'total_amount' => $totalAmount,
            ]);

            // For OutgoingGood
            $idProduct = $request->id_product;
            $idWarehouse = $outGoingGood->id_warehouse;
            $amount = $request->amount;

            $warehouseStock = WarehouseStock::where('id_warehouse', $idWarehouse)->where('id_product', $idProduct)->first();
            if($warehouseStock){
                $warehouseStock->update([
                    'stock' => $warehouseStock->stock - $amount,
                ]);
            } else {
                WarehouseStock::storeWarehouseStock([
                    'idWarehouse' => $idWarehouse,
                    'idProduct' => $idProduct,
                    'stock' => $amount,
                ]);
            }

            $dataOutgoingGoodDetail = [
                'id_product' => $request->id_product,
                'id_outgoing_good' => $outGoingGood->id,
                'amount' => $request->amount
            ];
            
            $filePhoto = $request->file_photo;

            $outgoingGoodDetail->updateOutgoingGoodDetail($dataOutgoingGoodDetail);
            if($request->file('file_photo')){
                if(isset($filePhoto)){
                    $outgoingGoodDetail->saveFile($filePhoto);
                }
            }

            $outgoingGoodDetail->product->perhitunganUlangStockProduct();

            DB::commit();
            return Response::success();
        } catch (Exception $e){
            DB::rollBack();
            return Response::error($e);
        }
        
    }
       

    public function destroy(OutgoingGoodDetail $outgoingGoodDetail)
    {
        DB::beginTransaction();

        try
        {
            $outgoingGoodDetail->removePhoto();
            $outGoingGood = $outgoingGoodDetail->outgoingGood;
            $amountAwal = $outGoingGood->total_amount;
            $amountAkhir = $amountAwal - $outgoingGoodDetail->amount;
            $outGoingGood->update([
                'total_amount' => $amountAkhir
            ]);
            $outgoingGoodDetail->deleteOutgoinGoodDetail();
            DB::commit();
            
            return Response::delete();
        }catch(Exception $e)
        {
            DB::rollback();
            return Response::error($e);
        }
    }
}