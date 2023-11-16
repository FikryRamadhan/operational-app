<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class WarehouseStockController extends Controller
{
    public function Index(Request $request)
    {
        if($request->ajax()){
            return WarehouseStock::dataTable();
        }
        return view('warehouse_stock.index', [
            'title' => 'Stok Gudang',
            'breadcrumbs' => [[
                'title' => 'Stok Gudang',
                'link' => route('warehouse_stock')
            ]]
        ]);
    }

    public function detail(Warehouse $warehouse){
        $idWarehouse = $warehouse->id;
        $productById = Warehouse::getProductById($idWarehouse);
        return view('warehouse.detail', [
			'title'			=> 'Detail Stok Gudang',
			'warehouse'		=> $warehouse,
            'product'       => $productById,
			'breadcrumbs'	=> [
				[
					'title'	=> 'Detail Stok Gudang',
					'link'	=> route('warehouse_stock.detail', $warehouse->id)
                ]
			]
		]);
    }

}
