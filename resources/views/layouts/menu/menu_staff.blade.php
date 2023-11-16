<li class="nav-item">
	<a href="{{ route('dashboard') }}">
		<i class="fas fa-home"></i>
		<p> Dashboard </p>
	</a>
</li>

<li class="nav-section">
	<h4 class="text-section"> Menu </h4>
</li>

<li class="nav-item">
	<a data-toggle="collapse" href="#base">
		<i class="fas fa-database"></i>
		<p> Master Finansial </p>
		<span class="caret"></span>
	</a>
	<div class="collapse" id="base">
		<ul class="nav nav-collapse">
			<li>
				<a href="{{ route('income_category') }}">
					<i class="fas fa-donate"></i>
					<p> Kategori Pemasukan </p>
				</a>
			</li>
			<li>
				<a href="{{ route('expense_category') }}">
					<i class="fas fa-shopping-cart"></i>
					<p> Kategori Pengeluaran </p>
				</a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a data-toggle="collapse" href="#warhouse">
		<i class="fa fa-warehouse"></i>
        <p> Master Gudang </p>
		<span class="caret"></span>
	</a>
	<div class="collapse" id="warhouse">
		<ul class="nav nav-collapse">
			<li>
				<a href="{{ route('warehouse') }}">
					<i class="fa fa-warehouse"></i>
					<p> Gudang </p>
				</a>
			</li>
			<li>
				<a href="{{ route('product') }}">
					<i class="fa fa-boxes"></i>
					<p> Produk </p>
				</a>
			</li>
			<li>
				<a href="{{ route('supplier') }}">
					<i class="fas fa-dolly"></i>
					{{-- <i class="fa fa-address-card-o" aria-hidden="true"></i> --}}
					<p> Supplier </p>
				</a>
			</li>
			<li>
				<a href="{{ route('brand') }}">
					<i class="fas fa-box-open"></i>
					<p> Merek </p>
				</a>
			</li>
			<li>
				<a href="{{ route('product_type') }}">
					<i class="fa fa-archive"></i>
					<p> Jenis Produk </p>
				</a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a data-toggle="collapse" href="#manageWarehouse">
		<i class="fa fa-truck"></i>
        <p> Kelola Gudang </p>
		<span class="caret"></span>
	</a>
	<div class="collapse" id="manageWarehouse">
		<ul class="nav nav-collapse">
			<li>
				<a href="{{ route('incoming-goods') }}">
					<i class="fa fa-box"></i>
					<p> Barang Masuk </p>
				</a>
			</li>
			
			<li>
				<a href="{{ route('outgoing-goods') }}">
					<i class="fa fa-box-open"></i>
					<p> Barang Keluar </p>
				</a>
			</li>

			<li>
				<a href="{{ route('item-incoming-good-details') }}">
					<i class="fa fa-box"></i>
					<p> Item Barang Masuk </p>
				</a>
			</li>

			<li class="">
				<a href="{{ route('item-outgoing-good-detail') }}">
					<i class="fa fa-box-open"></i>
					<p> Item Barang Keluar </p>
				</a>
			</li>

			<li>
				<a href="{{ route('stock-adjustment') }}">
					<i class="fa fa-cube"></i>
					<p> Penyesuaian Stok</p>
				</a>
			</li>

			<li class="">
				<a href="{{ route('warehouse_stock') }}">
					<i class="fa fa-cubes"></i>
					<p> Stok Gudang </p>
				</a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a href="{{ route('transaction') }}">
		<i class="fas fa-money-bill-wave"></i>
		<p> Transaksi </p>
	</a>
</li>

<li class="nav-item">
	<a href="{{ route('reminder') }}">
		<i class="fas fa-clock"></i>
		<p> Reminder </p>
	</a>
</li>

<li class="nav-section">
	<h4 class="text-section"> Report </h4>
</li>

<li class="nav-item">
	<a href="{{ route('report') }}">
		<i class="fas fa-file"></i>
		<p> Laporan </p>
	</a>
</li>

<li class="nav-section">
	<span class="sidebar-mini-icon">
	<i class="fa fa-ellipsis-h"></i>
	</span>
	<h4 class="text-section"> Configuration </h4>
</li>

<li class="nav-item">
	<a data-toggle="collapse" href="#menu-setting">
		<i class="fas fa-wrench"></i>
		<p> Pengaturan </p>
		<span class="caret"></span>
	</a>
	<div class="collapse" id="menu-setting">
		<ul class="nav nav-collapse">
			<li>
				<a href="{{ route('setting.change_password') }}">
					<i class="fas fa-key"></i>
					<p> Ganti Password </p>
				</a>
			</li>
			<li>
				<a href="{{ route('setting.profile') }}">
					<i class="fas fa-user-edit"></i>
					<p> Profil </p>
				</a>
			</li>
		</ul>
	</div>
</li>

<li class="nav-item">
	<a href="{{ url('logout') }}">
		<i class="fas fa-sign-out-alt"></i>
		<p> Log out </p>
	</a>
</li>
