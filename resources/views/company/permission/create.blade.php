@extends('layouts.main')

@section('title', 'Создать Право')

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Создать право доступа в <b>{{$company -> name}}</b></h2>
			<form action="{{route('company.permission.store', $company -> id)}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="name">Имя:</label>
					<input type="text" class="form-control" id="name" name="name" value="{{old('name')}}" required>
				</div>
				<div class="form-group mb-2">
					<label for="value">Идентификатор:</label>
					<input type="text" class="form-control" id="value" name="value" value="{{old('value')}}" required>
				</div>
				<div class="form-group mb-2">
					<label for="data">Значение:</label>
					<textarea class="form-control" name="data" id="data" cols="30" rows="10">{{old('data')}}</textarea>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Создать</button>
				</div>
			</form>
		</div>
	</div>

@endsection
