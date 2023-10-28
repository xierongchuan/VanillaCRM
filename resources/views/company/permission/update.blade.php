@extends('layouts.main')

@section('title', 'Изменитть Право '.$permission -> name)

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Изменить право доступа <b>{{$permission -> name}}</b> в <b>{{$company -> name}}</b></h2>
			<form action="{{route('company.permission.modify', compact('company', 'permission'))}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="name">Название:</label>
					<input type="text" class="form-control" id="name" name="name" value="{{$permission -> name}}" required>
				</div>

				<div class="form-group mb-2">
					<label for="data">Значение:</label>
					<textarea class="form-control" name="data" id="data" cols="30" rows="10">{{$permission -> data}}</textarea>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Обновить</button>
				</div>
			</form>
		</div>
	</div>

@endsection
