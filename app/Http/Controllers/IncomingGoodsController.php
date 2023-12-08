<?php

namespace App\Http\Controllers;

use App\Models\IncomingGood;
use App\Models\incomingGoodDetail;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\MyClass\Response;
use App\MyClass\Validations;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomingGoodsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return IncomingGood::dataTable();
        }
        return view('incoming-good.index', [
            'title' => 'Barang Masuk',
            'breadcrumbs'    => [
                [
                    'title'    => 'Barang Masuk',
                    'link'    => route('incoming-goods')
                ]
            ]
        ]);
    }

    public function create()
    {
        return view('incoming-good.create', [
            'title' => 'Tambah Barang Masuk',
            'breadcrumbs'    => [
                [
                    'title'    => 'Tambah Barang Masuk',
                    'link'    => route('incoming-goods.create')
                ]
            ],
            'supplier' => Supplier::all(),
            'warehouse' => Warehouse::all()
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        Validations::storeIncomingGoods($request);
        DB::beginTransaction();

        try {
            $idWarehouse = $request->id_warehouse;
            $idProduct = $request->id_product;


            // Incoming Good Store
            $incomingGood = IncomingGood::storeIncomingGoods([
                'id_warehouse' => $idWarehouse,
                'stock' => $request->stock,
                'id_user' => Auth()->user()->id,
                'id_supplier' => $request->id_supplier,
                'date' => $request->date,
                'total_amount' => $request->total_amount,
                'description' => $request->description,
            ]);

            $idIncomingGood = $incomingGood->id;

            $noTransaksi = IncomingGood::createFormatTransaksi($idIncomingGood);

            $incomingGood->update([
                'transaction_number' => $noTransaksi
            ]);

            // For Product,warehouseStock, and Product=Stock
            $idIncomingGood = $incomingGood->id;
            $amount = $request->amount;
            $filePhoto = $request->file_photo;

            // Warehouse Stock/IncomingGoods/IdProduct
            for ($i = 0; $i < count($idProduct); $i++) {
                $dataWarehouse = [
                    'idWarehouse' => $idWarehouse,
                    'idProduct' => $idProduct[$i],
                    'stock' => $amount[$i]
                ];
                $dataIncomingGoodDetail = [
                    'idIncomingGood' => $idIncomingGood,
                    'idProduct' => $idProduct[$i],
                    'amount' => $amount[$i],
                ];
                
                WarehouseStock::storeWarehouseStock($dataWarehouse);
                $incomingGoodDetail = IncomingGoodDetail::storeIncomingGoodDetail($dataIncomingGoodDetail);
                if($request->file('file_photo')) {
                    if(isset($filePhoto[$i])){
                        $incomingGoodDetail->saveFile($filePhoto[$i]);
                    }
                }
                $incomingGoodDetail->product->perhitunganUlangStockProduct();
            }
            DB::commit();

            return Response::save();
        } catch (\Exception $e) {
            DB::rollback();

            return Response::error($e);
        }
    }

    public function edit(incomingGood $incomingGoods)
    {
        $incomingGoodDetail = incomingGoodDetail::where('id_incoming_good', $incomingGoods->id)->get();
        // dd($incomingGoodDetail);
        return view('incoming-good.edit', [
            'title' => 'Edit Barang Masuk',
            'incomingGood' => $incomingGoods,
            'incomingGoodDetails' =>  $incomingGoodDetail,
            'breadcrumbs'    => [
                [
                    'title'    => 'Edit Barang Masuk',
                    'link'    => route('incoming-goods.update', $incomingGoods)
                ]
            ],
            'supplier' => Supplier::all(),
            'warehouse' => Warehouse::all(),
        ]);
    }

    public function update(Request $request, IncomingGood $incomingGoods)
    {
        // dd($request);
        Validations::storeIncomingGoods($request, $incomingGoods->id);
        DB::beginTransaction();
        
        try{
            $idWarehouse = $request->id_warehouse;
            
            $incomingGoods->updateIncomingGoods([
                'id_warehouse' => $idWarehouse,
                'id_supplier' => $request->id_supplier,
                'date' => $request->date,
                'total_amount' => $request->total_amount,
                'description' => $request->description,
            ]);

            DB::commit();

            return Response::update();
        } catch (Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }

    }

    public function destroy(IncomingGood $incomingGoods)
    {
        DB::beginTransaction();

        try {
            $incomingGoods->deleteIncomingGoods();
            DB::commit();

            return Response::delete();
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
    }
}