<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Mockery\Expectation;
use App\MyClass\Response;
use App\Models\ProductType;
use App\MyClass\Validations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Exceptions\Exception;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            return  Product::dataTable($request);
        };

        return view('product.index', [
            'title' => 'Produk',
            'breadcrumbs'	=> [
				[
					'title'	=> 'Produk',
					'link'	=> route('product'),
				]
                ],
            'brand' => Brand::all(),
            'productType' => ProductType::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validations::validateProductStore($request);
        DB::beginTransaction();

        try{
            $product = Product::productCreate($request->all());
            $product->saveFile($request);
            DB::commit();

            return Response::success();
        } catch(Expectation $e){
            DB::rollback();

            return Response::error($e);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function get(Product $product)
    {
        try{
            return Response::success([
                'product' => $product
            ]);
        } catch(Exception $e) {
            return Response::error($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        Validations::validateProductUpdate($request);
        DB::beginTransaction();

        try{
            $product->removeProductPhoto();
            $product->productUpdate($request->all());
            $product->saveFile($request);
            DB::commit();

            return Response::success();
        } catch(Exception $e){
            DB::rollBack();

            return Response::error($e);
        }
    }


    public function detail(Product $product)
    {
        return view('product.detail', [
            'title' => 'Detail Produk',
            'product' => $product,
            'breadcrumbs'=>[
                [
                    'title' => 'Detail Produk',
                    'link' => route('product')

                ],[
                    'title' => 'Detail Produk',
                    'link' => route('product.detail', $product->id)
                ]
            ]

        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try{
            $product->productDestroy();
            DB::commit();

            return Response::delete();
        } catch(Exception $e){
            DB::rollBack();

            return Response::error($e);
        }
    }

    public function import(Request $request){
        Validations::validateImport($request);
        try {
			Product::importProductFromExcel($request);
			return Response::success();
		} catch (\Exception $e) {
			return Response::error($e);
		}
    }
}
