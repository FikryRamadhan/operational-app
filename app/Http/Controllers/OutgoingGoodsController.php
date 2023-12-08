<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OutgoingGood;
use App\Models\OutgoingGoodDetail;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\MyClass\Response;
use App\MyClass\Validations;
use Exception;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\Count;

class OutgoingGoodsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return OutgoingGood::dataTable();
        }
        return view('outgoing-good.index', [
            'title' => 'Barang Keluar',
            'breadcrumbs' => [
                [
                    'title' => 'Barang Keluar',
                    'link' => route('outgoing-goods')
                ]
            ]
        ]);
    }

    public function  create()
    {
        return view('outgoing-good.create', [
            'title' => 'Barang Keluar',
            'breadcrumbs' => [
                [
                    'title' => 'Barang Keluar',
                    'link' => route('outgoing-goods.create')
                ]
            ],
            'warehouse' => Warehouse::all(),
            'outgoingGoodDetails' => OutgoingGoodDetail::all()
        ]);
    }

    public function store(Request $request)
    {
        Validations::storeOutgoingGoods($request);
        DB::beginTransaction();

        try {
            $idWarehouse = $request->id_warehouse;
            $idProduct = $request->id_product;

            // Outgoing Good Store
            $outgoingGood = OutgoingGood::storeOutgoingGood([
                'id_warehouse' => $idWarehouse,
                'stock' => $request->stock,
                'id_user' => Auth()->user()->id,
                'date' => $request->date,
                'total_amount' => $request->total_amount,
                'description' => $request->description,
            ]);

            $idOutgoingGood = $outgoingGood->id;

            $noTransaksi = OutgoingGood::createFormatTransaksi($idOutgoingGood);
            $outgoingGood->update([
                'transaction_number' => $noTransaksi
            ]);

            // For Product,warehouseStock, and Product=Stock
            $idOutgoingGood = $outgoingGood->id;
            $amount = $request->amount;
            $filePhoto = $request->file_photo;
            // dd($filePhoto);
            // Warehouse Stock/OutgoinGood/IdProduct
            for ($i = 0; $i < count($idProduct); $i++) {
                $dataWarehouse = [
                    'idWarehouse' => $idWarehouse,
                    'idProduct' => $idProduct[$i],
                    'stock' => $amount[$i],
                ];
                $dataOutgoinGoodDetail = [
                    'idOutgoingGood' => $idOutgoingGood,
                    'idProduct' => $idProduct[$i],
                    'amount' => $amount[$i],
                ];
                
                
                WarehouseStock::storeOutGoingGoodWarehouseStock($dataWarehouse);
                $outgoingGoodDetails = OutgoingGoodDetail::storeOutgoingGoodDetail($dataOutgoinGoodDetail);
                if($request->file('file_photo')){
                    if(isset($filePhoto[$i])){
                        $outgoingGoodDetails->saveFile($filePhoto[$i]);
                    }
                }
                $outgoingGoodDetails->product->perhitunganUlangStockProduct();
            }


            DB::commit();

            return Response::save();
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
    }

    public function edit(OutgoingGood $outgoingGoods)
    {
        $outgoingGoodDetails = OutgoingGoodDetail::where('id_outgoing_good', $outgoingGoods->id)->get();
        return view('outgoing-good.edit', [
            'title' => 'Edit Barang Keluar',
            'outgoingGoods' => $outgoingGoods,
            'outgoingGoodDetails' => $outgoingGoodDetails,
            'breadcrumbs' => [
                [
                    'title' => 'Edit Barang Keluar',
                    'link' =>  route('outgoing-goods.update', $outgoingGoods)
                ]
            ],
            'warehouse' => Warehouse::all(),
        ]);
    }


    public function update(Request $request, OutgoingGood $outgoingGoods)
    {
        Validations::storeOutgoingGoods($request, $outgoingGoods->id);
        DB::beginTransaction();

        try {
            $idWarehouse = $outgoingGoods->id_warehouse;

            $outgoingGoods->updateOutgoinGood([
                'id_warehouse' => $idWarehouse,
                'date' => $request->date,
                'total_amount' => $request->total_amount,
                'description' => $request->description
            ]);

            DB::commit();
            return Response::update();
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
    }

    public function destroy(OutgoingGood $outgoingGoods)
    {
        DB::beginTransaction();

        try {
            $outgoingGoods->deleteOutgoinGood();
            DB::commit();

            return Response::delete();
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
    }
}