@extends('layouts.main')

@section('title', 'Department '.$department -> name)

@section('content')

	<div class="row flex-column align-items-center">
		<div class="col-lg-7 bg-body-secondary rounded mt-3 p-2">

			<div class="d-flex justify-content-between mx-2">
				<h2><b>{{$department -> name}} в {{$company -> name}}</b></h2>

				<div class="btn-group align-items-center col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
					<a href="{{route('company.department.update', ['company' => $company, 'department' => $department])}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
					<a href="{{route('company.department.delete', ['company' => $company, 'department' => $department])}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
				</div>
			</div>

			<div class="p-2 border rounded">
				<span class="lead">Должности: </span><br>
				@foreach($department -> posts as $post)
					<span>
						<div class="d-flex justify-content-between rounded bg-body shadow gx-1 my-1 p-2 pb-0">
								<div class="col-lg-9 lead"><a href="{{route('company.department.post.index', compact('company', 'department', 'post'))}}" class="nav-link">{{$post -> name}}</a></div>

								<div class="btn-group col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
									<a href="{{route('company.department.post.update', compact('company', 'department', 'post'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
								<a href="{{route('company.department.post.delete', compact('company', 'department', 'post'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
								</div>
							</div>
					</span>
				@endforeach

				<a href="{{route('company.department.post.create', compact('company', 'department'))}}" class="btn btn-success w-100 mt-1">Создать <i class="bi bi-person-vcard"></i></a>
			</div>

			<div class="p-2 border rounded mt-2">
				<span class="lead">Сотрудники: </span><br>
				@foreach($department -> users as $user)
					<span>
						<div class="d-flex justify-content-between rounded bg-body shadow gx-1 my-1 p-2 pb-0">
								<div class="col-lg-9 lead"><a href="{{route('company.user.update', compact('company', 'user'))}}" class="nav-link">{{$user -> full_name}}</a></div>

								<div class="btn-group col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
									<a href="{{route('company.user.update', compact('company', 'user'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
								<a href="{{route('company.user.delete', compact('company', 'user'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
								</div>
							</div>
					</span>
				@endforeach
			</div>

		</div>
	</div>

@endsection
