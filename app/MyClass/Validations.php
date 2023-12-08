<?php

namespace App\MyClass;

class Validations
{

	public static function validateTransactionGroup($request, $exceptId = null)
	{
		$request->validate([
			'transaction_group_name' => 'required|unique:transaction_groups,transaction_group_name,' . $exceptId,
			'is_active' => 'required|in:yes,no'
		], [
			'transaction_group_name.required' => 'Nama grup transaksi wajib diisi',
			'transaction_group_name.unique' => 'Nama grup transaksi sudah ada',
			'is_active.required' => 'Status keaktifan wajib diisi',
			'is_active.in' => 'Status keaktifan tidak valid',
		]);
	}

	public static function validateIncomeCategory($request, $exceptId = null)
	{
		$request->validate([
			'category_name'	=> [new \App\Rules\UniqueIncomeCategoryName($exceptId)]
		]);
	}

	public static function validateExpenseCategory($request, $exceptId = null)
	{
		$request->validate([
			'category_name'	=> [new \App\Rules\UniqueExpenseCategoryName($exceptId)]
		]);
	}

	public static function validateTransaction($request, $exceptId = null)
	{
		$request->validate([
			'date' => 'required',
			'id_transaction_group' => 'required|exists:transaction_groups,id',
			'type' => 'required|in:Income,Expense',
			'id_category' => 'nullable|exists:categories,id',
			'description' => 'required',
			'nominal' => 'required|integer|min:1',
			'file_transaction_proof_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif,tiff|max:1024',
		], [
			'date.required' => 'Tanggal wajib diisi',
			'id_transaction_group.required' => 'Grup transaksi wajib diisi',
			'id_transaction_group.exists' => 'Grup transaksi tidak valid',
			'type.required' => 'Jenis wajib diisi',
			'type.in' => 'Jenis tidak valid',
			'id_category.exists' => 'Kategori tidak valid',
			'description.required' => 'Deskripsi wajib diisi',
			'nominal.required' => 'Nominal wajib diisi',
			'nominal.min' => 'Nominal minimal bernilai 0',
			'file_transaction_proof_upload.file' => 'Wajib bernilai file',
			'file_transaction_proof_upload.mimes' => 'Hanya mendukung ekstensi .pdf, .jpg, .jpeg, .png, .gif, .tiff',
			'file_transaction_proof_upload.max' => 'Ukuran Maksimal 1 MB',
		]);
	}

	public static function validateImport($request)
	{
		$request->validate([
			'file_excel' => 'required|file|mimes:xlsx,xls',
		], [
			'file_excel.required' => 'File excel wajib diisi',
			'file_excel.file' => 'Wajib bernilai file',
			'file_excel.mimes' => 'Hanya mendukung ekstensi .xlsx atau .xls',
		]);
	}

	public static function validateImportTransaction($request)
	{
		$request->validate([
			'file_excel' => 'required|file|mimes:xlsx,xls',
			'id_transaction_group' => 'required|exists:transaction_groups,id',
		], [
			'file_excel.required' => 'File excel wajib diisi',
			'file_excel.file' => 'Wajib bernilai file',
			'file_excel.mimes' => 'Hanya mendukung ekstensi .xlsx atau .xls',
			'id_transaction_group.required' => 'Grup transaksi wajib diisi',
			'id_transaction_group.exists' => 'Grup transaksi tidak valid',
		]);
	}

	public static function validateCreateUser($request)
	{
		$request->validate([
			'name' => 'required',
			'email' => 'required|unique:users,email',
			'password' => 'required',
			'confirm_password' => 'required|same:password',
			'role' => 'required|in:Staff,Owner',
		], [
			'name.required' => 'Nama wajib diisi',
			'email.required' => 'Nama wajib diisi',
			'email.unique' => 'Email sudah digunakan',
			'password.required' => 'Password wajib diisi',
			'confirm_password.required' => 'Wajib diisi',
			'confirm_password.same' => 'Password yang dimasukkan tidak sama',
			'role.required' => 'Role wajib diisi',
			'role.in' => 'Role tidak valid',
		]);
	}

	public static function validateEditUser($request, $userId)
	{
		$request->validate([
			'name' => 'required',
			'email' => 'required|unique:users,email,' . $userId,
			'password' => 'nullable',
			'confirm_password' => 'nullable|same:password',
			'role' => 'required|in:Staff,Owner',
		], [
			'name.required' => 'Nama wajib diisi',
			'email.required' => 'Nama wajib diisi',
			'email.unique' => 'Email sudah digunakan',
			'confirm_password.same' => 'Password yang dimasukkan tidak sama',
			'role.required' => 'Role wajib diisi',
			'role.in' => 'Role tidak valid',
		]);
	}

	public static function validateChangePassword($request, $userId)
	{
		$request->validate([
			'password' => ['required', new \App\Rules\ValidateUserPassword($userId)],
			'new_password' => 'required',
			'confirm_password' => 'required|same:new_password',
		], [
			'password.required' => 'Password lama wajib diisi',
			'new_password.required' => 'Password baru wajib diisi',
			'confirm_password.required' => 'Wajib diisi',
			'confirm_password.same' => 'Password baru yang dimasukkan tidak sama',
		]);
	}

	public static function validateProfileSave($request, $userId)
	{
		$request->validate([
			'name' => 'required',
			'email' => 'required|unique:users,email,' . $userId,
			'phone_number' => 'required',
		], [
			'name.required' => 'Nama lengkap wajib diisi',
			'email.required' => 'Email wajib diisi',
			'email.unique' => 'Email sudah digunakan',
			'phone_number.required' => 'Nomor telepon wajib diisi',
		]);
	}


	public static function validateTransactionGroupAccess($request)
	{
		$request->validate([
			'id_user' => 'required',
			'id_transaction_group' => ['required', new \App\Rules\CheckTransactionGroupAccess($request->id_user)],
		], [
			'id_user.required' => 'Staff wajib diisi',
			'id_transaction_group.required' => 'Grup transaksi wajib diisi',
		]);
	}


	public static function validateReminder($request)
	{
		$request->validate([
			'reminder_name' => 'required',
			'time' => 'required',
			'date' => 'required',
			'reminder_target' => 'required',
		], [
			'reminder_name.required' => 'Nama reminder wajib diisi',
			'time.required' => 'Waktu Wajib Di Isi',
			'date.required' => 'Tanggal acara wajib diisi',
			'reminder_target.required' => 'Nomor whatsapp target reminder wajib diisi',
		]);
	}

	public static function validateBrandCreate($request)
	{
		$request->validate([
			'brand_name' => 'required',
		], [
			'brand_name.required' => 'Nama Brand Wajib Diisi'
		]);
	}

	public static function validateBrandUpdate($request)
	{
		$request->validate([
			'brand_name' => 'required'
		], [
			'brand_name.required' => 'Nama Brand Wajib Diisi'
		]);
	}

	public static function validateProductTypeGroup($request, $exceptId = null)
	{
		$request->validate([
			'product_type_name' => 'required',
		], [
			'product_type_name.required' => 'jenis produk wajib diisi',
		]);
	}

	public static function updatevalidateProductType($request, $exceptId = null)
	{
		$request->validate([
			'product_type_name' => 'required',
		], [
			'product_type_name.required' => 'jenis produk wajib diisi',
		]);
	}

	public static function validateProductStore($request) {
		$request->validate([
			'product_name' => 'required',
			'id_product_type' => 'required',
			'minimal_stock' => 'required',
			'file_photo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif,tiff|max:1024'
		],[
			'product_name.required' => 'Nama Product Wajib Di Isi',
			'id_product_type.required' => 'Jenis Produk Wajib Di Isi',
			'minimal_stock.required' => 'Minimal Stock Wajib Di Isi',
			'file_photo.file' => 'File Harus Berupa File',
			'file_photo.mimes' => 'Hanya Mendukung ekstensi .pdf, .jpg, .jpeg, .png, .gif, .tiff',
		]);
	}

	public static function validateProductUpdate($request) {
		$request->validate([
			'product_name' => 'required',
		],[
			'product_name.required' => 'Nama Product Wajib Di Isi'
		]);
	}

	public static function validateSupplierCreate($request){
		$request->validate([
			'supplier_name' => 'required',
		],[
			'supplier_name.required' => 'Nama Supplier Wajib Diisi',
		]);
	}

	public static function validateSupplierUpdate($request){
		$request->validate([
			'supplier_name' => 'required',
		],[
			'supplier_name.required' => 'Nama Supplier Wajib Diisi',
		]);
	}
    public static function validateWarehouseStore($request){
		$request->validate([
			'warehouse_name' => 'required',
		],[
			'warehouse_name.required' => 'Nama Gudang Wajib Diisi',
		]);
	}
    public static function updateValidateWarehouse($request){
		$request->validate([
			'warehouse_name' => 'required',
		],[
			'warehouse_name.required' => 'Nama Gudang Wajib Diisi',
		]);
	}

    public static function storeIncomingGoods($request){
		$request->validate([
			'transaction_number' => 'required',
			'date' => 'required',
			'id_warehouse' => 'required',
			'id_supplier' => 'required',
			'amount.*' => 'required',
			'file_photo.*' => 'nullable|mimes:jpg,jpeg,png',
		], [
			'transaction_number.required' => 'No.Transaksi Harus Diisi',
			'date.required' => 'Tanggal Pengiriman Wajib Diisi',
			'id_supplier.required' => 'Supplier Harus Diisi',
			'id_warehouse.required' => 'Gudang Harus Diisi',
			'amount.*.required' => 'Jumlah Barang Wajib Diisi Wajib Diisi',
			'file_photo.*.mimes' => 'File Harus Berupa jpg,jpeg,png',
		]);
	}
	
	public static function updateIncomningDetail($request) {
		$request->validate([
			'id_product' => 'required',
			'amount' => 'required',
			'file_photo' => 'nullable|mimes:pdf,jpg,jpeg,png'
		],[
			'id_product.required' => 'Produk Wajib Diisi',
			'amount.required' => 'Jumlah Wajib Diisi',
			'file_photo.mimes' => 'Format File harus pdf atau gambar'
		]);
	}

	public static function updateOutgoingGoodDetail($request) {
		$request->validate([
			'id_product' => 'required',
			'amount' => 'required',
			'file_photo' => 'nullable|mimes:pdf,jpg,jpeg,png'
		],[
			'id_product.required' => 'Produk Wajib Diisi',
			'amount.required' => 'Jumlah Wajib Diisi',
			'file_photo.mimes' => 'Format File harus pdf atau gambar'
		]);
	}

	public static function storeOutgoingGoods($request){
		$request->validate([
			'transaction_number' => 'required',
			'date' => 'required',
			'id_warehouse' => 'required',
			'amount.*' => 'required',
			'file_photo.*' => 'nullable|mimes:jpg,jpeg,png',

		], [
			'transaction_number.required' => 'No.Transaksi Harus Diisi',
			'date.required' => 'Tanggal Pengiriman Wajib Diisi',
			'id_warehouse.required' => 'Gudang Harus Diisi',
			'amount.*.required' => 'Jumlah Barang Wajib Diisi Wajib Diisi',
			'file_photo.*.mimes' => 'File Harus Berupa jpg,jpeg,png',
		]);
	}

	public static function validationStockAdjustment($request){
		$request->validate([
			'transaction_number' => 'required',
			'date' => 'required',
			'id_warehouse' => 'required',
			'amount.*' => 'required',
			'file_photo.*' => 'nullable|mimes:jpg,jpeg,png',
		], [
			'transaction_number.required' => 'No.Transaksi Harus Diisi',
			'date.required' => 'Tanggal Pengiriman Wajib Diisi',
			'id_warehouse.required' => 'Gudang Harus Diisi',
			'amount.*.required' => 'Jumlah Barang Wajib Diisi Wajib Diisi',
			'file_photo.*.mimes' => 'File Harus Berupa jpg,jpeg,png',
		]);
	}

}
