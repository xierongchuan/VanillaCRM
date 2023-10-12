@extends('layouts.main')

@section('title', 'Sign In')

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Sign In</h2>
			<form action="{{route('auth.login')}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="login">Login:</label>
					<input type="text" class="form-control" id="login" name="login" required>
				</div>
				<div class="form-group mb-2">
					<label for="password">Password:</label>
					<input type="password" class="form-control" id="password" name="password" required>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Auth</button>
				</div>
			</form>
		</div>
	</div>

@endsection
