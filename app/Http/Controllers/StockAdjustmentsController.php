<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\MyClass\Response;
use App\MyClass\Validations;
use Illuminate\Http\Request;
use App\Models\WarehouseStock;
use App\Models\StockAdjustments;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\StockAdjustmentDetails;

class StockAdjustmentsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return StockAdjustments::dataTable();
        }
        return view('stock_adjustments.index', [
            'title' => 'Penyesuaian Stok Produk',
            'breadcrumbs'    => [
                [
                    'title'    => 'Penyesuaian Stok Produk',
                    'link'    => route('stock-adjustment')
                ]
            ]
        ]);
    }

    public function create()
    {
        return view('stock_adjustments.create', [
            'title' => 'Tambah Barang Masuk',
            'breadcrumbs'    => [
                [
                    'title'    => 'Tambah Barang Masuk',
                    'link'    => route('incoming-goods.create')
                ]
            ],
            'warehouse' => Warehouse::all()
        ]);
    }

    public function store(Request $request)
    {
        Validations::validationStockAdjustment($request);
        DB::beginTransaction();

        try {
            $idWarehouse = $request->id_warehouse;
            $idProduct = $request->id_product;


            // Incoming Good Store
            $stockAdjustment = StockAdjustments::storeStockAdjustment([
                'id_warehouse' => $idWarehouse,
                'stock' => $request->stock,
                'id_user' => Auth()->user()->id,
                'date' => $request->date,
                'total_amount' => $request->total_amount,
                'description' => $request->description,
            ]);

            $idStockAdjustment = $stockAdjustment->id;

            $noTransaksi = StockAdjustments::createFormatTransaksi($idStockAdjustment);

            $stockAdjustment->update([
                'transaction_number' => $noTransaksi
            ]);

            // For Product,warehouseStock, and Product=Stock
            $idStockAdjustment = $stockAdjustment->id;
            $amount = $request->amount;


            // Warehouse Stock/IncomingGoods/IdProduct
            for ($i = 0; $i < count($idProduct); $i++) {
                $dataWarehouse = [
                    'idWarehouse' => $idWarehouse,
                    'idProduct' => $idProduct[$i],
                    'stock' => $amount[$i]
                ];
                $dataIncomingGoodDetail = [
                    'idStockAdjustment' => $idStockAdjustment,
                    'idProduct' => $idProduct[$i],
                    'amount' => $amount[$i],
                ];
                $dataStockProduct = [
                    'idProduct' => $idProduct[$i],
                    'stock' => $amount[$i],
                ];

                // Product::storeStockProduct($dataStockProduct);
                WarehouseStock::storeWarehouseStock($dataWarehouse);
                $incomingGoodDetail = StockAdjustmentDetails::storeStockAdjustmentDetail($dataIncomingGoodDetail);
                $incomingGoodDetail->product->perhitunganUlangStockProduct();

            }
            DB::commit();

            return Response::save();
        } catch (\Exception $e) {
            DB::rollback();

            return Response::error($e);
        }
    }

    public function edit(StockAdjustments $stockAdjustment)
    {
        $stockAdjustmentDetail = StockAdjustmentDetails::where('id_stock_adjustment', $stockAdjustment->id)->get();
        return view('stock_adjustments.edit', [
            'title' => 'Edit Penyesuain Stok',
            'stockAdjustment' => $stockAdjustment,
            'stockAdjustmentDetail' =>  $stockAdjustmentDetail,
            'breadcrumbs'    => [
                [
                    'title'    => 'Edit Penyesuain Stok',
                    'link'    => route('stock-adjustment.edit', $stockAdjustment)
                ]
            ],
            'warehouse' => Warehouse::all(),
            
        ]);
    }

    public function update(Request $request, StockAdjustments $stockAdjustment)
    {
        Validations::validationStockAdjustment($request, $stockAdjustment->id);
        DB::beginTransaction();

        try{
            $idWarehouse = $request->id_warehouse;
            $amount = $request->amount;

            $stockAdjustment->updateStockAdjustment([
                'id_warehouse' => $idWarehouse,
                'date' => $request->date,
                'total_amount' => $request->total_amount,
                'description' => $request->description,
            ]);

            foreach ($stockAdjustment->stockAdjustmentDetail as $key => $stockAdjustmentDetails) {
                $warehouseStock = WarehouseStock::where('id_warehouse', $idWarehouse)->where('id_product', $stockAdjustmentDetails->id_product)->first();
                $warehouseStock->update([
                    'stock' => $warehouseStock->stock - $stockAdjustmentDetails->amount
                ]);

                $stockAdjustmentDetails->product->perhitunganUlangStockProduct();
            }

            StockAdjustmentDetails::where('id_stock_adjustment', $stockAdjustment->id)->delete();

            // For Product Dan Incoming Goods
            $idProduct = $request->id_product;
            $idStockAdjustment = $stockAdjustment->id;

            for($i = 0; $i < count($idProduct); $i++ ){
                $warehouseStock = WarehouseStock::where('id_warehouse', $idWarehouse)->where('id_product', $idProduct[$i])->first();
                if($warehouseStock){
                    $warehouseStock->update([
                        'stock' => $warehouseStock->stock + $amount[$i]
                    ]);
                } else {
                    $dataWarehouse = [
                        'idWarehouse' => $idWarehouse,
                        'idProduct' => $idProduct[$i],
                        'stock' => $amount[$i]
                    ];
                    WarehouseStock::storeWarehouseStock($dataWarehouse);
                }

                $dataStockAdjustmentDetail = [
                    'idStockAdjustment' => $idStockAdjustment,
                    'idProduct' => $idProduct[$i],
                    'amount' => $amount[$i],
                ];

                $stockAdjustmentDetail = StockAdjustmentDetails::storeStockAdjustmentDetail($dataStockAdjustmentDetail);
                $stockAdjustmentDetail->product->perhitunganUlangStockProduct();
            }

            DB::commit();

            return Response::update();
        } catch (Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }

    }

    public function destroy(StockAdjustments $stockAdjustment)
    {
        DB::beginTransaction();

        try {
            $stockAdjustment->deleteStockAdjustment();
            DB::commit();

            return Response::delete();
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
    }
}
