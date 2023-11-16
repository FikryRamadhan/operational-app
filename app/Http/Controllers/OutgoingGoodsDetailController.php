<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OutgoingGood;
use App\MyClass\Response;
use App\Models\OutgoingGoodDetail;
use Illuminate\Http\Request;
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
    
    public function detail(OutgoingGood $outgoingGoods)
    {
        $idOutgoingGood = $outgoingGoods->id;
        $outgoingGoodDetails = OutgoingGood::getOutgoingGoodDetailsById($idOutgoingGood);

        return view('outgoing-good-detail.index', [
            'title' => 'Detail Barang Keluar',
            'breadcrumbs' => [
                [
                    'title' => 'Detaol Barang Keluar',
                    'link' => route('outgoing-goods.detail', $idOutgoingGood)
                ]
                ],
                'outgoingGood' => $outgoingGoods,
                'outgoingGoodDetails' => $outgoingGoodDetails
        ]);
    }

    public function destroy(OutgoingGoodDetail $outgoingGoodDetail)
    {
        DB::beginTransaction();

        try
        {
            $outgoingGoodDetail->deleteOutgoinGoodDetail();
            DB::commit();
            
            return Response::delete();
        }catch(\Exception $e)
        {
            DB::rollback();

            return Response::error($e);
        }
    }
}
