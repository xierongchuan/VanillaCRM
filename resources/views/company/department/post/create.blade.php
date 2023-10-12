@extends('layouts.main')

@section('title', 'Post Create')

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Create Post in <b>{{$department -> name}}</b></h2>
			<form action="{{route('company.department.post.store', compact('company', 'department'))}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="name">Name:</label>
					<input type="text" class="form-control" id="name" name="name" required>
				</div>

				<div class="form-group mb-2">
					<label for="permissions">Permissions: </label>
					<select class="form-select" multiple size="8" aria-label="size 3 select example" name="permission[]">
						@foreach($permissions as $permission)
							<option value="{{$permission -> id}}">{{$permission -> name}}</option>
						@endforeach
					</select>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Create</button>
				</div>
			</form>
		</div>
	</div>

@endsection
