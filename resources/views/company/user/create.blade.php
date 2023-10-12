@extends('layouts.main')

@section('title', 'User Create')

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Create User in <b>{{$company -> name}}</b></h2>
			<form action="{{route('company.user.store', $company -> id)}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="login">Login:</label>
					<input type="text" class="form-control" id="login" name="login" required>
				</div>
				<div class="form-group mb-2">
					<label for="password">Password:</label>
					<input type="password" autocomplete="off" class="form-control" id="password" name="password" required>
				</div>
				<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" name="department" required>
					<option value="" selected>Select Department</option>
					@foreach($departments as $department)
						<option value="{{$department -> id}}">{{$department -> name}}</option>
					@endforeach
				</select>
				<div class="form-group mb-2">
					<label for="full_name">Name:</label>
					<input type="text" class="form-control" id="full_name" name="full_name" required>
				</div>
				<div class="form-group mb-2">
					<label for="phone_number">Phone:</label>
					<input type="text" class="form-control" id="phone_number" name="phone_number" required>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Create</button>
				</div>
			</form>
		</div>
	</div>

@endsection
