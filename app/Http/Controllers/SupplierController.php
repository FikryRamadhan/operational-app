<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Supplier;
use App\MyClass\Response;
use App\MyClass\Validations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            return Supplier::dataTable($request);
        }

        return view('supplier.index', [
            'title' => 'Supplier',
            'breadcrumbs' => [[
                'title'=> 'Supplier',
                'link' => route('supplier')
            ]]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSupplierRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validations::validateSupplierCreate($request);
        DB::beginTransaction();

        try{
            Supplier::createSupplier($request->all());
            DB::commit();

            return Response::save();
        } catch (Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function import(Supplier $supplier, Request $request)
    {
        Validations::validateImport($request);
        try {
			$supplier->importExpenseSupplierFromExcel($request);
			return Response::success();
		} catch (\Exception $e) {
			return Response::error($e);
		}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function get(Supplier $supplier)
    {
        try {
			return Response::success([
				'supplier' => $supplier
			]);
		} catch (Exception $e) {
			return Response::error($e);
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSupplierRequest  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        Validations::validateSupplierUpdate($request, $supplier->id);
        DB::beginTransaction();

        try {
            $supplier->updateSupplier($request->all());
            DB::commit();

            return  Response::update();
        } catch(Exception $e){
            DB::rollBack();

            return Response::error($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        DB::beginTransaction();

        try{
            $supplier->deleteSupplier();
            DB::commit();

            return Response::delete();
        } catch(Exception $e){
            DB::rollBack();

            return Response::error($e);
        }
    }
}
