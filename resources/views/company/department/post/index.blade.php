@extends('layouts.main')

@section('title', 'Post '.$post -> name)

@section('content')

	<div class="row flex-column align-items-center">
		<div class="col-md-6 bg-body-secondary rounded mt-3 p-2">
			<h2><b>{{$post -> name}} in {{$company -> name}}</b></h2>

			<div class="btn-group col-lg-3 p-0 mb-2" role="group" aria-label="Basic mixed styles example">
				<a href="{{route('company.department.post.update', compact('company', 'department', 'post'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
				<a href="{{route('company.department.post.delete', compact('company', 'department', 'post'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
			</div>

			<div class="p-2 border rounded mt-2">
				<span class="lead">Workers: </span><br>
				@foreach($post -> workers as $worker)
					<span>
						<div class="my-1 p-2 rounded bg-body shadow row justify-content-between gx-1">
							<div class="col-lg-9 lead"><a href="{{route('company.department.worker.update', compact('company', 'department', 'worker'))}}" class="nav-link">{{$worker -> name}}</a></div>

							<div class="btn-group col-lg-3 p-0" role="group" aria-label="Basic mixed styles example">
								<a href="{{route('company.department.worker.update', compact('company', 'department', 'worker'))}}" type="button" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
								<a href="{{route('company.department.worker.delete', compact('company', 'department', 'worker'))}}" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></a>
							</div>
						</div>
					</span>
				@endforeach

				<a href="" class="btn btn-success w-100 mt-1">Create <i class="bi bi-person"></i></a>
			</div>

		</div>
	</div>

@endsection
