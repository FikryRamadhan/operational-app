<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\Product;
use App\MyClass\Response;
use App\MyClass\Validations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function Index(Request $request)
    {
        if($request->ajax()){
            return Warehouse::dataTable($request);
        }
        return view('warehouse.index',[
            'title' => 'Gudang',
            'breadcrumbs' => [[
                'title' => 'Gudang',
                'link' => route('warehouse')
            ]]
        ]);
    }
    public function Store(Request $request)
    {
        Validations::validateWarehouseStore($request);
        DB::beginTransaction();

        try
        {
            Warehouse::createWarehouse($request->all());
            DB::commit();

            return Response::save();
        } catch (\Exception $e)
        {
            DB::rollBack();

            return Response::error($e);
        }
    }
    public function Import(Warehouse $warehouse, Request $request)
    {
        Validations::validateImport($request);
        try{
            $warehouse->importWarehouseFromExcel($request);
            return Response::success();
        } catch(\Exception $e) {
            return Response::error($e);
        }
    }
    public function Get(Warehouse $warehouse)
    {
        try {
            return Response::success([
                'warehouse' => $warehouse
            ]);
        } catch(\Exception $e){
            return Response::error($e);
        }
    }
    public function Update(Request $request, Warehouse $warehouse)
    {
        Validations::updateValidateWarehouse($request);
        DB::beginTransaction();

        try {
            $warehouse->updateWarehouse($request->all());

            DB::commit();
            return Response::update();
        } catch (\Exception $e){
            DB::rollBack();
            return Response::error($e);
        }
    }
    public function destroy(Warehouse $warehouse)
    {
        DB::beginTransaction();

        try {
            $warehouse->deleteWarehouse();
            DB::commit();

            return Response::delete();
        }catch(\Exception $e){
            DB::rollBack();

            return Response::error($e);
        }
    }

    // public function detail(Warehouse $warehouse){
    //     $idWarehouse = $warehouse->id;
    //     $productById = Warehouse::getProductById($idWarehouse);
    //     return view('warehouse.detail', [
	// 		'title'			=> 'Detail Stok Gudang',
	// 		'warehouse'		=> $warehouse,
    //         'product'       => $productById,
	// 		'breadcrumbs'	=> [
	// 			[
	// 				'title'	=> 'Detail Stok Gudang',
	// 				'link'	=> route('warehouse')
	// 			],
	// 			[
	// 				'title'	=> 'Detail Stok Gudang',
	// 				'link'	=> route('warehouse.detail', $warehouse->id)
    //             ]
	// 		]
	// 	]);
    // }


}
