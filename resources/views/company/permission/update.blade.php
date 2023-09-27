@extends('layouts.main')

@section('title', 'Update Permission '.$permission -> name)

@section('content')

	<div class="row justify-content-center">
		<div class="col-md-6">
			<h2 class="text-center mt-4">Update Department <b>{{$permission -> name}}</b> in <b>{{$company -> name}}</b></h2>
			<form action="{{route('company.permission.modify', compact('company', 'permission'))}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="name">Name:</label>
					<input type="text" class="form-control" id="name" name="name" value="{{$permission -> name}}" required>
				</div>
				<div class="form-group mb-2">
					<label for="value">Value:</label>
					<input type="text" class="form-control" id="value" name="value" value="{{$permission -> value}}" required>
				</div>

				<div class="form-group mb-2">
					<label for="data">Data:</label>
					<textarea class="form-control" name="data" id="data" cols="30" rows="10">{{$permission -> data}}</textarea>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Update</button>
				</div>
			</form>
		</div>
	</div>

@endsection
