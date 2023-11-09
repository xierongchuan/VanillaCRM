@extends('layouts.main')

@section('title', 'Admin Create')

@section('content')

	<div class="row justify-content-center">
		<div class="col-lg-6">
			<h2 class="text-center mt-4">Создать Администратора</h2>
			<form action="{{route('admin.store')}}" method="post" class="bg-body-tertiary rounded p-3">
				@csrf

				<div class="form-group mb-2">
					<label for="login">Login:</label>
					<input type="text" autocomplete="off" class="form-control" id="login" name="login" required>
				</div>
				<div class="form-group mb-2">
					<label for="password">Password:</label>
					<input type="password" autocomplete="off" class="form-control" id="password" name="password" required>
				</div>
				<div class="form-group mb-2">
					<label for="full_name">Name:</label>
					<input type="text" class="form-control" id="full_name" name="full_name" required>
				</div>
				<div class="d-flex justify-content-center">
					<button type="submit" class="btn btn-primary">Create</button>
				</div>
			</form>

			<div class="p-2 border bg-body-tertiary rounded p-3 mt-2">
				<span class="lead">Администраторы: </span><br>
				@foreach($admins as $admin)
					<span>
						<div class="d-flex justify-content-between rounded bg-body shadow gx-1 my-1 p-2 pb-0">
								<div class="col-lg-9 lead"><a class="nav-link">{{$admin -> full_name}}</a></div>

								<div class="btn-group col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
								<a href="{{route('admin.delete', compact('admin'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
								</div>
							</div>
					</span>
				@endforeach
			</div>
		</div>
	</div>

@endsection
