@extends('layouts.main')

@section('title', 'Update Department '.$department -> name)

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Update Department <b>{{$department -> name}}</b> in <b>{{$company -> name}}</b></h2>
			<form action="{{route('company.department.modify', compact('company', 'department'))}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="name">Name:</label>
					<input type="text" class="form-control" id="name" name="name" value="{{$department -> name}}" required>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Update</button>
				</div>
			</form>
		</div>
	</div>

@endsection
