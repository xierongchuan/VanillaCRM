@extends('layouts.main')

@section('title', 'Должность '.$post -> name)

@section('content')

	<div class="row flex-column align-items-center">
		<div class="col-lg-7 bg-body-secondary rounded mt-3 p-2">

			<div class="d-flex justify-content-between mx-2">
				<h2><b>{{$post -> name}} в {{$company -> name}}</b></h2>

				<div class="btn-group p-0 mb-2" role="group" aria-label="Basic mixed styles example">
					<a href="{{route('company.department.post.update', compact('company', 'department', 'post'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
					<a href="{{route('company.department.post.delete', compact('company', 'department', 'post'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
				</div>
			</div>

			<div class="p-2 border rounded mt-2">
				<span class="lead">Сотрудники: </span><br>
				@foreach($post -> users as $user)
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
