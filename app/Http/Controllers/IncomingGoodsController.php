<?php

namespace App\Http\Controllers;

use App\Models\IncomingGood;
use App\Models\incomingGoodDetail;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\MyClass\Response;
use App\MyClass\Validations;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\Count;

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
                $dataStockProduct = [
                    'idProduct' => $idProduct[$i],
                    'stock' => $amount[$i],
                ];

                // Product::storeStockProduct($dataStockProduct);
                WarehouseStock::storeWarehouseStock($dataWarehouse);
                $incomingGoodDetail = incomingGoodDetail::storeIncomingGoodDetail($dataIncomingGoodDetail);
                $incomingGoodDetail->product->perhitunganUlangStockProduct();
                // $product = Product::where('id', $idProduct[$i])->first();
                // $product->perhitunganUlangStockProduct();

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
        Validations::storeIncomingGoods($request, $incomingGoods->id);
        DB::beginTransaction();
        
        try{
            $idWarehouse = $request->id_warehouse;
            $amount = $request->amount;
            
            $incomingGoods->updateIncomingGoods([
                'id_warehouse' => $idWarehouse,
                'id_supplier' => $request->id_supplier,
                'date' => $request->date,
                'total_amount' => $request->total_amount,
                'description' => $request->description,
            ]);

            foreach ($incomingGoods->incomingGoodDetail as $key => $incomingGoodDetails) {
                $warehouseStock = WarehouseStock::where('id_warehouse', $idWarehouse)->where('id_product', $incomingGoodDetails->id_product)->first();
                $warehouseStock->update([
                    'stock' => $warehouseStock->stock - $incomingGoodDetails->amount
                ]);

                $incomingGoodDetails->product->perhitunganUlangStockProduct();
            }

            incomingGoodDetail::where('id_incoming_good', $incomingGoods->id)->delete();

            // For Product Dan Incoming Goods
            $idProduct = $request->id_product;
            $idIncomingGood = $incomingGoods->id;

            for($i = 0; $i < count($idProduct); $i++ ){
                $warehouseStock = WarehouseStock::where('id_warehouse', $idWarehouse)->where('id_product', $idProduct[$i])->first();
                if($warehouseStock){
                    $warehouseStock->update([
                        'stock' => $warehouseStock->stock + $amount[$i]
                    ]);
                }else{
                    WarehouseStock::storeWarehouseStock([
                        'idWarehouse' => $idWarehouse,
                        'idProduct' => $idProduct[$i],
                        'stock' => $amount[$i],
                    ]);
                }

                $dataIncomingGoodDetail = [
                    'idIncomingGood' => $idIncomingGood,
                    'idProduct' => $idProduct[$i],
                    'amount' => $amount[$i],
                ];

                $incomingGoodDetail = incomingGoodDetail::storeIncomingGoodDetail($dataIncomingGoodDetail);
                $incomingGoodDetail->product->perhitunganUlangStockProduct();
            }

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