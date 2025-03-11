@extends('layouts.main')

@section('title', 'Создать Компанию')

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Создать Компанию</h2>
			<form action="{{route('company.store')}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="name">Название:</label>
					<input type="text" class="form-control" id="name" name="name" required>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Создать</button>
				</div>
			</form>
		</div>
	</div>

@endsection
