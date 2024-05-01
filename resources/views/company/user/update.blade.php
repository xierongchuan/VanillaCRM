@extends('layouts.main')

@section('title', 'User Update')

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Update User in <b>{{$company -> name}}</b></h2>
			<form action="{{route('company.user.modify', compact('company', 'user'))}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<input type="hidden" id="user_post_id" value="{{$user -> post_id}}">

				<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" id="user_department" name="department" required>
					@foreach($departments as $department)
						<option value="{{$department -> id}}" @if($user -> dep_id == $department -> id)selected @endif>{{$department -> name}}</option>
					@endforeach
				</select>

				<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" id="user_post" name="post">
					<option value="" selected>Select Post</option>
					@foreach($posts as $post)
						<option value="{{$post -> id}}" @if($user -> post_id == $post -> id)selected @endif>{{$post -> name}}</option>
					@endforeach
				</select>

				<div class="form-group mb-2">
					<label for="full_name">Name:</label>
					<input type="text" class="form-control" id="full_name" name="full_name" value="{{$user -> full_name}}" required>
				</div>
				<div class="form-group mb-2">
					<label for="phone_number">Phone:</label>
					<input type="text" class="form-control" id="phone_number" name="phone_number" value="{{$user -> phone_number}}" required>
				</div>

				<div class="form-group mb-2">
					<label for="password">Password:</label>
					<input type="password" class="form-control" id="password" autocomplete="off" name="password" placeholder="Write the password here if you want to change it">
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Update</button>
				</div>
			</form>
		</div>
	</div>

@endsection
