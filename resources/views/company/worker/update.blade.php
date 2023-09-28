@extends('layouts.main')

@section('title', 'Worker Update')

@section('content')

	<div class="row justify-content-center">
		<div class="col-md-6">
			<h2 class="text-center mt-4">Update Worker in <b>{{$company -> name}}</b></h2>
			<form action="{{route('company.worker.modify', compact('company', 'worker'))}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<input type="hidden" id="worker_post_id" value="{{$worker -> post_id}}">

				<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" id="worker_department" name="department" required>
					@foreach($departments as $department)
						<option value="{{$department -> id}}" @if($worker -> dep_id == $department -> id)selected @endif>{{$department -> name}}</option>
					@endforeach
				</select>

				<select class="form-select form-select-lg mb-3" aria-label=".form-select-lg example" id="worker_post" name="post">
					<option value="" selected>Select Post</option>
					@foreach($posts as $post)
						<option value="{{$post -> id}}" @if($worker -> post_id == $post -> id)selected @endif>{{$post -> name}}</option>
					@endforeach
				</select>

				<div class="form-group mb-2">
					<label for="full_name">Name:</label>
					<input type="text" class="form-control" id="full_name" name="full_name" value="{{$worker -> full_name}}" required>
				</div>
				<div class="form-group mb-2">
					<label for="phone_number">Phone:</label>
					<input type="text" class="form-control" id="phone_number" name="phone_number" value="{{$worker -> phone_number}}" required>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Create</button>
				</div>
			</form>
		</div>
	</div>

@endsection
