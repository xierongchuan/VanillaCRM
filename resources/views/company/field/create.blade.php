@extends('layouts.main')

@section('title', 'Field Create')

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Создать Ссылку в <b>{{$company -> name}}</b></h2>
			<form action="{{route('company.field.store', $company -> id)}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="title">Title:</label>
					<input type="text" class="form-control" id="title" name="title" required>
				</div>
				<div class="form-group mb-2">
					<label for="link">Link:</label>
					<input type="link" autocomplete="off" class="form-control" id="link" name="link" required>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Create</button>
				</div>
			</form>
		</div>
	</div>

@endsection
