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
		<p> Master Data </p>
		<span class="caret"></span>
	</a>
	<div class="collapse" id="base">
		<ul class="nav nav-collapse">
			<li>
				<a href="{{ route('transaction_group') }}">
					<i class="fas fa-tags"></i>
					<p> Grup Transaksi </p>
				</a>
			</li>
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
	<a href="{{ route('transaction') }}">
		<i class="fas fa-money-bill-wave"></i>
		<p> Transaksi </p>
	</a>
</li>

<li class="nav-item">
	<a href="{{ route('transaction_group_access') }}">
		<i class="fas fa-lock"></i>
		<p> Akses Grup Transaksi </p>
	</a>
</li>

<li class="nav-item">
	<a href="{{ route('user') }}">
		<i class="fas fa-users"></i>
		<p> User </p>
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

<li class="nav-item">
	<a href="{{ route('report_receiver') }}">
		<i class="fas fa-file-export"></i>
		<p> CC Pengiriman Laporan </p>
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