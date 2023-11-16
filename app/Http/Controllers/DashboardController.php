<?php

namespace App\Http\Controllers;

use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
	public function index()
	{
		$warehouseStock = WarehouseStock::getMinimalStock();
		return view('dashboard.index', [
			'title'			=> 'Dashboard',
			'warehouseStock' => $warehouseStock
		]);
	}

	public function templateImport($filename)
	{
		try {
			$path = storage_path('import_templates/'.$filename);
			return response()->download($path);
		} catch (\Exception $e) {
			return \Res::error($e);
		}
	}
}
