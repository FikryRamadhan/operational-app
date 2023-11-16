<?php

namespace App\Http\Controllers;

use App\Models\ProductType;

use Exception;
use App\MyClass\Validations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\MyClass\Response;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return ProductType::dataTable($request);
        }

        return view('product_type.index', [
            'title'            => ' Jenis Produk ',
            'breadcrumbs'    => [
                [
                    'title'    => 'Jenis Produk',
                    'link'    => route('product_type')
                ]
            ]
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProduct_typeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validations::validateProductTypeGroup($request);
        DB::beginTransaction();

        try {
            ProductType::createProductType($request->all());
            DB::commit();

            return Response::save();
        } catch (\Exception $e) {
            DB::rollback();

            return Response::error($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product_type  $product_type
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        Validations::validateImport($request);
        try {
			ProductType::importExpenseProductTypeFromExcel($request);
			return Response::success();
		} catch (\Exception $e) {
			return Response::error($e);
		}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product_type  $product_type
     * @return \Illuminate\Http\Response
     */
    public function get(ProductType $product_type)
    {
        try {
            return Response::success([
                'productType' => $product_type
            ]);
        } catch (\Exception $e) {
            return Response::error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProduct_typeRequest  $request
     * @param  \App\Models\Product_type  $product_type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductType $product_type)
    {
        validations::updatevalidateProductType($request, $product_type->id);
        DB::beginTransaction();

        try {
            $product_type->updateproductType($request->all());
            DB::commit();

            return Response::update();
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product_type  $product_type
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductType $product_type)
    {
        DB::beginTransaction();

        try {
            $product_type->deleteProductTypeGroup();
            DB::commit();

            return Response::delete();
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::error($e);
        }
    }
}
