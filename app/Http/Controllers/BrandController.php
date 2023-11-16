<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Brand;
use App\MyClass\Validations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\MyClass\Response;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
			return Brand::dataTable($request);
		}

		return view('brand.index', [
			'title'			=> 'Merek',
			'breadcrumbs'	=> [
				[
					'title'	=> 'Merek',
					'link'	=> route('brand')
				]
			]
		]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validations::validateBrandCreate($request);
        DB::beginTransaction();

        try{
            Brand::createBrand($request->all());
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
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        Validations::validateImport($request);
		try {
			Brand::importFromExcel($request);
			return \Res::success();
		} catch (\Exception $e) {
			return \Res::error($e);
		}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function get(Brand $brand)
    {
        try {
			return Response::success([
				'brand' => $brand
			]);
		} catch (Exception $e) {
			return Response::error($e);
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBrandRequest  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        Validations::validateBrandUpdate($request, $brand->id);
        DB::beginTransaction();

        try {
            $brand->updateBrand($request->all());
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
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        DB::beginTransaction();

        try{
            $brand->deleteBrand();
            DB::commit();

            return Response::delete();
        } catch(Exception $e){
            DB::rollBack();

            return Response::error($e);
        }
    }
}