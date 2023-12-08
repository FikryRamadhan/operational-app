<?php

namespace App\Models;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
	use HasFactory;
	use SoftDeletes;

	protected $guarded = [''];

	public static function productCreate($request)
	{
		$product =  self::create($request);
		return $product;
	}

	public function productUpdate($request)
	{
		$this->update($request);

		return $this;
	}

	public function productDestroy()
	{
		$this->removeProductPhoto();
		return $this->delete();
	}

	public function perhitunganUlangStockProduct()
	{
		if ($this->warehouseStock) {
			$totalStock = $this->warehouseStock->sum('stock');
			$this->update([
				'stock' => $totalStock
			]);
		}
	}

	// Relationship
	public function brand()
	{
		return $this->belongsTo(Brand::class, 'id_brand')->withTrashed();
	}

	public function productType()
	{
		return $this->belongsTo(ProductType::class, 'id_product_type')->withTrashed();
	}
	public function warehouseStock()
	{
		return $this->hasMany(WarehouseStock::class, 'id_product');
	}

	// Upload Photo To Storage
	public function productFilePath()
	{
		return storage_path('app/public/product_photo/' . $this->file_photo);
	}

	public function productPhotoFileLink()
	{
		return url('storage/product_photo/' . $this->file_photo);
	}

	public function productFileLinkHtml()
	{
		if ($this->isHasProductPhoto()) {
			$href = '<a href="' . $this->productPhotoFileLink() . '" target="_blank"> Lihat Photo Product </a>';
			return $href;
		} else {
			return '<span class="text-danger"> Tidak Melampirkan Photo </span>';
		}
	}

	public function isHasProductPhoto()
	{
		if (empty($this->file_photo)) return false;
		return \File::exists($this->productFilePath());
	}

	public function removeProductPhoto()
	{
		if ($this->isHasProductPhoto()) {
			\File::delete($this->productFilePath());
			$this->update([
				'file_photo' => null
			]);
		}

		return $this;
	}

	public function saveFile($request)
	{
		if ($request->hasFile('file_photo')) {
			$this->removeProductPhoto();
			$file = $request->file('file_photo');
			$filename = date('YmdHis_') . $file->getClientOriginalName();
			$file->move(storage_path('app/public/product_photo/'), $filename);
			$this->update([
				'file_photo' => $filename,
			]);
		}

		return $this;
	}


	// For Data Table
	public function getBrandName()
	{
		return $this->brand ? $this->brand->brand_name : '-';
	}

	public function getProductTypeName()
	{
		return $this->productType ? $this->productType->product_type_name : '-';
	}
	public function getProduct()
	{
		return $this->product ? $this->product->product_name : '-';
	}


	public static function dataTable($request)
	{
		$data = self::select(['products.*'])
			->with('brand', 'productType',)
			->leftJoin('brands', 'products.id_brand', '=', 'brands.id')
			->leftJoin('product_types', 'products.id_product_type', '=', 'product_types.id');

		return \DataTables::eloquent($data)
			->addColumn('action', function ($data) {
				$action = '
                	<div class="dropdown">
						<button class="btn btn-primary px-2 py-1 dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Pilih Aksi
						</button>
						<div class="dropdown-menu">
                           <a class="dropdown-item" href="' . route('product.detail', $data->id) . '">
								<i class="fas fa-search mr-1"></i> Detail
							</a>
							<a class="dropdown-item edit" href="javascript:void(0);" data-edit-href="' . route('product.update', $data->id) . '" data-get-href="' . route('product.get', $data->id) . '">
								<i class="fas fa-pencil-alt mr-1"></i> Edit
							</a>
							<a class="dropdown-item delete" href="javascript:void(0)" data-delete-message="Yakin ingin menghapus <strong>' . $data->product_name . '</strong>?" data-delete-href="' . route('product.destroy', $data->id) . '">
								<i class="fas fa-trash mr-1"></i> Hapus
							</a>
						</div>
					</div>
                ';
				return $action;
			})
			->editColumn('brand.brand_name', function ($data) {
				return $data->getBrandName();
			})
			->editColumn('productType.product_type_name', function ($data) {
				return $data->getProductTypeName();
			})
			->editColumn('file_photo', function ($data) {
				return $data->productFileLinkHtml();
			})
			->rawColumns(['action', 'file_photo'])
			->make(true);
	}

	// Import Excel
	public static function importProductFromExcel($request)
	{
		return self::importFromExcel($request);
	}

	public static function importFromExcel($request)
	{
		$amount = 0;

		if (!empty($request->file_excel)) {
			$file = $request->file('file_excel');
			$filename = date('YmdHis_') . rand(100, 999) . '.' . $file->getClientOriginalExtension();
			$file->move(storage_path('app/public/temp_files'), $filename);
			$path = storage_path('app/public/temp_files/' . $filename);
			$parseData = \App\MyClass\SimpleXLSX::parse($path);

			if ($parseData) {
				$iter = 0;
				foreach ($parseData->rows() as $row) {
					$iter++;
					if ($iter == 1) continue;

					if (!empty($row[0])) {
						$product = self::where('product_name', $row[0])
							->first();

						// Check
						$brand = Brand::checkBrand($row[2]);
						// Dan Create Brand
						if (!$brand) {
							$brand = Brand::buatBrand($row[2]);
						}

						$idBrand = $brand->id;

						// Check dan create ProductType
						$productType = ProductType::checkProductType($row[3]);

						if (!$productType) {
							$productType =  ProductType::buatProductType($row[3]);
						}

						$idProductType = $productType->id;

						if (!$product) {
							DB::beginTransaction();
							try {
								self::create([
									'product_name' => $row[0],
									'model_name' => $row[1],
									'id_brand' => $idBrand,
									'id_product_type' => $idProductType,
									'minimal_stock' => $row[4],
									'description' => $row[5]
								]);

								$amount++;
								DB::commit();
							} catch (\Exception $e) {
								DB::rollback();
							}
						}
					}
				}
			}

			\File::delete($path);
		}
		return $amount;
	}

	public static function sendNotificationStock()
	{
		$stocks = WarehouseStock::getStockToWhatsapp();
		if ($stocks->count() > 0) {
			$message = "Pemberitahuan*\nSudah Ada barang Yang Kurang Dari Minimal Stock,Berikut Daftar Produk Nya:";
			foreach ($stocks as  $key => $stock) {
				$message .= "\n\n- " . $key . ": ";
				foreach ($stock as $s) {
					$message .= "\n-> " . $s->product->product_name . " Dengan Stock " . $s->stock;
				};
			};
			$message .= "\n\nTerima Kasih Atas Perhatiannya🙏";

			\App\MyClass\Whatsapp::sendChat([
				'to'	=> '6282316425264',
				'text'	=> $message
			]);
		}
		// else {
		// 	$message = "Stok Produk Masih Aman";
		// 	\App\MyClass\Whatsapp::sendChat([
		// 		'to'	=> '6283823115994',
		// 		'text'	=> $message
		// 	]);
		// }
	}
}
