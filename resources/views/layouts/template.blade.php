<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title> Keuangan | PT Adiva Sumber Solusi </title>

	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="_token" content="{{ csrf_token() }}">
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="{{ url('img/icon_adiva.ico') }}" type="image/x-icon" />

	<link rel="stylesheet" href="{{ url('vendors/ladda/ladda-themeless.min.css') }}">
	<link rel="stylesheet" href="{{ url('vendors/jquery-confirm/jquery-confirm.css') }}">

	<link rel="stylesheet" href="{{ url('vendors/select2/select2.css') }}">
	<link rel="stylesheet" href="{{ url('css/custom/select2-atlantis.css') }}">

	<script src="{{ url('js/plugin/webfont/webfont.min.js') }}"></script>
	<script>
		WebFont.load({
			google: {
				"families": ["Lato:300,400,700,900"]
			},
			custom: {
				"families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands",
					"simple-line-icons"
				],
				urls: [`{{ url('css/fonts.min.css') }}`]
			},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>

	<!-- CSS Files -->
	<link rel="stylesheet" href="{{ url('css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/atlantis.min.css') }}">
	<link rel="stylesheet" href="{{ url('css/custom/app.css') }}">

	<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css"> -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.css" integrity="sha256-pODNVtK3uOhL8FUNWWvFQK0QoQoV3YA9wGGng6mbZ0E=" crossorigin="anonymous" />

	<!-- CSS Just for demo purpose, don't include it in your project -->
	<link rel="stylesheet" href="{{ url('css/demo.css') }}">
	<style>
		.lo {
			margin: auto;
			position: absolute;
			margin-left: 50px;
		}

		.mh {
			width: 100px;
			height: 50px;
		}

		.card-title .btn,
		.card-title .btn:hover {
			color: white;
			padding: .4rem 1rem;
		}

		table.table-bordered.dataTable tbody td {
			vertical-align: top !important;
		}
	</style>

	@yield('styles')

</head>

<body>
	<div class="wrapper">
		<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" data-background-color="blue">
				<div class="avatar-sm lo">
					<img src="{{ url('img/adiva.png') }}" alt="navbar brand" class=" mt--4 mh">
				</div>

				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse"
					data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="fa-solid fa-bars"></i>
					</span>
				</button>
				<button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
				<div class="nav-toggle">
					<button class="btn btn-toggle toggle-sidebar">
						<i class="icon-menu"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->

			<!-- Navbar Header -->
			<nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">

				<div class="container-fluid">
					<ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
						<li class="nav-item toggle-nav-search hidden-caret">
							<a class="nav-link" data-toggle="collapse" href="#search-nav" role="button"
								aria-expanded="false" aria-controls="search-nav">
								<i class="fa fa-search"></i>
							</a>
						</li>
						<li class="nav-item dropdown hidden-caret">
							<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"
								aria-expanded="false">
								<div class="avatar-sm">
									<img src="{{ auth()->user()->avatarLink() }}" alt="..."
										class="avatar-img rounded-circle">
							</a>
							<ul class="dropdown-menu dropdown-user animated fadeIn">
								<div class="dropdown-user-scroll scrollbar-outer">
									<li>
										<div class="user-box">
											<div class="avatar-lg"><img src="{{ auth()->user()->avatarLink() }}"
													alt="..." class="avatar-img rounded"></div>
											<div class="u-text">
												<h4>{{ Auth::user()->name }}</h4>
												<p class="text-muted">{{ Auth::user()->email }}</p><a
													href="profile.html" class="btn btn-xs btn-secondary btn-sm">Lihat
													Profile</a>
											</div>
										</div>
									</li>
									<li>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
									</li>
								</div>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
			<!-- End Navbar -->
		</div>

		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img src="{{ auth()->user()->avatarLink() }}" alt="..."
								class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
								<span>
									{{ Auth::user()->name }}
									<span class="user-level">{{ Auth::user()->role }}</span>

								</span>
							</a>

						</div>
					</div>
					<ul class="nav nav-primary" id="menu-nav">
						
						@include('layouts.menu')
					</ul>
				</div>
			</div>
		</div>
		<!-- End Sidebar -->

		<div class="main-panel">
			<div class="content">
				@if(isset($breadcrumbs))
				<div class="page-inner">
					<div class="page-header">
						<h4 class="page-title"> {{ $title ?? 'Judul' }} </h4>
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="{{ url('dashboard') }}">
									<i class="flaticon-home"></i>
								</a>
							</li>

							@foreach ($breadcrumbs as $breadcrumb)
								<li class="separator">
									<i class="flaticon-right-arrow"></i>
								</li>
								<li class="nav-item">
									<a href="{{ $breadcrumb['link'] }}"> {{ $breadcrumb['title'] }} </a>
								</li>
							@endforeach

						</ul>
					</div>

					@yield('content')

				</div>
				@else
					@yield('content')
				@endif
			</div>

			<footer class="footer">
				<div class="container-fluid">
					<div class="copyright ml-auto">
						<p class="social-text"> Copyright &copy; 2022 | <a href="https://adiva.co.id"
								target="_blank">
								PT.
								Adiva Sumber Solusi </a> . All rights reserved. </p>
					</div>
				</div>
			</footer>
		</div>

	</div>

	@yield('modal')

	<!-- LIBARARY JS -->
	<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


	<!--   Core JS Files   -->

	{{-- ON ERRORR PAGE TRANSACTIONS --}}
	<script src="{{ url('js/core/popper.min.js') }}"></script>
	<script src="{{ url('js/core/bootstrap.min.js') }}"></script>

	<!-- jQuery UI -->
	<script src="{{ url('js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
	<script src="{{ url('js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js') }}"></script>

	<!-- jQuery Scrollbar -->
	<script src="{{ url('js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>


	<!-- Chart JS -->

	<!-- jQuery Sparkline -->
	<script src="{{ url('js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

	<!-- Chart Circle -->

	<!-- Datatables -->
	<script src="{{ url('js/plugin/datatables/datatables.min.js') }}"></script>

	<!-- Bootstrap Notify -->
	<script src="{{ url('js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

	<!-- jQuery Vector Maps -->

	<!-- Sweet Alert -->
	<script src="{{ url('js/plugin/sweetalert/sweetalert.min.js') }}"></script>

	<!-- Atlantis JS komen -->
	<script src="{{ url('js/atlantis.min.js') }}"></script>


	<script src="{{ url('vendors/ladda/spin.min.js') }}"></script>
	<script src="{{ url('vendors/ladda/ladda.min.js') }}"></script>
	<script src="{{ url('vendors/ladda/ladda.jquery.min.js') }}"></script>
	<script src="{{ url('vendors/jquery-confirm/jquery-confirm.js') }}"></script>
	<script src="{{ url('vendors/select2/select2.min.js') }}"></script>

	<script src="{{ url('js/myJs.js') }}"></script>

	<script type="text/javascript">
		const setActiveMenu = () => {
			let isFoundLink = false;
			let path = [];
			window.location.pathname.split("/").forEach(item => {
				if (item !== "") path.push(item);
			})
			let lengthPath = path.length;
			let lengthUse = lengthPath;
			let origin = window.location.origin;

			while (lengthUse >= 1) {
				let link = '';
				for (let i = 0; i < lengthUse; i++) {
					link += `/${path[i]}`;
				}
				$.each($('#menu-nav').find('a'), (i, elem) => {
					if ($(elem).attr('href') == `${origin}${link}`) {
						$(elem).parent(' ').addClass('active')
						$(elem).parents('li.nav-item').addClass('active').addClass('submenu')
						$(elem).parents('li.nav-item').find(`.collapse`).addClass('show')
					}
				})

				if (isFoundLink) break;
				lengthUse--;
			}
		}


		setActiveMenu();

		$('.dt').DataTable()
	</script>

	@yield('scripts')

</body>

</html>