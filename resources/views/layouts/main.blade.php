<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{getenv('APP_NAME')}} - @yield('title')</title>

	{{-- Import Styles --}}
	@vite(['resources/sass/app.scss'])

	@yield('includes')

</head>
<body data-bs-theme="{{session('theme') ?? 'dark'}}">
<header class="container">
	<div class="row">
		<nav class="navbar navbar-expand-lg rounded-bottom bg-body-secondary">
			<div class="container-fluid">
				<a class="navbar-brand" href="{{route('home.index')}}"> {{getenv('APP_NAME')}}</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
						<li class="nav-item">
							<a class="
								nav-link

								@if(Route::currentRouteName() == 'home.index')
								active
								@endif

								" aria-current="page" href="{{route('home.index')}}">Home</a>
						</li>


						@if(@auth()->user()->role === 'admin')
							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									Company
								</a>
								<ul class="dropdown-menu">
									<li><a class="dropdown-item" href="{{route('company.list')}}">List</a></li>
									<li><a class="dropdown-item" href="{{route('company.create')}}">Create</a></li>
								</ul>
							</li>

							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									Admin
								</a>
								<ul class="dropdown-menu">
									<li><a class="dropdown-item" href="{{route('admin.create')}}">Create</a></li>
								</ul>
							</li>
						@endif

					</ul>


					<ul class="d-flex navbar-nav mb-2 mb-lg-0">
						@yield('nav_right')

						@hasSection('nav_right')
							<div class="vr mx-1  mr-2 d-none d-lg-block"></div>
						@endif

						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								Theme
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="{{route('theme.switch', 'light')}}">Light</a></li>
								<li><a class="dropdown-item" href="{{route('theme.switch', 'dark')}}">Dark</a></li>
							</ul>
						</li>

						@if(!@auth()->user()->role === 'admin')
							<li class="nav-item">
								<a class="
									nav-link

									@if(Route::currentRouteName() == 'auth.sign_in')
									active
									@endif

									" aria-current="page" href="{{route('auth.sign_in')}}">Sign In</a>
							</li>
						@else
							<li class="nav-item">
								<a class="nav-link" aria-current="page" href="{{route('auth.logout')}}"><i class="bi bi-box-arrow-right"></i></a>
							</li>
						@endif

					</ul>

				</div>
			</div>
		</nav>
	</div>
</header>

<main class="container">

	@if (Session::has('success'))
		<div class="alert alert-success mt-3">
			<ul>
				<li>{{ Session::get('success') }}</li>
			</ul>
		</div>
	@endif

	@if (Session::has('warning'))
		<div class="alert alert-warning mt-3">
			<ul>
				<li>{{ Session::get('warning') }}</li>
			</ul>
		</div>
	@endif

	@if ($errors->any())
		<div class="alert alert-danger mt-3">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@yield('content')

</main>

<footer>

</footer>

{{-- Import JavaScript --}}
@vite(['resources/js/app.js'])
</body>
</html>
