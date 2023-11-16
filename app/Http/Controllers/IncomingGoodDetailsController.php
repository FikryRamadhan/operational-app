<?php

namespace App\Http\Controllers;

use Exception;
use App\MyClass\Response;
use App\Models\IncomingGood;
use Illuminate\Http\Request;
use App\Models\IncomingGoodDetail;
use Illuminate\Support\Facades\DB;

class incomingGoodDetailsController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            return IncomingGoodDetail::dataTable();
        }
        return view('item_incoming_good_detail.index', [
            'title' => 'Item Barang Masuk',
            'breadcrumbs'    => [
                [
                    'title'    => 'Item Barang Masuk',
                    'link'    => route('item-incoming-good-details')
                ]
            ]
        ]);
    }


    public function detail(IncomingGood $incomingGoods)
    {
        $idIncominGood = $incomingGoods->id;
        $incomingGoodDetails = IncomingGood::getIncomingGoodDetailsById($idIncominGood);
        return view('incoming_good_detail.index', [
            'title' => 'Detail Barang Masuk',
            'breadcrumbs'	=> [
				[
					'title'	=> 'Detail Barang Masuk',
					'link'	=> route('incoming-goods.detail', $incomingGoods)
				]
                ],
                'incomingGood' => $incomingGoods,
                'incomingGoodDetails' => $incomingGoodDetails
        ]);
    }

    public function destroy(IncomingGoodDetail $incomingGoodDetail){
        DB::beginTransaction();

        try{
            $incomingGoodDetail->deleteIncomingGoodDetail();
            DB::commit();

            return Response::delete();
        } catch (Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }

    }
}