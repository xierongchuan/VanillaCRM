@extends('layouts.main')

@section('title', 'Companies List')

@section('content')

	<div class="row flex-column align-items-center">
		@foreach($companies as $company)
			<div class="col-lg-9 bg-body-secondary rounded mt-3 p-2">
				<h2><b>{{$company -> name}}</b></h2>

				<div class="btn-group col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
					<a href="{{route('company.update', $company -> id)}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
					<a href="{{route('company.delete', $company -> id)}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
				</div>

				<div class="p-2 border rounded">
					<span class="lead">Departments: </span><br>
					@foreach($company -> departments as $department)
						<span>
							<div class="my-1 p-2 rounded bg-body shadow row justify-content-between gx-1">
								<div class="col-lg-9 lead"><a href="{{route('company.department.index', compact('company', 'department'))}}" class="nav-link">{{$department -> name}}</a></div>

								<div class="btn-group col-lg-3 p-0" role="group" aria-label="Basic mixed styles example">
									<a href="{{route('company.department.update', ['company' => $company, 'department' => $department])}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
									<a href="{{route('company.department.delete', ['company' =>  $company, 'department' => $department])}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
								</div>
							</div>
						</span>
					@endforeach

					<a href="{{route('company.department.create', $company -> id)}}" class="btn btn-success w-100 mt-1">Create <i class="bi bi-people"></i></a>

				</div>

				<div class="p-2 border rounded mt-2">
				<span class="lead">Permissions: </span><br>
				@foreach($company -> permissions as $permission)
				<span>
					<div class="my-1 p-2 rounded bg-body shadow row justify-content-between gx-1">
							<div class="col-lg-9 lead"><a href="#" class="nav-link">{{$permission -> name}}</a></div>

							<div class="btn-group col-lg-3 p-0" role="group" aria-label="Basic mixed styles example">
								<a href="{{route('company.permission.update', compact('company', 'permission'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
								<a href="{{route('company.permission.delete', compact('company', 'permission'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
							</div>
						</div>
					</span>
				@endforeach

				@if(config('app.debug'))
					<a href="{{route('company.permission.create', $company -> id)}}" class="btn btn-success w-100 mt-1">Create <i class="bi bi-person-vcard"></i></a>
				@endif

				</div>

				<div class="p-2 border rounded mt-2">
					<span class="lead">Users: </span><br>
					@foreach($company -> users as $user)
						<span>
						<div class="my-1 p-2 rounded bg-body shadow row justify-content-between gx-1">
							<div class="col-lg-9 lead"><a href="{{route('company.user.update', compact('company', 'user'))}}" class="nav-link">{{$user -> full_name}} ({{$user -> login}})</a></div>

							<div class="btn-group col-lg-3 p-0" role="group" aria-label="Basic mixed styles example">
								<a href="{{route('company.user.update', compact('company', 'user'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
								<a href="{{route('company.user.delete', compact('company', 'user'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
							</div>
						</div>
					</span>
					@endforeach

					<a href="{{route('company.user.create', compact('company'))}}" class="btn btn-success w-100 mt-1">Create <i class="bi bi-person"></i></a>
				</div>

			</div>
		@endforeach
	</div>

@endsection
