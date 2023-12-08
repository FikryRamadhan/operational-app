<?php

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseStockController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IncomingGoodsController;
use App\Http\Controllers\IncomingGoodDetailsController;
use App\Http\Controllers\OutgoingGoodsController;
use App\Http\Controllers\OutgoingGoodsDetailController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportReceiverController;
use App\Http\Controllers\StockAdjustmentDetailsController;
use App\Http\Controllers\StockAdjustmentsController;
use App\Http\Controllers\TransactionGroupAccessController;

Auth::routes();


Route::group([ 'middleware' => 'auth' ], function(){

	Route::redirect('/', 'dashboard');
	Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
	Route::get('logout', [LoginController::class, 'logout'])->name('logout');
	Route::get('import-templates/{filename}', [DashboardController::class, 'templateImport'])->name('import_templates');


	// Transaction Group
	Route::prefix('transaction-group')->group(function(){
		Route::get('/', [MasterDataController::class, 'transactionGroupIndex'])->name('transaction_group');
		Route::post('store', [MasterDataController::class, 'transactionGroupStore'])->name('transaction_group.store');
		Route::post('import', [MasterDataController::class, 'transactionGroupImport'])->name('transaction_group.import');
		Route::put('{transactionGroup}/update', [MasterDataController::class, 'transactionGroupUpdate'])->name('transaction_group.update');
		Route::get('{transactionGroup}/get', [MasterDataController::class, 'transactionGroupGet'])->name('transaction_group.get');
		Route::delete('{transactionGroup}/destroy', [MasterDataController::class, 'transactionGroupDestroy'])->name('transaction_group.destroy');
	});


	// Income Category
	Route::prefix('income-category')->group(function(){
		Route::get('/', [MasterDataController::class, 'incomeCategoryIndex'])->name('income_category');
		Route::post('store', [MasterDataController::class, 'incomeCategoryStore'])->name('income_category.store');
		Route::post('import', [MasterDataController::class, 'incomeCategoryImport'])->name('income_category.import');
		Route::put('{category}/update', [MasterDataController::class, 'incomeCategoryUpdate'])->name('income_category.update');
		Route::get('{category}/get', [MasterDataController::class, 'incomeCategoryGet'])->name('income_category.get');
		Route::delete('{category}/destroy', [MasterDataController::class, 'incomeCategoryDestroy'])->name('income_category.destroy');
	});


	// Expense Category
	Route::prefix('expense-category')->group(function(){
		Route::get('/', [MasterDataController::class, 'expenseCategoryIndex'])->name('expense_category');
		Route::post('store', [MasterDataController::class, 'expenseCategoryStore'])->name('expense_category.store');
		Route::post('import', [MasterDataController::class, 'expenseCategoryImport'])->name('expense_category.import');
		Route::put('{category}/update', [MasterDataController::class, 'expenseCategoryUpdate'])->name('expense_category.update');
		Route::get('{category}/get', [MasterDataController::class, 'expenseCategoryGet'])->name('expense_category.get');
		Route::delete('{category}/destroy', [MasterDataController::class, 'expenseCategoryDestroy'])->name('expense_category.destroy');
	});

	Route::get('get-category', [MasterDataController::class, 'getCategory'])->name('get_category');

	// Product Type
	Route::prefix('product-type')->group(function(){
		Route::get('/', [ProductTypeController::class, 'Index'])->name('product_type');
		Route::post('store', [ProductTypeController::class, 'Store'])->name('product_type.store');
		Route::post('import', [ProductTypeController::class, 'Import'])->name('product_type.import');
		Route::put('{product_type}/update', [ProductTypeController::class, 'Update'])->name('product_type.update');
		Route::get('{product_type}/get', [ProductTypeController::class, 'Get'])->name('product_type.get');
		Route::delete('{product_type}/destroy', [ProductTypeController::class, 'Destroy'])->name('product_type.destroy');
	});


	// Product
	Route::prefix('product')->group(function(){
		Route::get('/', [ProductController::class, 'Index'])->name('product');
		Route::post('/store', [ProductController::class, 'Store'])->name('product.store');
		Route::post('/import', [ProductController::class, 'Import'])->name('product.import');
		Route::put('/{product}/update', [ProductController::class, 'Update'])->name('product.update');
		Route::get('/{product}/get', [ProductController::class, 'Get'])->name('product.get');
        Route::get('{product}/detail', [ProductController::class, 'detail'])->name('product.detail');
		Route::delete('/{product}/destroy', [ProductController::class, 'Destroy'])->name('product.destroy');
	});


	// Brands
	Route::prefix('brand')->group(function(){
		Route::get('/', [BrandController::class, 'Index'])->name('brand');
		Route::post('store', [BrandController::class, 'Store'])->name('brand.store');
		Route::post('import', [BrandController::class, 'Import'])->name('brand.import');
		Route::put('{brand}', [BrandController::class, 'Update'])->name('brand.update');
		Route::get('{brand}/get', [BrandController::class, 'Get'])->name('brand.get');
		Route::delete('{brand}/destroy', [BrandController::class, 'Destroy'])->name('brand.destroy');
	});

	// Suppliers
	Route::prefix('supplier')->group(function(){
		Route::get('/', [SupplierController::class, 'Index'])->name('supplier');
		Route::post('store', [SupplierController::class, 'Store'])->name('supplier.store');
		Route::post('import', [SupplierController::class, 'Import'])->name('supplier.import');
		Route::put('{supplier}/update', [SupplierController::class, 'Update'])->name('supplier.update');
		Route::get('{supplier}/get', [SupplierController::class, 'Get'])->name('supplier.get');
		Route::delete('{supplier}/destroy', [SupplierController::class, 'Destroy'])->name('supplier.destroy');
	});
	// Warehouse
	Route::prefix('warehouse')->group(function(){
		Route::get('/', [WarehouseController::class, 'Index'])->name('warehouse');
		Route::post('store', [WarehouseController::class, 'Store'])->name('warehouse.store');
		Route::post('import', [WarehouseController::class, 'Import'])->name('warehouse.import');
		Route::put('{warehouse}/update', [WarehouseController::class, 'Update'])->name('warehouse.update');
		Route::get('{warehouse}/get', [WarehouseController::class, 'Get'])->name('warehouse.get');
		Route::delete('{warehouse}/destroy', [WarehouseController::class, 'Destroy'])->name('warehouse.destroy');
	});
	// Warehouse_stock
	Route::prefix('warehouse_stock')->group(function(){
		Route::get('/', [WarehouseStockController::class, 'index'])->name('warehouse_stock');
		Route::get('{warehouse}/detail', [WarehouseStockController::class, 'Detail'])->name('warehouse_stock.detail');
		Route::get('{warehouse}/export', [WarehouseStockController::class, 'Export'])->name('warehouse_stock.export');
	});

    // Route Incoming-Good
   Route::prefix('incoming-goods')->group(function(){
        Route::get('/', [IncomingGoodsController::class, 'index'])->name('incoming-goods');
        Route::get('create', [IncomingGoodsController::class, 'create'])->name('incoming-goods.create');
        Route::post('store', [IncomingGoodsController::class, 'store'])->name('incoming-goods.store');
        Route::get('{incomingGoods}/edit', [IncomingGoodsController::class, 'edit'])->name('incoming-goods.edit');
        Route::put('{incomingGoods}/update', [IncomingGoodsController::class, 'update'])->name('incoming-goods.update');
        Route::delete('{incomingGoods}/destroy', [IncomingGoodsController::class, 'destroy'])->name('incoming-goods.destroy');
		Route::get('{incomingGoods}/detail', [incomingGoodDetailsController::class, 'detail'])->name('incoming-goods.detail');
    });

    Route::prefix('incoming-good-detail')->group(function(){
        Route::get('/', [incomingGoodDetailsController::class, 'index'])->name('item-incoming-good-details');
        Route::get('{incomingGoodDetail}/edit', [incomingGoodDetailsController::class, 'edit'])->name('incoming-good-detail.edit');
        Route::put('{incomingGoodDetail}/update', [incomingGoodDetailsController::class, 'update'])->name('incoming-good-detail.update');
        Route::get('{incomingGoods}/detail', [incomingGoodDetailsController::class, 'detail'])->name('incoming-goods.detail');
		Route::delete('{incomingGoodDetail}/destroy', [incomingGoodDetailsController::class, 'destroy'])->name('incoming-good-detail.destroy');
    });


	// Route Outgoing-Good (Barang Keluar)
    Route::prefix('outgoing-goods')->group(function(){
        Route::get('/', [OutgoingGoodsController::class, 'index'])->name('outgoing-goods');
        Route::get('create', [OutgoingGoodsController::class, 'create'])->name('outgoing-goods.create');
        Route::post('store', [OutgoingGoodsController::class, 'store'])->name('outgoing-goods.store');
        Route::get('{outgoingGoods}/edit', [OutgoingGoodsController::class, 'edit'])->name('outgoing-goods.edit');
        Route::put('{outgoingGoods}/update', [OutgoingGoodsController::class, 'update'])->name('outgoing-goods.update');
        Route::delete('{outgoingGoods}/destroy', [OutgoingGoodsController::class, 'destroy'])->name('outgoing-goods.destroy');
		Route::get('{outgoingGoods}/detail', [OutgoingGoodsDetailController::class, 'detail'])->name('outgoing-goods.detail');
    });

	// detail Barang Keluar
	Route::prefix('outgoing-good-detail')->group(function(){
		Route::get('/', [OutgoingGoodsDetailController::class, 'index'])->name('item-outgoing-good-detail');
		Route::get('{outgoingGoodDetail}/edit', [OutgoingGoodsDetailController::class, 'edit'])->name('outgoing-good-detail.edit');
        Route::put('{outgoingGoodDetail}/update', [OutgoingGoodsDetailController::class, 'update'])->name('outgoing-good-detail.update');
        Route::get('{outgoingGoods}/detail', [OutgoingGoodsDetailController::class, 'detail'])->name('outgoing-good-detail.detail');
        Route::delete('{outgoingGoodDetail}/destroy', [OutgoingGoodsDetailController::class, 'destroy'])->name('outgoing-good-detail.destroy');
    });

	// Route Stock Adjustment (Tambah Barang)
    Route::prefix('stock-adjustment')->group(function(){
        Route::get('/', [StockAdjustmentsController::class, 'index'])->name('stock-adjustment');
        Route::get('create', [StockAdjustmentsController::class, 'create'])->name('stock-adjustment.create');
        Route::post('store', [StockAdjustmentsController::class, 'store'])->name('stock-adjustment.store');
        Route::get('{stockAdjustment}/edit', [StockAdjustmentsController::class, 'edit'])->name('stock-adjustment.edit');
        Route::put('{stockAdjustment}/update', [StockAdjustmentsController::class, 'update'])->name('stock-adjustment.update');
        Route::delete('{stockAdjustment}/destroy', [StockAdjustmentsController::class, 'destroy'])->name('stock-adjustment.destroy');
		Route::get('{stockAdjustment}/detail', [StockAdjustmentDetailsController::class, 'detail'])->name('stock-adjustment.detail');
    });

	Route::prefix('stock-adjustment-detail')->group(function(){
		Route::delete('{stockAdjustmentDetail}/destroy', [StockAdjustmentDetailsController::class, 'destroy'])->name('stock-adjustment-detail.destroy');
	});

	// Transaction
	Route::prefix('transaction')->group(function(){
		Route::get('/', [TransactionController::class, 'index'])->name('transaction');
		Route::get('create', [TransactionController::class, 'create'])->name('transaction.create');
		Route::get('{transaction}/edit', [TransactionController::class, 'edit'])->name('transaction.edit');
		Route::get('{transaction}/detail', [TransactionController::class, 'detail'])->name('transaction.detail');
		Route::post('store', [TransactionController::class, 'store'])->name('transaction.store');
		Route::post('import', [TransactionController::class, 'import'])->name('transaction.import');
		Route::put('{transaction}/update', [TransactionController::class, 'update'])->name('transaction.update');
		Route::get('{transaction}/get', [TransactionController::class, 'get'])->name('transaction.get');
		Route::post('{transaction}/verify', [TransactionController::class, 'verify'])->name('transaction.verify');
		Route::post('verify-selected', [TransactionController::class, 'verifySelected'])->name('transaction.verify_selected');
		Route::post('verify-all', [TransactionController::class, 'verifyAll'])->name('transaction.verify_all');
		Route::delete('{transaction}/destroy', [TransactionController::class, 'destroy'])->name('transaction.destroy');
	});

	// Transaction Group Access
	Route::prefix('transaction-group-access')->group(function(){
		Route::get('/', [TransactionGroupAccessController::class, 'index'])->name('transaction_group_access');
		Route::get('create', [TransactionGroupAccessController::class, 'create'])->name('transaction_group_access.create');
		Route::post('store', [TransactionGroupAccessController::class, 'store'])->name('transaction_group_access.store');
		Route::delete('{transactionGroupAccess}/destroy', [TransactionGroupAccessController::class, 'destroy'])->name('transaction_group_access.destroy');
	});

	// Reminder
	Route::prefix('reminder')->group(function(){
		Route::get('/', [ReminderController::class, 'index'])->name('reminder');
		Route::get('create', [ReminderController::class, 'create'])->name('reminder.create');
		Route::get('{reminder}/edit', [ReminderController::class, 'edit'])->name('reminder.edit');
		Route::get('{reminder}/detail', [ReminderController::class, 'detail'])->name('reminder.detail');
		Route::post('store', [ReminderController::class, 'store'])->name('reminder.store');
		Route::put('{reminder}/update', [ReminderController::class, 'update'])->name('reminder.update');
		Route::delete('{reminder}/destroy', [ReminderController::class, 'destroy'])->name('reminder.destroy');
		Route::post('{reminder}/check', [ReminderController::class, 'check'])->name('reminder.check');
	});

	// User
	Route::prefix('user')->group(function(){
		Route::get('/', [UserController::class, 'index'])->name('user');
		Route::get('create', [UserController::class, 'create'])->name('user.create');
		Route::get('{user}/edit', [UserController::class, 'edit'])->name('user.edit');
		Route::get('{user}/detail', [UserController::class, 'detail'])->name('user.detail');
		Route::post('store', [UserController::class, 'store'])->name('user.store');
		Route::put('{user}/update', [UserController::class, 'update'])->name('user.update');
		Route::get('{user}/get', [UserController::class, 'get'])->name('user.get');
		Route::delete('{user}/destroy', [UserController::class, 'destroy'])->name('user.destroy');
	});

	// Report
	Route::prefix('report')->group(function () {
		Route::get('/', [ReportController::class, 'index'])->name('report');
		Route::get('transaction-generate', [ReportController::class, 'transactionGenerate'])->name('report.transaction_generate');
		Route::get('transaction-per-category-generate', [ReportController::class, 'transactionPerCategoryGenerate'])->name('report.transaction_per_category_generate');
		Route::get('income-generate', [ReportController::class, 'incomeGenerate'])->name('report.income_generate');
		Route::get('expense-generate', [ReportController::class, 'expenseGenerate'])->name('report.expense_generate');
	});

	// Report Receiver
	Route::prefix('report-receiver')->group(function(){
		Route::get('/', [ReportReceiverController::class, 'index'])->name('report_receiver');
		Route::get('create', [ReportReceiverController::class, 'create'])->name('report_receiver.create');
		Route::post('store', [ReportReceiverController::class, 'store'])->name('report_receiver.store');
		Route::get('{reportReceiver}/edit', [ReportReceiverController::class, 'edit'])->name('report_receiver.edit');
		Route::put('{ }', [ReportReceiverController::class, 'update'])->name('report_receiver.update');
		Route::delete('{reportReceiver}/destroy', [ReportReceiverController::class, 'destroy'])->name('report_receiver.destroy');
	});

	// Setting
	Route::prefix('setting')->group(function(){
		Route::get('change-password', [SettingController::class, 'changePassword'])->name('setting.change_password');
		Route::post('save-password', [SettingController::class, 'savePassword'])->name('setting.save_password');
		Route::get('profile', [SettingController::class, 'profile'])->name('setting.profile');
		Route::post('save-profile', [SettingController::class, 'saveProfile'])->name('setting.save_profile');
	});


});

Route::get('migrate', function(){
	\Artisan::call('config:clear');
	\Artisan::call('view:clear');
	\Artisan::call('cache:clear');
	\Artisan::call('route:clear');
	\Artisan::call('migrate');
});
