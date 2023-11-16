<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustmentDetails;
use App\MyClass\Response;
use App\Models\StockAdjustments;
use Illuminate\Support\Facades\DB;

class StockAdjustmentDetailsController extends Controller
{
    public function detail(StockAdjustments $stockAdjustment)
    {
        $idStockAdjustments = $stockAdjustment->id;
        $stockAdjustmentDetail = StockAdjustments::getStockAdjustmentDetailById($idStockAdjustments);

        return view('stock_adjustment_detail.index', [
            'title' => 'Detail Penyesuain Stok',
            'breadcrumbs'	=> [
				[
					'title'	=> 'Detail Penyesuaian Stok',
					'link'	=> route('stock-adjustment.detail', $stockAdjustment)
				]
                ],
                'stockAdjustment' => $stockAdjustment,
                'stockAdjustmentDetails' => $stockAdjustmentDetail
        ]);
    }

    public function destroy(StockAdjustmentDetails $stockAdjustmentDetail){
        DB::beginTransaction();

        try
        {
            $stockAdjustmentDetail->deleteStockAdjustment();
            DB::commit();
            
            return Response::delete();
        }catch(\Exception $e)
        {
            DB::rollback();

            return Response::error($e);
        }
    }
}
